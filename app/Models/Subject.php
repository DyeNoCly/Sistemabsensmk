<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'mata_pelajaran';

    protected $primaryKey = 'idm';

    public $timestamps = false;

    protected $fillable = [
        'nama_mp',
    ];
}
