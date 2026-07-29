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
        'updated_at',
        'type',
        'sId1',
        'isFreeSession',
        'is_sequential',
        'tried_astrologer_ids',
        'rejected_astrologer_ids',
        'rejected_by',
        'ring_started_at',
        'ring_timeout_seconds',
        'token',
    ];

    protected $casts = [
        'is_sequential' => 'boolean',
        'tried_astrologer_ids' => 'array',
        'rejected_astrologer_ids' => 'array',
        'ring_started_at' => 'datetime',
    ];

    public function astrologer() {
        return $this->belongsTo(Astrologer::class, 'astrologerId');
    }
    public function user() {
        return $this->belongsTo(User::class, 'userId');
    }

}
