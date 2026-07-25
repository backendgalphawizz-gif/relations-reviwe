<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerGift extends Model
{
    use HasFactory;
    protected $table = 'astrologer_gifts';
    protected $fillable = [
        'astrologerId',
        'giftId',
        'userId',
        'createdBy',
        'modifiedBy',
        'giftAmount'
    ];
}
