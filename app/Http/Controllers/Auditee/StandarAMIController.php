<?php

namespace App\Http\Controllers\Auditee;

use App\DataTables\Auditee\StandarAMIDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Dokumen;
use App\Models\Periode;
use App\Models\UPT;
use App\Models\UptItemSubStandarMutu;
use App\Models\UptStandarMutu;
use App\Models\UptSubStandarMutu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StandarAMIController extends Controller
{
    public function index(StandarAMIDataTable $dataTable)
    {
        return $dataTable->render('auditee.standar-ami');
    }

    public function detail($upt_id, $periode_id)
    {
        $user = Auth::user();

        $auditee = Auditee::where('user_id', $user->id)->firstOrFail();

        if ($auditee->upt_id !== $upt_id) {
            abort(403, 'Anda tidak memiliki akses ke pemetaan ini.');
        }

        $upt = UPT::findOrFail($upt_id);
        $periode = Periode::findOrFail($periode_id);

        $pemetaanStandar = UptStandarMutu::with('standar_mutu')
            ->join('standar_mutu', 'upt_standar_mutu.standar_mutu_id', '=', 'standar_mutu.standar_mutu_id')
            ->where('upt_standar_mutu.upt_id', $upt_id)
            ->where('upt_standar_mutu.periode_id', $periode_id)
            ->orderBy('standar_mutu.urutan', 'asc')
            ->select('upt_standar_mutu.*')
            ->get();

        $uptSubStandar = UptSubStandarMutu::with('standar_mutu')
            ->where('upt_id', $upt_id)
            ->where('periode_id', $periode_id)
            ->orderBy('urutan', 'asc')
            ->get();

        $uptItemSubStandar = UptItemSubStandarMutu::where('upt_id', $upt_id)
            ->where('periode_id', $periode_id)
            ->orderBy('urutan', 'asc')
            ->get()
            ->groupBy('upt_sub_standar_id');

        $buktiDukung = Dokumen::where('auditee_id', $auditee->auditee_id)
            ->get()
            ->groupBy('upt_item_sub_standar_id');

        return view('auditee.standar-ami-detail', compact(
            'upt',
            'periode',
            'pemetaanStandar',
            'uptSubStandar',
            'uptItemSubStandar',
            'buktiDukung'
        ));
    }

    public function uploadBukti(Request $request)
    {
        $validated = $request->validate([
            'upt_item_sub_standar_id' => 'required|exists:upt_item_sub_standar_mutu,upt_item_sub_standar_id',
            'periode_id' => 'required|exists:periode,id',
            'file_bukti' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'file_bukti.required' => 'File bukti wajib diupload.',
            'file_bukti.mimes' => 'Tipe file tidak didukung. Gunakan PDF, Word, Excel, JPG, JPEG, atau PNG.',
            'file_bukti.max' => 'Ukuran file terlalu besar. Maksimal 5 MB.',
        ]);

        $auditee = Auditee::with('upt')
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $item = UptItemSubStandarMutu::where('upt_item_sub_standar_id', $validated['upt_item_sub_standar_id'])
            ->where('upt_id', $auditee->upt_id)
            ->where('periode_id', $validated['periode_id'])
            ->firstOrFail();

        $file = $request->file('file_bukti');

        // ambil nama UPT & tahun periode
        $uptNama = Str::slug($auditee->upt->nama_upt ?? 'upt');

        $periode = Periode::findOrFail($validated['periode_id']);
        $periodeTahun = $periode->tahun;

        // folder otomatis
        $folder = 'bukti-dukung/upt-' . $uptNama . '/periode-' . $periodeTahun;

        // nama file aman
        $namaAsliTanpaExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $namaFileBaru = time() . '_' . Str::slug($namaAsliTanpaExt) . '.' . $extension;
        // $namaFileBaru = Str::random(40) . '.' . $extension;

        // simpan file
        $path = $file->storeAs($folder, $namaFileBaru, 'public');

        // simpan database
        Dokumen::create([
            'dokumen_id' => Str::uuid()->toString(),
            'upt_item_sub_standar_id' => $item->upt_item_sub_standar_id,
            'auditee_id' => $auditee->auditee_id,
            'nama_file' => $file->getClientOriginalName(),
            'file_path' => $path,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return back()->with('success', 'Bukti dukung berhasil diupload.');
    }

    public function hapusBukti($id)
    {
        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $dokumen = Dokumen::where('dokumen_id', $id)
            ->where('auditee_id', $auditee->auditee_id)
            ->firstOrFail();

        // hapus file dari storage
        if ($dokumen->file_path && Storage::disk('public')->exists($dokumen->file_path)) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        // hapus dari database
        $dokumen->delete();

        return back()->with('success', 'Bukti dukung berhasil dihapus.');
    }
}
