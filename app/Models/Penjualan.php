<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'idpenjualan';
    public $timestamps = false;

    protected $fillable = ['created_at', 'subtotal_nilai', 'ppn', 'total_nilai', 'iduser', 'idmargin_penjualan'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    public function marginPenjualan(): BelongsTo
    {
        return $this->belongsTo(MarginPenjualan::class, 'idmargin_penjualan');
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class, 'idpenjualan');
    }
}
