<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Result extends Model
{
    use HasFactory;
    protected $table = 'result';

    protected $fillable = [
        'score',
        'restTime',
        'examineeId',
        'emxamId',
        'countLimit',
      ];

      protected $casts = [
        'email_verified_at' => 'datetime',
    ];


}
