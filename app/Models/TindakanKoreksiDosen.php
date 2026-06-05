<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanKoreksiDosen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tindakan_koreksi_dosen';
    protected $primaryKey = 'tindakan_koreksi_dosen_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tindakan_koreksi_dosen_id',
        'tindakan_koreksi_id',
        'penugasan_id',
        'assigned_by_user_id',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function tindakanKoreksi()
    {
        return $this->belongsTo(TindakanKoreksi::class, 'tindakan_koreksi_id', 'tindakan_koreksi_id');
    }

    public function penugasan()
    {
        return $this->belongsTo(Penugasan::class, 'penugasan_id', 'penugasan_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id', 'id');
    }
}
