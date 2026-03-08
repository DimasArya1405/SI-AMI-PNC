<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory, HasUuids;

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
}
