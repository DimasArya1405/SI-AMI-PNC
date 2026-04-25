<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPSTORM_META\map;

class PengajuanJadwalAudit extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $table = 'pengajuan_jadwal_audit';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'penugasan_id',
        'tanggal_audit',
        'jam',
        'alasan',
        'ketua_auditor',
        'anggota_auditor',
        'upt',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id', 'penugasan_id');
    }
    // Relasi untuk Ketua
    public function ketua()
    {
        // 'ketua_auditor' adalah nama kolom di tabel ini
        // 'auditor_id' adalah primary key di tabel Auditor
        return $this->belongsTo(Auditor::class, 'ketua_auditor', 'auditor_id');
    }
    public function auditor(){
        return $this->belongsTo(Auditor::class, 'id_pengaju', 'auditor_id');
    }

    // Relasi untuk Anggota
    public function anggota()
    {
        // 'anggota_auditor' adalah nama kolom di tabel ini
        return $this->belongsTo(Auditor::class, 'anggota_auditor', 'auditor_id');
    }
    public function upt()
    {
        return $this->belongsTo(UPT::class, 'upt', 'upt_id');
    }
}
