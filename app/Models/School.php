<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'sekolah';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
    ];
}
