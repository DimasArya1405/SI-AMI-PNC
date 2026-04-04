<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StandarMutu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'standar_mutu';
    protected $primaryKey = 'standar_mutu_id';

    protected $fillable = [
        'standar_mutu_id',
        'nama_standar_mutu',
    ];

    public function sub_standar_mutu()
    {
        return $this->hasMany(SubStandarMutu::class, 'standar_mutu_id');
    }

    public function upt_standar_mutu()
    {
        return $this->hasMany(UptStandarMutu::class, 'standar_mutu_id');
    }
}
