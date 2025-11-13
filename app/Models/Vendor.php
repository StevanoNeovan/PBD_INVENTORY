<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $table = 'vendor';
    protected $primaryKey = 'idvendor';
    public $timestamps = false;

    protected $fillable = ['nama_vendor', 'badan_hukum', 'status'];

    public function pengadaan(): HasMany
    {
        return $this->hasMany(Pengadaan::class, 'idvendor');
    }
}
