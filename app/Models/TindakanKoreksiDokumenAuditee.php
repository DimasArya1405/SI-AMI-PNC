<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanKoreksiDokumenAuditee extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tindakan_koreksi_auditee_dokumen';
    protected $primaryKey = 'dokumen_tk_auditee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'dokumen_tk_auditee_id',
        'tindakan_koreksi_id',
        'uploaded_by_user_id',
        'nama_file',
        'file_path',
        'keterangan',
    ];

    public function tindakanKoreksi()
    {
        return $this->belongsTo(TindakanKoreksi::class, 'tindakan_koreksi_id', 'tindakan_koreksi_id');
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id', 'id');
    }
}
