<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UptItemSubStandarMutu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'upt_item_sub_standar_mutu';
    protected $primaryKey = 'upt_item_sub_standar_id';

    protected $fillable = [
        'upt_item_sub_standar_id',
        'upt_sub_standar_id',
        'item_sub_standar_master_id',
        'parent_upt_item_id',
        'tipe_item',
        'level',
        'nama_item',
        'urutan',
    ];

    public function uptSubStandar()
    {
        return $this->belongsTo(UptSubStandarMutu::class, 'upt_sub_standar_id', 'upt_sub_standar_id');
    }

    public function itemMaster()
    {
        return $this->belongsTo(ItemSubStandarMutu::class, 'item_sub_standar_master_id', 'item_sub_standar_id');
    }

    public function parent()
    {
        return $this->belongsTo(UptItemSubStandarMutu::class, 'parent_upt_item_id', 'upt_item_sub_standar_id');
    }

    public function children()
    {
        return $this->hasMany(UptItemSubStandarMutu::class, 'parent_upt_item_id', 'upt_item_sub_standar_id');
    }
}
