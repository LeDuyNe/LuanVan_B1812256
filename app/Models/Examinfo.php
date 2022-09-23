<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examinfo extends Model
{
    use HasFactory;
    protected $table = 'examinfos';

    protected $fillable = [
        'userID',
        'course',
        'total_questions',
        'uniqueid',
        'time',
        'status',
        'timeActive',
      ];

      protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
