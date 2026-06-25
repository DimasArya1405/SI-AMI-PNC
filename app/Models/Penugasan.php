<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penugasan extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $table = 'penugasan';
    protected $primaryKey = 'penugasan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'penugasan_id',
        'periode_id',
        'upt_id',
        'auditor_id_1',
        'auditor_id_2',
        'auditee_id',
        'tanggal_audit',
        'jam',
        'status_penugasan',
        'acc_kepala_p4mp',
        'acc_kepala_p4mp_at',
        'acc_kepala_p4mp_by_user_id'
    ];

    protected $casts = [
        'tanggal_audit' => 'date',
        'acc_kepala_p4mp_at' => 'datetime',
    ];

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id', 'id');
    }

    public function auditor1()
    {
        return $this->belongsTo(Auditor::class, 'auditor_id_1');
    }

    public function auditor2()
    {
        return $this->belongsTo(Auditor::class, 'auditor_id_2');
    }

    public function auditee()
    {
        return $this->belongsTo(Auditee::class, 'auditee_id', 'auditee_id');
    }

    public function upt()
    {
        return $this->belongsTo(UPT::class, 'upt_id', 'upt_id');
    }

    public function pengajuan_jadwal_audit()
    {
        return $this->hasMany(PengajuanJadwalAudit::class, 'penugasan_id', 'penugasan_id');
    }

    public function tindakanKoreksi()
    {
        return $this->hasMany(TindakanKoreksi::class, 'penugasan_id', 'penugasan_id');
    }

    public function rka()
    {
        return $this->hasOne(RingkasanKondisiAudit::class, 'penugasan_id', 'penugasan_id');
    }

    public function verifikasiTindakanKoreksi()
    {
        return $this->hasOne(VerifikasiTindakanKoreksi::class, 'penugasan_id', 'penugasan_id');
    }
}
