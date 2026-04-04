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
        'sub_standar_id',
        'nama_item',
    ];

    public function sub_standar_mutu()
    {
        return $this->belongsTo(SubStandarMutu::class, 'sub_standar_id', 'sub_standar_id');
    }
}
