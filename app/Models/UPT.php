<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UPT extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'upt';
    protected $primaryKey = 'upt_id';
    protected $fillable = [
        'upt_id',
        'kode_upt',
        'nama_upt',
        'kategori_upt',
    ];

    public function uptStandarMutu()
    {
        return $this->hasMany(UptStandarMutu::class, 'upt_id');
    }
}
