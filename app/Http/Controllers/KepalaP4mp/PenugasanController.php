<?php

namespace App\Http\Controllers\KepalaP4mp;

use App\Http\Controllers\Concerns\PeriodeFilterSupport;
use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Milon\Barcode\Facades\DNS2DFacade;
use Symfony\Component\HttpFoundation\Response;

class PenugasanController extends Controller
{
    use PeriodeFilterSupport;

    public function index(Request $request): View
    {
        $periodeFilter = $this->getPeriodeFilterContext($request);
        $selectedPeriode = $periodeFilter['selectedPeriode'];

        $penugasan = Penugasan::with(['periode', 'upt', 'auditor1', 'auditor2'])
            ->when($selectedPeriode?->id, fn ($query) => $query->where('periode_id', $selectedPeriode->id))
            ->orderBy('tanggal_audit')
            ->orderBy('jam')
            ->get();

        $sudahDitandatangani = $penugasan->isNotEmpty()
            && $penugasan->every(fn (Penugasan $item) => $item->acc_kepala_p4mp === '1');

        return view('kepala-p4mp.penugasan.index', array_merge(compact(
            'penugasan',
            'sudahDitandatangani'
        ), $periodeFilter));
    }

    public function tandaTangan(string $periodeId): RedirectResponse
    {
        $periode = Periode::findOrFail($periodeId);
        $jumlahPenugasan = Penugasan::where('periode_id', $periode->id)->count();

        if ($jumlahPenugasan === 0) {
            return back()->with('error', 'Belum ada penugasan pada periode ini.');
        }

        Penugasan::where('periode_id', $periode->id)->update([
            'acc_kepala_p4mp' => '1',
            'acc_kepala_p4mp_at' => now(),
            'acc_kepala_p4mp_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Penugasan berhasil ditandatangani Kepala P4MP.');
    }

    public function export(string $periodeId): Response
    {
        $periode = Periode::findOrFail($periodeId);

        $penugasan = Penugasan::with(['upt', 'auditor1', 'auditor2'])
            ->where('periode_id', $periode->id)
            ->orderBy('tanggal_audit')
            ->orderBy('jam')
            ->get();

        $uptProdi = $this->kelompokkanPenugasanPerUpt($penugasan, 'Prodi');
        $uptBagian = $this->kelompokkanPenugasanPerUpt($penugasan, 'Unit/Bagian');
        $tahun = $periode->tahun;
        $sudahDitandatangani = $penugasan->isNotEmpty()
            && $penugasan->every(fn (Penugasan $item) => $item->acc_kepala_p4mp === '1');
        $tanggalTtd = $penugasan->firstWhere('acc_kepala_p4mp', '1')
            ?->acc_kepala_p4mp_at
            ?->locale('id')
            ->translatedFormat('d F Y');
        $kepalaP4mp = User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->first()
            ?: Auth::user();
        $kepalaP4mpName = $kepalaP4mp?->name ?? 'Kepala P4MP';
        $penugasanQR = $sudahDitandatangani && $penugasan->first()
            ? $this->generateQrCode('penugasan_kepala||', $penugasan->first()->penugasan_id)
            : null;

        return Pdf::loadView('admin.export.pdf.penugasan', compact(
            'uptProdi',
            'uptBagian',
            'tahun',
            'kepalaP4mpName',
            'sudahDitandatangani',
            'tanggalTtd',
            'penugasanQR'
        ))
            ->setPaper('a4', 'portrait')
            ->stream('Jadwal-AMI-PNC-' . $tahun . '.pdf');
    }

    private function kelompokkanPenugasanPerUpt($penugasan, string $kategori)
    {
        return $penugasan
            ->filter(fn (Penugasan $item) => $item->upt?->kategori_upt === $kategori)
            ->map(function (Penugasan $item) {
                $upt = $item->upt;
                $upt->setRelation('penugasan', collect([$item]));

                return $upt;
            })
            ->values();
    }

    private function generateQrCode(string $prefix, string $registrasi): string
    {
        $encodedCode = base64_encode($prefix . $registrasi);
        $qrLink = route('ttdcode.show', ['ttdcode' => $encodedCode]);

        return 'data:image/png;base64,' . DNS2DFacade::getBarcodePNG($qrLink, 'QRCODE', 5, 5);
    }
}
