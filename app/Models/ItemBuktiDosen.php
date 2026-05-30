<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBuktiDosen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'item_bukti_dosen';
    protected $primaryKey = 'item_bukti_dosen_id';

    protected $fillable = [
        'item_bukti_dosen_id',
        'penugasan_id',
        'upt_item_sub_standar_id',
        'assigned_by_user_id',
    ];

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id', 'penugasan_id');
    }

    public function item()
    {
        return $this->belongsTo(UptItemSubStandarMutu::class, 'upt_item_sub_standar_id', 'upt_item_sub_standar_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id', 'id');
    }
}
