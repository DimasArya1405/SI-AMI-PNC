<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'dokumen';
    protected $primaryKey = 'dokumen_id';

    protected $fillable = [
        'dokumen_id',
        'upt_item_sub_standar_id',
        'auditee_id',
        'nama_file',
        'file_path',
        'keterangan',
    ];
}
