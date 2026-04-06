<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemSubStandarMutu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'item_sub_standar';
    protected $primaryKey = 'item_sub_standar_id';
    protected $fillable = [
        'item_sub_standar_id',
        'parent_item_id',
        'sub_standar_id',
        'nama_item',
        'level',
        'tipe_item',
    ];

    public function sub_standar_mutu()
    {
        return $this->belongsTo(SubStandarMutu::class, 'sub_standar_id', 'sub_standar_id');
    }

    public function upt_item_sub_standar_mutu()
    {
        return $this->hasMany(UptItemSubStandarMutu::class, 'item_sub_standar_master_id', 'item_sub_standar_id');
    }

    public function parent()
    {
        return $this->belongsTo(ItemSubStandarMutu::class, 'parent_item_id', 'item_sub_standar_id');
    }

    public function children()
    {
        return $this->hasMany(ItemSubStandarMutu::class, 'parent_item_id', 'item_sub_standar_id');
    }
}
