<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiTindakanKoreksi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'verifikasi_tindakan_koreksi';
    protected $primaryKey = 'verifikasi_tk_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'verifikasi_tk_id',
        'penugasan_id',
        'catatan_umum',
        'wadir1_nama',
        'finalized_by_user_id',
        'finalized_at',
    ];

    protected $casts = [
        'finalized_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id', 'penugasan_id');
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by_user_id', 'id');
    }
}
