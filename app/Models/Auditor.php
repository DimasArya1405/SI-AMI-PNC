<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditor extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'auditor';
    protected $primaryKey = 'auditor_id';
    protected $fillable = [
        'auditor_id',
        'user_id',
        'nip',
        'nama_lengkap',
        'jabatan',
        'prodi_id',
        'no_telp',
        'email',
        'status_aktif',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'prodi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'auditor_id', 'auditor_id');
    }
}
