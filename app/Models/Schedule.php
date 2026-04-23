<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'jadwal';

    protected $primaryKey = 'idj';

    public $timestamps = false;

    protected $fillable = [
        'idh',
        'idg',
        'idk',
        'idm',
        'jam_mulai',
        'jam_selesai',
        'aktif',
    ];

    public function day()
    {
        return $this->belongsTo(Day::class, 'idh', 'idh');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'idg', 'idg');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'idk', 'idk');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'idm', 'idm');
    }
}
