<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanKoreksi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tindakan_koreksi';
    protected $primaryKey = 'tindakan_koreksi_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tindakan_koreksi_id',
        'penugasan_id',
        'jawaban_audit_id',
        'analisis_ketidaksesuaian',
        'akar_penyebab',
        'rencana_koreksi',
        'penanggung_jawab',
        'target_selesai',
        'tanggal_selesai',
        'bukti_nama_file',
        'bukti_file_path',
        'bukti_uploaded_by_user_id',
        'bukti_uploaded_at',
        'pelaksanaan_deskripsi',
        'hasil_penilaian_auditor',
        'tanggal_penilaian_ulang',
        'p4mp_status',
        'p4mp_catatan',
        'p4mp_verified_by_user_id',
        'p4mp_verified_at',
        'wadir1_nama',
        'status',
        'catatan_auditor',
        'created_by_user_id',
        'verified_by_user_id',
        'verified_at',
        'auditee_signed_by_user_id',
        'auditee_signed_at',
    ];

    protected $casts = [
        'target_selesai' => 'date',
        'tanggal_selesai' => 'date',
        'bukti_uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
        'tanggal_penilaian_ulang' => 'date',
        'p4mp_verified_at' => 'datetime',
        'auditee_signed_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id', 'penugasan_id');
    }

    public function jawabanAudit()
    {
        return $this->belongsTo(JawabanAudit::class, 'jawaban_audit_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_user_id', 'id');
    }

    public function buktiUploadedBy()
    {
        return $this->belongsTo(User::class, 'bukti_uploaded_by_user_id', 'id');
    }

    public function p4mpVerifiedBy()
    {
        return $this->belongsTo(User::class, 'p4mp_verified_by_user_id', 'id');
    }

    public function auditeeSignedBy()
    {
        return $this->belongsTo(User::class, 'auditee_signed_by_user_id', 'id');
    }

    public function kebutuhanDokumenDosen()
    {
        return $this->hasOne(TindakanKoreksiDosen::class, 'tindakan_koreksi_id', 'tindakan_koreksi_id');
    }

    public function dokumenDosen()
    {
        return $this->hasMany(TindakanKoreksiDokumenDosen::class, 'tindakan_koreksi_id', 'tindakan_koreksi_id');
    }
}
