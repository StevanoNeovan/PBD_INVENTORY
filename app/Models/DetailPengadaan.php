<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengadaan extends Model
{
    protected $table = 'detail_pengadaan';
    protected $primaryKey = 'iddetail_pengadaan';
    public $timestamps = false;

    protected $fillable = ['harga_satuan', 'jumlah', 'sub_total', 'idbarang', 'idpengadaan'];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'idbarang');
    }

    public function pengadaan(): BelongsTo
    {
        return $this->belongsTo(Pengadaan::class, 'idpengadaan');
    }
}

