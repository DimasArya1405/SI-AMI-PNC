<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Periode extends Model
{
    use HasFactory, SoftDeletes, HasUlids;

    protected $table = 'periode';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'tahun',
    ];
}
