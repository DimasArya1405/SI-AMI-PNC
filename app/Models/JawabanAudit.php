<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JawabanAudit extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jawaban_audit';

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'upt_item_sub_standar_id',
        'jawaban',
        'catatan',
    ];

    public function itemSubStandar()
    {
        return $this->belongsTo(UptItemSubStandarMutu::class, 'upt_item_sub_standar_id', 'upt_item_sub_standar_id');
    }
}