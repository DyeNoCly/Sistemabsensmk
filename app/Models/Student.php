<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'siswa';

    protected $primaryKey = 'ids';

    public $timestamps = false;

    protected $fillable = [
        'nis',
        'nama',
        'jk',
        'alamat',
        'idk',
        'tlp',
        'bapak',
        'k_bapak',
        'ibu',
        'k_ibu',
        'pass',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'idk', 'idk');
    }
}
