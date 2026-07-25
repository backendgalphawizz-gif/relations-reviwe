<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AstrologerModel\Astrologer;
use App\Models\User;

class CallRequest extends Model
{
    use HasFactory;
    protected $table = 'callrequest';
    protected $fillable = [
        'astrologerId',
        'callStatus',
        'userId',
        'totalMin',
        'callRate',
        'deductionFromAstrologer',
        'deduction',
        'sId',
        'channelName',
        'chatId',
        'created_at',
        'type',
        'sId1',
        'isFreeSession',
    ];

    public function astrologer() {
        return $this->belongsTo(Astrologer::class, 'astrologerId');
    }
    public function user() {
        return $this->belongsTo(User::class, 'userId');
    }

}
