<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ChooseAnswer extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Uuids;
    protected $table = 'choose_answer';

    protected $fillable = [
        'answerId',
        'resultId',
      ];

      protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
