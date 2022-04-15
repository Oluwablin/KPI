<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RoleUser extends Model
{
    use HasFactory;

    protected $table = 'role_user';

    protected $guarded = [''];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
