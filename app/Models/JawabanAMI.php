<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanAMI extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jawaban_ami';
    protected $primaryKey = 'jawaban_id';

    protected $fillable = [
        'jawaban_id',
        'upt_item_sub_standar_id',
        'penugasan_id',
        'nama_file',
        'file_path',
        'keterangan',
        'sumber',
        'uploaded_by_user_id',
        'dosen_id',
        'status_validasi',
        'catatan_validasi',
        'validated_by_user_id',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id', 'penugasan_id');
    }

    public function item()
    {
        return $this->belongsTo(UptItemSubStandarMutu::class, 'upt_item_sub_standar_id', 'upt_item_sub_standar_id');
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
