<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class QuestionBank extends Model
{
    use HasFactory, Notifiable;
    protected $table = 'questionbank';

    protected $fillable = [
        'name',
        'info',
        'note',
        'categoryId',
        'creatorId',
      ];

      protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
