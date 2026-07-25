<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemFlag extends Model
{
    use HasFactory;
    protected $table = 'systemflag';
    protected $fillable = [
        'valueType',
        'name',
        'value',
    ];

    public static function getByKey($key)
    {
        if ($key) {
            return self::where('name', $key)->select('value');
        }
    }
}
