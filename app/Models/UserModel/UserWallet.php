<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;
    protected $table = 'user_wallets';
    protected $fillable = [
        'userId',
        'amount',
        'createdBy',
        'modifiedBy'
    ];
}
