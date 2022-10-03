<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Exams extends Model
{
    use HasFactory, Uuids, Notifiable;
    protected $table = 'exams';

    protected $fillable = [
        'name',
        'timeDuration',
        'timeStart',
        'countLimit',
        'note',
        'isPublished',
        'categoryId',
        'creatorId',
      ];

      protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
