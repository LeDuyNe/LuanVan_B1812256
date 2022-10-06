<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class QuestionBank_Question extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'questionBank_questions';

    protected $fillable = [
        'questionBankId',
        'quesitonId	',
      ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}