<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
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
        'modifiedBy',
    ];
}
