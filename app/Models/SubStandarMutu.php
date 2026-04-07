<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubStandarMutu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sub_standar_mutu';
    protected $primaryKey = 'sub_standar_id';

    protected $fillable = [
        'sub_standar_id',
        'standar_mutu_id',
        'nama_sub_standar',
        'urutan',
    ];

    public function standar_mutu()
    {
        return $this->belongsTo(StandarMutu::class, 'standar_mutu_id');
    }

    public function item_sub_standar_mutu()
    {
        return $this->hasMany(ItemSubStandarMutu::class, 'sub_standar_id');
    }

    public function upt_sub_standar_mutu()
    {
        return $this->hasMany(UptSubStandarMutu::class, 'sub_standar_master_id', 'sub_standar_id');
    }
}
