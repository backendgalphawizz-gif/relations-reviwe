<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Model;
use App\Models\UserModel\User;

class AstrologerEnquiry extends Model
{
    protected $table = 'astrologer_enquiries';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'profession',
        'file',
        'created_at',
        'updated_at'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
