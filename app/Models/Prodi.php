<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Prodi extends Model
{
    use HasFactory, SoftDeletes, HasUuids;
    
    protected $table = 'prodi';
    protected $primaryKey = 'prodi_id';
    protected $fillable = [
        'prodi_id',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
    ];

    public function auditor()
    {
        return $this->hasMany(Auditor::class, 'prodi_id', 'prodi_id');
    }
}
