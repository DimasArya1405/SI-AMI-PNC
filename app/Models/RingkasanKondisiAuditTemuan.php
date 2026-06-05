<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RingkasanKondisiAuditTemuan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ringkasan_kondisi_audit_temuan';
    protected $primaryKey = 'rka_temuan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rka_temuan_id',
        'rka_id',
        'jawaban_audit_id',
        'kondisi_final',
        'kategori_final',
        'rekomendasi',
        'urutan',
    ];

    public function rka()
    {
        return $this->belongsTo(RingkasanKondisiAudit::class, 'rka_id', 'rka_id');
    }

    public function jawabanAudit()
    {
        return $this->belongsTo(JawabanAudit::class, 'jawaban_audit_id', 'id');
    }
}
