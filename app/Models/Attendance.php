<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'absensi';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'nis',
        'idm',
        'tanggal',
        'status',
        'latitude',
        'longitude',
        'photo_path',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'nis', 'nis');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'idm', 'idm');
    }
}
