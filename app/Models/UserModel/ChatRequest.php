<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AstrologerModel\Astrologer;
use App\Models\User;
class ChatRequest extends Model
{
    use HasFactory;
    protected $table = 'chatrequest';
    protected $fillable = [
        'astrologerId',
        'chatStatus',
        'userId',
        'chatRate',
        'totalMin',
        'deductionFromAstrologer',
        'deduction',
        'isFreeSession',
    ];

    public function astrologer() {
        return $this->belongsTo(Astrologer::class, 'astrologerId');
    }
    public function user() {
        return $this->belongsTo(User::class, 'userId');
    }

}
