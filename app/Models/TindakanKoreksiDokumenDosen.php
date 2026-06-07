<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanKoreksiDokumenDosen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tindakan_koreksi_dosen_dokumen';
    protected $primaryKey = 'dokumen_tk_dosen_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'dokumen_tk_dosen_id',
        'tindakan_koreksi_id',
        'dosen_id',
        'uploaded_by_user_id',
        'nama_file',
        'file_path',
        'keterangan',
        'status_validasi',
        'catatan_validasi',
        'validated_by_user_id',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function tindakanKoreksi()
    {
        return $this->belongsTo(TindakanKoreksi::class, 'tindakan_koreksi_id', 'tindakan_koreksi_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id', 'dosen_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id', 'id');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by_user_id', 'id');
    }
}
