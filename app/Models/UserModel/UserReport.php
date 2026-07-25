<?php

namespace App\Models\UserModel;

use App\Models\AstrologerModel\Skill;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;
    protected $table = 'user_reports';
    protected $fillable = [
        'userId',
        'firstName',
        'lastName',
        'contactNo',
        'gender',
        'birthDate',
        'birthTime',
        'birthPlace',
        'occupation',
        'maritalStatus',
        'answerLanguage',
        'partnerName',
        'partnerBirthDate',
        'partnerBirthTime',
        'partnerBirthPlace',
        'comments',
        'reportFile',
        'createdBy',
        'modifiedBy',
        'reportType',
        'astrologerId',
        'countryCode',
        'reportRate'
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
