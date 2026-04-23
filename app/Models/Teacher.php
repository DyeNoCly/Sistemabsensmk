<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'guru';

    protected $primaryKey = 'idg';

    public $timestamps = false;

    protected $fillable = [
        'nip',
        'nama',
        'jk',
        'alamat',
        'pass',
    ];
}
