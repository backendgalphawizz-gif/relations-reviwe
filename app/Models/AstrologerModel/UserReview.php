<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    use HasFactory;
    protected $table = 'user_reviews';
    protected $fillable = [
        'userId',
        'rating',
        'review',
        'astrologerId',
        'createdBy',
        'modifiedBy',
        'isPublic'
    ];
}
