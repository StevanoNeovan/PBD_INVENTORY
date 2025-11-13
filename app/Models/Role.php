<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Role extends Model
{
   protected $table = 'role';
    protected $primaryKey = 'idrole';
    public $timestamps = false;

    protected $fillable = ['nama_role'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'idrole');
    }
}
