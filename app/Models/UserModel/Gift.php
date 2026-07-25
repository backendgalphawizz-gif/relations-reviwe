<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;
    protected $table = 'gifts';
    protected $fillable = [
        'name',
        'amount',
        'displayOrder',
        'createdBy',
        'modifiedBy'
    ];
}
