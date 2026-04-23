<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $table = 'user';

    protected $primaryKey = 'idu';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'pass',
        'level',
        'id',
    ];
}
