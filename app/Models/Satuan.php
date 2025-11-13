<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Satuan extends Model
{
    protected $table = 'satuan';
    protected $primaryKey = 'idsatuan';
    public $timestamps = false;

    protected $fillable = ['nama_satuan', 'status'];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'idsatuan');
    }
}

