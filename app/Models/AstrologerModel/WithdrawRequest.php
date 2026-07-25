<?php

namespace app\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WithDrawRequest extends Model
{
    use HasFactory;
    protected $table = 'withdrawrequest';
    protected $fillable = [
        'astrologerId',
        'withdrawAmount',
        'createdBy',
        'modifiedBy',
        'status'
    ];
}
