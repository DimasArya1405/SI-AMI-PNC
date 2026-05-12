<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UptSubStandarMutu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'upt_sub_standar_mutu';
    protected $primaryKey = 'upt_sub_standar_id';

    protected $fillable = [
        'upt_sub_standar_id',
        'upt_standar_mutu_id',
        'sub_standar_master_id',
        'nama_sub_standar',
        'urutan',
    ];

    public function uptStandarMutu()
    {
        return $this->belongsTo(UptStandarMutu::class, 'upt_standar_mutu_id', 'upt_standar_mutu_id');
    }

    public function subStandarMaster()
    {
        return $this->belongsTo(SubStandarMutu::class, 'sub_standar_master_id', 'sub_standar_id');
    }

    public function items()
    {
        return $this->hasMany(UptItemSubStandarMutu::class, 'upt_sub_standar_id', 'upt_sub_standar_id')
            ->orderBy('urutan');
    }
}
