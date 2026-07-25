<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = 'payment';
    protected $fillable = [
        'paymentMode',
        'paymentReference',
        'paymentStatus',
        'amount',
        'createdBy',
        'modifiedBy',
        'userId',
        'signature',
        'orderId'
    ];
}
