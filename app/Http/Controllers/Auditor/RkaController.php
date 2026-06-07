<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\RingkasanKondisiAudit;
use App\Models\UptItemSubStandarMutu;
use App\Notifications\PenugasanAuditNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class RkaController extends Controller
{
    public function index(): View
    {
        $auditor = $this->getAuditor();

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->latest()
            ->get()
            ->map(function (Penugasan $penugasan) use ($auditor) {
                $progress = $this->getAuditProgress($penugasan);

                $penugasan->setAttribute('total_item', $progress['total_item']);
                $penugasan->setAttribute('item_terjawab', $progress['item_terjawab']);
                $penugasan->setAttribute('persentase', $progress['persentase']);
                $penugasan->setAttribute('penilaian_selesai', $progress['selesai']);
                $penugasan->setAttribute('is_ketua_auditor', $penugasan->auditor_id_1 === $auditor->auditor_id);

                return $penugasan;
            });

        return view('auditor.rka.index', compact('penugasan'));
    }

    public function show(string $penugasanId): View
    {
        $auditor = $this->getAuditor();
        $penugasan = $this->getPenugasanAuditor($penugasanId);
        $isKetuaAuditor = $penugasan->auditor_id_1 === $auditor->auditor_id;
        $progress = $this->getAuditProgress($penugasan);

        abort_unless($progress['selesai'], 403, 'Draft RKA baru bisa disusun setelah seluruh item dinilai.');

        if ($isKetuaAuditor) {
            $rka = $this->getOrCreateDraftRka($penugasan);
            $this->sinkronkanTemuanDraft($rka, $penugasan);
        } else {
            $rka = $penugasan->rka;
            abort_unless($rka, 403, 'Draft RKA belum disusun oleh ketua auditor.');
        }

        $rka->load([
            'temuan.jawabanAudit.itemSubStandar.parent.parent.parent',
            'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
            'finalizedBy',
        ]);

        $ringkasan = $this->getRingkasan($progress['item_ids'], $rka);
        $temuanPerStandar = $this->getTemuanPerStandar($rka);

        return view('auditor.rka.show', compact('penugasan', 'rka', 'ringkasan', 'temuanPerStandar', 'isKetuaAuditor'));
    }

    public function update(Request $request, string $rkaId): RedirectResponse
    {
        $rka = RingkasanKondisiAudit::with(['penugasan', 'temuan'])
            ->where('rka_id', $rkaId)
            ->firstOrFail();

        $this->getPenugasanKetuaAuditor($rka->penugasan_id);

        $validated = $request->validate([
            'tanggal_rapat' => 'nullable|date',
            'temuan' => 'array',
            'temuan.*.kondisi_final' => 'required|string|max:10000',
            'temuan.*.kategori_final' => 'required|in:KTS,OB',
            'aksi' => 'required|in:simpan,finalisasi',
        ]);

        $rka->update([
            'tanggal_rapat' => $validated['tanggal_rapat'] ?? null,
            'ringkasan_umum' => null,
            'catatan_rapat' => null,
            'status' => $validated['aksi'] === 'finalisasi' ? 'final' : 'draft',
            'finalized_by_user_id' => $validated['aksi'] === 'finalisasi' ? Auth::id() : null,
            'finalized_at' => $validated['aksi'] === 'finalisasi' ? now() : null,
        ]);

        foreach ($validated['temuan'] ?? [] as $temuanId => $data) {
            $rka->temuan()
                ->where('rka_temuan_id', $temuanId)
                ->update([
                    'kondisi_final' => $data['kondisi_final'],
                    'kategori_final' => $data['kategori_final'],
                    'rekomendasi' => null,
                ]);
        }

        if ($validated['aksi'] === 'finalisasi') {
            $this->kirimNotifikasiRkaFinal($rka->fresh('penugasan.upt'));
        }

        return back()->with('success', $validated['aksi'] === 'finalisasi'
            ? 'RKA berhasil difinalisasi dan dikirim ke auditee.'
            : 'Draft RKA berhasil disimpan.');
    }

    public function export(string $rkaId): Response
    {
        $rka = RingkasanKondisiAudit::with([
            'penugasan.upt',
            'penugasan.periode',
            'penugasan.auditor1',
            'penugasan.auditor2',
            'temuan.jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
        ])
            ->where('rka_id', $rkaId)
            ->firstOrFail();

        $penugasan = $this->getPenugasanAuditor($rka->penugasan_id);
        $upt = $penugasan->upt;
        $periode = $penugasan->periode;
        $namaFile = 'RKA-' . Str::slug($upt?->nama_upt ?? 'unit') . '-' . ($periode?->tahun ?? 'periode') . '.pdf';

        return Pdf::loadView('auditor.export.pdf.rka', compact('rka', 'upt', 'periode', 'penugasan'))
            ->setPaper('a4', 'portrait')
            ->stream($namaFile);
    }

    private function getAuditor(): Auditor
    {
        return Auditor::where('user_id', Auth::id())->firstOrFail();
    }

    private function getPenugasanAuditor(string $penugasanId): Penugasan
    {
        $auditor = $this->getAuditor();

        return Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->where('penugasan_id', $penugasanId)
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->firstOrFail();
    }

    private function getPenugasanKetuaAuditor(string $penugasanId): Penugasan
    {
        $auditor = $this->getAuditor();

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2', 'rka'])
            ->where('penugasan_id', $penugasanId)
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->firstOrFail();

        abort_unless(
            $penugasan->auditor_id_1 === $auditor->auditor_id,
            403,
            'Penyusunan RKA hanya dapat dilakukan oleh ketua auditor.'
        );

        return $penugasan;
    }

    private function getOrCreateDraftRka(Penugasan $penugasan): RingkasanKondisiAudit
    {
        return RingkasanKondisiAudit::firstOrCreate(
            ['penugasan_id' => $penugasan->penugasan_id],
            [
                'rka_id' => Str::uuid()->toString(),
                'status' => 'draft',
                'created_by_user_id' => Auth::id(),
            ]
        );
    }

    private function sinkronkanTemuanDraft(RingkasanKondisiAudit $rka, Penugasan $penugasan): void
    {
        if ($rka->status === 'final') {
            return;
        }

        $temuan = $this->getTemuanAudit($penugasan);
        $jawabanIds = $temuan->pluck('id');

        $rka->temuan()
            ->whereNotIn('jawaban_audit_id', $jawabanIds)
            ->delete();

        foreach ($temuan as $index => $jawaban) {
            $rka->temuan()->firstOrCreate(
                ['jawaban_audit_id' => $jawaban->id],
                [
                    'rka_temuan_id' => Str::uuid()->toString(),
                    'kondisi_final' => $jawaban->catatan ?: 'Kondisi perlu dirumuskan dalam rapat tim auditor.',
                    'kategori_final' => $jawaban->kategori_temuan ?: 'OB',
                    'rekomendasi' => null,
                    'urutan' => $index + 1,
                ]
            );
        }
    }

    private function getTemuanAudit(Penugasan $penugasan): Collection
    {
        return JawabanAudit::with('itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu')
            ->whereIn('upt_item_sub_standar_id', $this->getItemIds($penugasan))
            ->where('jawaban', 0)
            ->get()
            ->sortBy(fn ($jawaban) => sprintf(
                '%05d-%05d-%05d',
                $jawaban->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->urutan ?? 0,
                $jawaban->itemSubStandar?->uptSubStandar?->urutan ?? 0,
                $jawaban->itemSubStandar?->urutan ?? 0
            ))
            ->values();
    }

    private function getTemuanPerStandar(RingkasanKondisiAudit $rka): Collection
    {
        return $rka->temuan
            ->sortBy(function ($temuan) {
                $item = $temuan->jawabanAudit?->itemSubStandar;
                $standar = $item?->uptSubStandar?->uptStandarMutu?->standar_mutu;

                return sprintf(
                    '%05d-%05d-%05d',
                    $standar?->urutan ?? 0,
                    $item?->uptSubStandar?->urutan ?? 0,
                    $item?->urutan ?? 0
                );
            })
            ->groupBy(fn ($temuan) => $temuan->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->standar_mutu_id ?? 'tanpa-standar')
            ->map(function ($temuan) {
                $standar = $temuan->first()?->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu;

                return [
                    'nama_standar' => $standar?->nama_standar_mutu ?? '-',
                    'temuan' => $temuan
                        ->values()
                        ->map(function ($itemTemuan) {
                            $item = $itemTemuan->jawabanAudit?->itemSubStandar;
                            $itemTemuan->setAttribute('item_path', $this->getItemPath($item));

                            return $itemTemuan;
                        }),
                ];
            })
            ->values();
    }

    private function getItemPath(?UptItemSubStandarMutu $item): Collection
    {
        if (!$item) {
            return collect();
        }

        $path = collect();
        $current = $item;
        $guard = 0;

        while ($current && $guard < 10) {
            $path->prepend($current);
            $current = $current->parent;
            $guard++;
        }

        return $path->values();
    }

    private function getAuditProgress(Penugasan $penugasan): array
    {
        $itemIds = $this->getItemIds($penugasan);
        $totalItem = $itemIds->count();
        $itemTerjawab = JawabanAudit::whereIn('upt_item_sub_standar_id', $itemIds)
            ->distinct()
            ->count('upt_item_sub_standar_id');

        return [
            'item_ids' => $itemIds,
            'total_item' => $totalItem,
            'item_terjawab' => $itemTerjawab,
            'persentase' => $totalItem > 0 ? (int) round(($itemTerjawab / $totalItem) * 100) : 0,
            'selesai' => $totalItem > 0 && $itemTerjawab >= $totalItem,
        ];
    }

    private function getItemIds(Penugasan $penugasan): Collection
    {
        return UptItemSubStandarMutu::whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($penugasan) {
            $query->where('upt_id', $penugasan->upt_id)
                ->where('periode_id', $penugasan->periode_id);
        })->pluck('upt_item_sub_standar_id');
    }

    private function getRingkasan(Collection $itemIds, RingkasanKondisiAudit $rka): array
    {
        $jawabanAudit = JawabanAudit::whereIn('upt_item_sub_standar_id', $itemIds)
            ->get()
            ->unique('upt_item_sub_standar_id');

        return [
            'total_item' => $itemIds->count(),
            'sesuai' => $jawabanAudit->filter(fn ($jawaban) => (int) $jawaban->jawaban === 1)->count(),
            'temuan' => $rka->temuan->count(),
            'kts' => $rka->temuan->where('kategori_final', 'KTS')->count(),
            'ob' => $rka->temuan->where('kategori_final', 'OB')->count(),
        ];
    }

    private function kirimNotifikasiRkaFinal(RingkasanKondisiAudit $rka): void
    {
        $penugasan = $rka->penugasan;
        $namaUpt = $penugasan->upt?->nama_upt ?? 'UPT';
        $pesan = "RKA final untuk {$namaUpt} sudah tersedia setelah rapat internal tim auditor.";
        $url = route('auditee.rka.show', $penugasan->penugasan_id);

        Auditee::with('user')
            ->where('upt_id', $penugasan->upt_id)
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->each(function ($user) use ($penugasan, $pesan, $url) {
                $sudahDikirim = $user->notifications()
                    ->where('type', PenugasanAuditNotification::class)
                    ->get()
                    ->contains(fn ($notifikasi) => ($notifikasi->data['jenis'] ?? null) === 'rka-final'
                        && ($notifikasi->data['penugasan_id'] ?? null) === $penugasan->penugasan_id);

                if ($sudahDikirim) {
                    return;
                }

                $user->notify(new PenugasanAuditNotification(
                    $penugasan,
                    'RKA Final Tersedia',
                    $pesan,
                    $url,
                    'rka-final'
                ));
            });
    }
}
