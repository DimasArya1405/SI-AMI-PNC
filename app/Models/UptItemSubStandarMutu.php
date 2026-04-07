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
        'upt_id',
        'upt_sub_standar_id',
        'item_sub_standar_master_id',
        'parent_upt_item_id',
        'tipe_item',
        'level',
        'nama_item',
        'urutan',
    ];

    public function upt()
    {
        return $this->belongsTo(UPT::class, 'upt_id', 'upt_id');
    }

    public function upt_sub_standar()
    {
        return $this->belongsTo(UptSubStandarMutu::class, 'upt_sub_standar_id', 'upt_sub_standar_id');
    }

    public function item_sub_standar_master()
    {
        return $this->belongsTo(ItemSubStandarMutu::class, 'item_sub_standar_master_id', 'item_sub_standar_id');
    }
}
