<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    use HasFactory;
    protected $table = 'order_request';
    protected $fillable = [
        'productCategoryId',
        'productId',
        'orderAddressId',
        'payableAmount',
        'walletBalanceDeducted',
        'totalPayable',
        'paymentMethod',
        'orderStatus',
        'userId',
        'gstPercent',
        'orderType',
    ];
}
