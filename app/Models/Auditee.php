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
        'upt_id',
        'nip',
        'nama_lengkap',
        'no_telp',
        'email',
        'status_aktif'
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'prodi_id');
    }

    public function upt()
    {
        return $this->belongsTo(UPT::class, 'upt_id', 'upt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'auditee_id', 'auditee_id');
    }
}
