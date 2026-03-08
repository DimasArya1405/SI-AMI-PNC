<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Prodi extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'prodi';
    protected $primaryKey = 'prodi_id';
    protected $fillable = [
        'prodi_id',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
    ];
}
