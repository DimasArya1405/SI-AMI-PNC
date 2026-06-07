<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RingkasanKondisiAudit extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ringkasan_kondisi_audit';
    protected $primaryKey = 'rka_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rka_id',
        'penugasan_id',
        'tanggal_rapat',
        'ringkasan_umum',
        'catatan_rapat',
        'status',
        'created_by_user_id',
        'finalized_by_user_id',
        'finalized_at',
    ];

    protected $casts = [
        'tanggal_rapat' => 'date',
        'finalized_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id', 'penugasan_id');
    }

    public function temuan()
    {
        return $this->hasMany(RingkasanKondisiAuditTemuan::class, 'rka_id', 'rka_id')
            ->orderBy('urutan');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id', 'id');
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by_user_id', 'id');
    }
}
