<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UptStandarMutu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'upt_standar_mutu';
    protected $primaryKey = 'upt_standar_mutu_id';

    protected $fillable = [
        'upt_standar_mutu_id',
        'upt_id',
        'standar_mutu_id',
        'periode_id',
    ];

    public function standar_mutu()
    {
        return $this->belongsTo(StandarMutu::class, 'standar_mutu_id', 'standar_mutu_id');
    }

    public function upt()
    {
        return $this->belongsTo(Upt::class, 'upt_id', 'upt_id');
    }
}
