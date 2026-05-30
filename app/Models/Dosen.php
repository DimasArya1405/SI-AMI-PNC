<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dosen extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'dosen';
    protected $primaryKey = 'dosen_id';
    protected $fillable = [
        'dosen_id',
        'user_id',
        'prodi_id',
        'nip',
        'nama_lengkap',
        'jabatan',
        'no_telp',
        'email',
        'status_aktif'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'prodi_id');
    }

    public function buktiDukung()
    {
        return $this->hasMany(JawabanAMI::class, 'dosen_id', 'dosen_id');
    }
}
