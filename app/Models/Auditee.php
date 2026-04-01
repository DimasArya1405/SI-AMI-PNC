<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditee extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'auditee';
    protected $primaryKey = 'auditee_id';
    protected $fillable = [
        'auditee_id',
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
