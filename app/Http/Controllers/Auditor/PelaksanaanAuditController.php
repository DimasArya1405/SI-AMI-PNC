<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\Dokumen;
use App\Models\JawabanAudit;
use App\Models\Penugasan;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PelaksanaanAuditController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $auditor = Auditor::where('user_id', $user->id)->firstOrFail();
        $periode = Periode::where('status', '1')->first();
        $penugasan = Penugasan::where('periode_id', $periode->id)
            ->where('status_penugasan', 'aktif')
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->with('upt')
            ->get();

        return view('auditor.pelaksanaan_audit.index', compact('penugasan'));
    }
    public function detail($id)
    {
        $user = Auth::user();

        $auditor = Auditor::where('user_id', $user->id)->firstOrFail();
        $periode = Periode::where('status', '1')->first();
        $penugasan = Penugasan::where('periode_id', $periode->id)
            ->where('upt_id', $id) // Cari berdasarkan ID UPT yang dipassing
            ->where(function ($query) use ($auditor) {
                $query->where('auditor_id_1', $auditor->auditor_id)
                    ->orWhere('auditor_id_2', $auditor->auditor_id);
            })
            ->firstOrFail();
        $auditee = Auditee::where('upt_id', $id)->firstOrFail();

        // CEK AKETUA AUDITOR ATAU BUKAN
        if ($penugasan->auditor_id_1 == $auditor->auditor_id) {
            $ketua = 1;
        } else {
            $ketua = 0;
        }

        $upt = UPT::findOrFail($id);
        $status_periode = $periode->status == 0;

        $pemetaanStandar = UptStandarMutu::with('standar_mutu')
            ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
            ->where('upt_standar_mutu.upt_id', $id)
            ->where('upt_standar_mutu.periode_id', $periode->id)
            ->orderBy('standar_mutu.urutan', 'asc')
            ->select('upt_standar_mutu.*')
            ->get();

        $uptSubStandar = UptSubStandarMutu::with('standar_mutu')
            ->where('upt_id', $id)
            ->where('periode_id', $periode->id)
            ->orderBy('urutan', 'asc')
            ->get();

        $uptItemSubStandar = UptItemSubStandarMutu::where('upt_id', $id)
            ->where('periode_id', $periode->id)
            ->orderBy('urutan', 'asc')
            ->get()
            ->groupBy('upt_sub_standar_id');

        $buktiDukung = Dokumen::where('auditee_id', $auditee->auditee_id)
            ->get()
            ->groupBy('upt_item_sub_standar_id');

      $allItemIds = $uptItemSubStandar->flatten()->pluck('upt_item_sub_standar_id');
        // Ambil data jawaban berdasarkan ID item tersebut
        $jawabanAudit = JawabanAudit::whereIn('upt_item_sub_standar_id', $allItemIds)
            ->get()
            ->keyBy('upt_item_sub_standar_id');

        return view('auditor.pelaksanaan_audit.detail', compact(
            'upt',
            'periode',
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar',
            'buktiDukung',
            'status_periode',
            'auditee',
            'ketua',
            'jawabanAudit'
        ));
    }
public function penilaian(Request $request, $id)
{

    $nilaiJawaban = ($request->jawaban == 'Ya') ? 1 : 0;

    JawabanAudit::updateOrCreate(
        ['upt_item_sub_standar_id' => $id], 
        [
            'jawaban' => $nilaiJawaban,
            'catatan' => $request->catatan,
            'id' => (string) Str::uuid() 
        ]
    );

    return redirect()->back()->with([
        'success' => 'Penilaian berhasil disimpan!',
        'active_item' => $id, // ID item yang baru dinilai
    ]);
}
}
