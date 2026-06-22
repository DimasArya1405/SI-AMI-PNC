<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Dosen;
use App\Models\ItemBuktiDosen;
use App\Models\JawabanAMI;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\Prodi;
use App\Models\TindakanKoreksi;
use App\Models\TindakanKoreksiDokumenDosen;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::where('user_id', Auth::id())->first();
        $periodeNow = Periode::where('status', '1')->first();

        $upt = $dosen?->upt_id ? UPT::find($dosen->upt_id) : null;
        $auditee = $upt ? Auditee::with('upt')->where('upt_id', $upt->upt_id)->first() : null;

        return view('dosen.dashboard', [
            'dosen'      => $dosen,
            'upt'        => $upt,
            'auditee'    => $auditee,
            'periode_now' => $periodeNow,
            'nama_unit'  => $upt?->nama_upt ?? '-',
        ]);
    }

    public function dokumen()
    {
        $context = $this->getDosenAuditContext();

        $pemetaanStandar = collect();
        $uptSubStandar = collect();
        $uptItemSubStandar = collect();
        $buktiDukung = collect();
        $assignedItemIds = collect();

        if ($context['upt'] && $context['periode_now'] && $context['penugasan']) {
            $assignedItemIds = ItemBuktiDosen::where('penugasan_id', $context['penugasan']->penugasan_id)
                ->pluck('upt_item_sub_standar_id');

            $pemetaanStandar = UptStandarMutu::with('standar_mutu')
                ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
                ->where('upt_standar_mutu.upt_id', $context['upt']->upt_id)
                ->where('upt_standar_mutu.periode_id', $context['periode_now']->id)
                ->orderBy('standar_mutu.urutan', 'asc')
                ->select('upt_standar_mutu.*')
                ->get();

            $uptStandarIds = $pemetaanStandar->pluck('upt_standar_mutu_id');

            if ($assignedItemIds->isNotEmpty()) {
                $assignedSubStandarIds = UptItemSubStandarMutu::whereIn('upt_item_sub_standar_id', $assignedItemIds)
                    ->pluck('upt_sub_standar_id')
                    ->unique();

                $assignedUptStandarIds = UptSubStandarMutu::whereIn('upt_sub_standar_id', $assignedSubStandarIds)
                    ->pluck('upt_standar_mutu_id')
                    ->unique();

                $pemetaanStandar = $pemetaanStandar
                    ->whereIn('upt_standar_mutu_id', $assignedUptStandarIds)
                    ->values();

                $uptSubStandar = UptSubStandarMutu::with('uptStandarMutu.standar_mutu')
                    ->whereIn('upt_standar_mutu_id', $assignedUptStandarIds)
                    ->whereIn('upt_sub_standar_id', $assignedSubStandarIds)
                    ->orderBy('urutan', 'asc')
                    ->get();

                $uptSubStandarIds = $uptSubStandar->pluck('upt_sub_standar_id');

                $uptItemSubStandar = UptItemSubStandarMutu::whereIn('upt_sub_standar_id', $uptSubStandarIds)
                    ->whereIn('upt_item_sub_standar_id', $assignedItemIds)
                    ->orderBy('urutan', 'asc')
                    ->get()
                    ->groupBy('upt_sub_standar_id');
            }

            $buktiDukung = JawabanAMI::with('dosen')
                ->where('penugasan_id', $context['penugasan']->penugasan_id)
                ->where('dosen_id', $context['dosen']->dosen_id)
                ->get()
                ->groupBy('upt_item_sub_standar_id');
        }

        return view('dosen.bukti-dukung', array_merge($context, compact(
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar',
            'buktiDukung',
            'assignedItemIds'
        )));
    }

    public function uploadDokumen(Request $request)
    {
        $validated = $request->validate([
            'upt_item_sub_standar_id' => 'required|exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
            'file_bukti' => 'required|array',
            'file_bukti.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string|max:1000',
            'active_tab' => 'nullable|string',
            'open_accordion' => 'nullable|string',
            'target_scroll' => 'nullable|string',
        ], [
            'file_bukti.required' => 'File bukti wajib diupload.',
            'file_bukti.*.mimes' => 'Tipe file tidak didukung. Gunakan PDF, Word, Excel, JPG, JPEG, atau PNG.',
            'file_bukti.*.max' => 'Ukuran salah satu file terlalu besar. Maksimal 5 MB per file.',
        ]);

        $context = $this->getDosenAuditContext();

        if (!$context['periode_now'] || !$context['penugasan'] || !$context['upt']) {
            return back()->with('error', 'Belum ada periode aktif atau penugasan AMI untuk prodi Anda.');
        }

        if ($this->isRkaFinal($context['penugasan'])) {
            return back()->with('error', 'RKA sudah difinalisasi. Upload dokumen AMI tidak dapat dilakukan lagi.');
        }

        $itemDitugaskan = ItemBuktiDosen::where('penugasan_id', $context['penugasan']->penugasan_id)
            ->where('upt_item_sub_standar_id', $validated['upt_item_sub_standar_id'])
            ->exists();

        if (!$itemDitugaskan) {
            return back()->with('error', 'Item ini belum dibuka oleh auditee untuk upload dokumen dosen.');
        }

        $item = UptItemSubStandarMutu::with('uptSubStandar.uptStandarMutu.standar_mutu')
            ->where('upt_item_sub_standar_id', $validated['upt_item_sub_standar_id'])
            ->whereHas('uptSubStandar.uptStandarMutu', function ($query) use ($context) {
                $query->where('upt_id', $context['upt']->upt_id)
                    ->where('periode_id', $context['periode_now']->id);
            })
            ->firstOrFail();

        $namaStandar = optional($item->uptSubStandar?->uptStandarMutu?->standar_mutu)->nama_standar_mutu ?? 'standar';
        $folder = 'bukti-dukung-dosen/upt-' . Str::slug($context['upt']->nama_upt)
            . '/periode-' . $context['periode_now']->tahun
            . '/dosen-' . Str::slug($context['dosen']->nama_lengkap)
            . '/' . Str::slug($namaStandar);

        foreach ($request->file('file_bukti') as $file) {
            $namaAsli = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $path = $folder . '/' . Str::slug($namaAsli) . '-' . Str::random(5) . '.' . $extension;

            Storage::disk('google')->put($path, file_get_contents($file->getRealPath()));

            JawabanAMI::create([
                'jawaban_id' => Str::uuid()->toString(),
                'upt_item_sub_standar_id' => $item->upt_item_sub_standar_id,
                'penugasan_id' => $context['penugasan']->penugasan_id,
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'keterangan' => $validated['keterangan'] ?? null,
                'sumber' => 'dosen',
                'uploaded_by_user_id' => Auth::id(),
                'dosen_id' => $context['dosen']->dosen_id,
                'status_validasi' => 'menunggu',
            ]);
        }

        return redirect()
            ->to(url()->previous() . '#item-' . $item->upt_item_sub_standar_id)
            ->with([
                'success' => 'Dokumen berhasil diupload dan menunggu validasi auditee.',
                'active_tab' => $request->active_tab,
                'open_accordion' => $request->open_accordion,
                'target_scroll' => $request->target_scroll,
            ]);
    }

    public function hapusDokumen(Request $request, $id)
    {
        $context = $this->getDosenAuditContext();

        $dokumen = JawabanAMI::with('penugasan.rka')
            ->where('jawaban_id', $id)
            ->where('dosen_id', $context['dosen']->dosen_id)
            ->firstOrFail();

        if ($dokumen->status_validasi === 'diterima') {
            return back()->with('error', 'Dokumen yang sudah diterima auditee tidak dapat dihapus oleh dosen.');
        }

        $itemId = $dokumen->upt_item_sub_standar_id;

        if ($this->isRkaFinal($dokumen->penugasan)) {
            return redirect()
                ->to(url()->previous() . '#item-' . $itemId)
                ->with([
                    'error' => 'RKA sudah difinalisasi. Dokumen AMI tidak dapat dihapus lagi.',
                    'active_tab' => $request->active_tab,
                    'open_accordion' => $request->open_accordion,
                    'target_scroll' => $request->target_scroll,
                ]);
        }

        if ($dokumen->file_path && Storage::disk('google')->exists($dokumen->file_path)) {
            Storage::disk('google')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return redirect()
            ->to(url()->previous() . '#item-' . $itemId)
            ->with([
                'success' => 'Dokumen berhasil dihapus.',
                'active_tab' => $request->active_tab,
                'open_accordion' => $request->open_accordion,
                'target_scroll' => $request->target_scroll,
            ]);
    }

    public function previewDokumen($id)
    {
        $dokumen = $this->findDokumenDosen($id);

        if (!Storage::disk('google')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $file = Storage::disk('google')->get($dokumen->file_path);
        $mimeType = Storage::disk('google')->mimeType($dokumen->file_path) ?? 'application/octet-stream';
        $namaFile = str_replace('"', '', $dokumen->nama_file);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    public function downloadDokumen($id)
    {
        $dokumen = $this->findDokumenDosen($id);

        if (!Storage::disk('google')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan di Google Drive.');
        }

        return response(Storage::disk('google')->get($dokumen->file_path), 200)
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-Disposition', 'attachment; filename="' . $dokumen->nama_file . '"');
    }

    public function dokumenTindakanKoreksi()
    {
        $context = $this->getDosenAuditContext();
        $tindakanKoreksi = collect();
        $dokumenSaya = collect();

        if ($context['penugasan']) {
            $tindakanKoreksi = TindakanKoreksi::with([
                'jawabanAudit.itemSubStandar.uptSubStandar.uptStandarMutu.standar_mutu',
                'kebutuhanDokumenDosen',
                'dokumenDosen' => fn($query) => $query
                    ->where('dosen_id', $context['dosen']->dosen_id)
                    ->latest(),
            ])
                ->where('penugasan_id', $context['penugasan']->penugasan_id)
                ->whereHas('kebutuhanDokumenDosen')
                ->get()
                ->sortBy(fn($tk) => sprintf(
                    '%05d-%05d-%05d',
                    $tk->jawabanAudit?->itemSubStandar?->uptSubStandar?->uptStandarMutu?->standar_mutu?->urutan ?? 0,
                    $tk->jawabanAudit?->itemSubStandar?->uptSubStandar?->urutan ?? 0,
                    $tk->jawabanAudit?->itemSubStandar?->urutan ?? 0
                ))
                ->values();

            $dokumenSaya = TindakanKoreksiDokumenDosen::where('dosen_id', $context['dosen']->dosen_id)
                ->whereIn('tindakan_koreksi_id', $tindakanKoreksi->pluck('tindakan_koreksi_id'))
                ->latest()
                ->get()
                ->groupBy('tindakan_koreksi_id');
        }

        return view('dosen.tindakan-koreksi-dokumen', array_merge($context, compact('tindakanKoreksi', 'dokumenSaya')));
    }

    public function uploadDokumenTindakanKoreksi(Request $request, string $tindakanKoreksiId)
    {
        $validated = $request->validate([
            'file_bukti' => 'required|array',
            'file_bukti.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'file_bukti.required' => 'File bukti wajib diupload.',
            'file_bukti.*.mimes' => 'Tipe file tidak didukung. Gunakan PDF, Word, Excel, JPG, JPEG, atau PNG.',
            'file_bukti.*.max' => 'Ukuran salah satu file terlalu besar. Maksimal 5 MB per file.',
        ]);

        $context = $this->getDosenAuditContext();

        if (!$context['penugasan']) {
            return back()->with('error', 'Belum ada penugasan AMI aktif untuk prodi Anda.');
        }

        $tindakanKoreksi = TindakanKoreksi::with('kebutuhanDokumenDosen')
            ->where('tindakan_koreksi_id', $tindakanKoreksiId)
            ->where('penugasan_id', $context['penugasan']->penugasan_id)
            ->firstOrFail();

        abort_unless($tindakanKoreksi->kebutuhanDokumenDosen, 403, 'Tindakan koreksi ini belum dibuka oleh auditee untuk upload dokumen dosen.');

        if ($this->isTindakanKoreksiVerified($tindakanKoreksi)) {
            return back()->with('error', 'Tindakan koreksi sudah diverifikasi P4MP. Upload dokumen tidak dapat dilakukan lagi.');
        }

        $folder = 'dokumen-tk-dosen/upt-' . Str::slug($context['upt']?->nama_upt ?? 'upt')
            . '/periode-' . Str::slug((string) ($context['periode_now']?->tahun ?? 'periode'))
            . '/dosen-' . Str::slug($context['dosen']->nama_lengkap);

        foreach ($request->file('file_bukti') as $file) {
            $namaAsli = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $path = $folder . '/' . Str::slug($namaAsli) . '-' . Str::random(5) . '.' . $extension;

            Storage::disk('local')->put($path, file_get_contents($file->getRealPath()));

            TindakanKoreksiDokumenDosen::create([
                'dokumen_tk_dosen_id' => Str::uuid()->toString(),
                'tindakan_koreksi_id' => $tindakanKoreksi->tindakan_koreksi_id,
                'dosen_id' => $context['dosen']->dosen_id,
                'uploaded_by_user_id' => Auth::id(),
                'nama_file' => $file->getClientOriginalName(),
                'file_path' => $path,
                'keterangan' => $validated['keterangan'] ?? null,
                'status_validasi' => 'menunggu',
            ]);
        }

        return redirect()
            ->to(url()->previous() . '#tk-' . $tindakanKoreksi->tindakan_koreksi_id)
            ->with('success', 'Dokumen tindakan koreksi berhasil diupload dan menunggu persetujuan auditee.');
    }

    public function hapusDokumenTindakanKoreksi(string $dokumenId)
    {
        $dokumen = $this->findDokumenTindakanKoreksiDosen($dokumenId);

        if ($dokumen->status_validasi === 'diterima') {
            return back()->with('error', 'Dokumen yang sudah diterima auditee tidak dapat dihapus.');
        }

        if ($this->isTindakanKoreksiVerified($dokumen->tindakanKoreksi)) {
            return back()->with('error', 'Tindakan koreksi sudah diverifikasi P4MP. Dokumen tidak dapat dihapus lagi.');
        }

        if ($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path)) {
            Storage::disk('local')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return back()->with('success', 'Dokumen tindakan koreksi berhasil dihapus.');
    }

    public function previewDokumenTindakanKoreksi(string $dokumenId)
    {
        $dokumen = $this->findDokumenTindakanKoreksiDosen($dokumenId);

        abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

        $namaFile = str_replace('"', '', $dokumen->nama_file);

        return response(Storage::disk('local')->get($dokumen->file_path), 200)
            ->header('Content-Type', Storage::disk('local')->mimeType($dokumen->file_path) ?? 'application/octet-stream')
            ->header('Content-Disposition', 'inline; filename="' . $namaFile . '"');
    }

    public function downloadDokumenTindakanKoreksi(string $dokumenId)
    {
        $dokumen = $this->findDokumenTindakanKoreksiDosen($dokumenId);

        abort_unless($dokumen->file_path && Storage::disk('local')->exists($dokumen->file_path), 404);

        return Storage::disk('local')->download($dokumen->file_path, $dokumen->nama_file);
    }

    private function getDosenAuditContext(): array
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();
        $periodeNow = Periode::where('status', '1')->first();

        $upt = $dosen->upt_id ? UPT::find($dosen->upt_id) : null;

        $auditee = $upt ? Auditee::with('upt')->where('upt_id', $upt->upt_id)->first() : null;

        $penugasan = ($upt && $periodeNow)
            ? Penugasan::where('upt_id', $upt->upt_id)
            ->where('periode_id', $periodeNow->id)
            ->with('rka')
            ->first()
            : null;

        return [
            'dosen'      => $dosen,
            'upt'        => $upt,
            'auditee'    => $auditee,
            'periode_now' => $periodeNow,
            'penugasan'  => $penugasan,
            'nama_unit'  => $upt?->nama_upt ?? '-',
        ];
    }

    private function findDokumenDosen(string $id): JawabanAMI
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        return JawabanAMI::with('penugasan.rka')
            ->where('jawaban_id', $id)
            ->where('dosen_id', $dosen->dosen_id)
            ->firstOrFail();
    }

    private function findDokumenTindakanKoreksiDosen(string $id): TindakanKoreksiDokumenDosen
    {
        $dosen = Dosen::where('user_id', Auth::id())->firstOrFail();

        return TindakanKoreksiDokumenDosen::with('tindakanKoreksi')
            ->where('dokumen_tk_dosen_id', $id)
            ->where('dosen_id', $dosen->dosen_id)
            ->firstOrFail();
    }

    private function isRkaFinal(?Penugasan $penugasan): bool
    {
        if (!$penugasan) {
            return false;
        }

        $rka = $penugasan->relationLoaded('rka') ? $penugasan->rka : $penugasan->rka()->first();

        return $rka && ($rka->status === 'final' || filled($rka->finalized_at));
    }

    private function isTindakanKoreksiVerified(?TindakanKoreksi $tindakanKoreksi): bool
    {
        return $tindakanKoreksi
            && ($tindakanKoreksi->p4mp_status === 'terverifikasi' || filled($tindakanKoreksi->p4mp_verified_at));
    }
}
