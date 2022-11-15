<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class QuestionBank extends Authenticatable
{
    use HasFactory, Uuids, Notifiable;
    protected $table = 'questionbank';

    protected $fillable = [
        'name',
        'note',
        'categoryId',
        'creatorId',
      ];

      protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
