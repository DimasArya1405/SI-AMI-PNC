<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penugasan extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $table = 'penugasan';
    protected $primaryKey = 'penugasan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'penugasan_id',
        'periode_id',
        'upt_id',
        'auditor_id',
        'auditee_id',
        'tanggal_audit',
        'lokasi',
        'status_penugasan'
    ];

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id', 'id');
    }

    public function auditor()
    {
        return $this->belongsTo(Auditor::class, 'auditor_id', 'auditor_id');
    }

    public function auditee()
    {
        return $this->belongsTo(Auditee::class, 'auditee_id', 'auditee_id');
    }

    public function upt()
    {
        return $this->belongsTo(UPT::class, 'upt_id', 'upt_id');
    }
}