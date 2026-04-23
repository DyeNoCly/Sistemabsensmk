<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    protected $table = 'hari';

    protected $primaryKey = 'idh';

    public $timestamps = false;

    protected $fillable = [
        'hari',
    ];
}
