<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstromallProduct extends Model
{
    use HasFactory;
    protected $table = 'astromall_products';
    protected $fillable = [
        'name',
        'features',
        'productImage',
        'productCategoryId',
        'amount',
        'description',
        'createdBy',
        'modifiedBy'
    ];
}
