<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $primaryKey = 'idk';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'id', 'id');
    }
}
