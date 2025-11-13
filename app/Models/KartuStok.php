<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KartuStok extends Model
{
    protected $table = 'kartu_stok';
    protected $primaryKey = 'idkartu_stok';
    public $timestamps = false;

    protected $fillable = ['jenis_transaksi', 'masuk', 'keluar', 'stock', 'created_at', 'idtransaksi', 'idbarang'];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'idbarang');
    }
}

