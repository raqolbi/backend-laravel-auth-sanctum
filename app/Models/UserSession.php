<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserSession extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'users_sessions';
    protected $primaryKey = 'id';
    public $timestamps = true;
    public $incrementing = true;
    protected $fillable = [
        'userid',
        'login_at',
        'logout_at',
    ];
}
