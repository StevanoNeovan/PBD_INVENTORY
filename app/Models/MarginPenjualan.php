<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarginPenjualan extends Model
{
    protected $table = 'margin_penjualan';
    protected $primaryKey = 'idmargin_penjualan';
    public $timestamps = false;

    protected $fillable = ['created_at', 'persen', 'status', 'iduser', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'iduser');
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'idmargin_penjualan');
    }

    public function tempDetailPenjualan(): HasMany
    {
        return $this->hasMany(TempDetailPenjualan::class, 'idmargin_penjualan');
    }
}

