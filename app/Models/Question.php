<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Question extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'questions';

    protected $fillable = [
        'content',
        'correctAnswer',
        'inCorrectAnswer',
        'level',
        'questionBankId',
      ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
