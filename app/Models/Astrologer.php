<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserModel\UserDeviceDetail;

class Astrologer extends Model
{
    use HasFactory;
    protected $table = 'astrologers';
    protected $fillable = [
        'userId',
        'name',
        'email',
        'contactNo',
        'gender',
        'birthDate',
        'primarySkill',
        'allSkill',
        'languageKnown',
        'profileImage',
        'charge',
        'experienceInYears',
        'dailyContribution',
        'hearAboutAstroguru',
        'isWorkingOnAnotherPlatform',
        'whyOnBoard',
        'interviewSuitableTime',
        'currentCity',
        'mainSourceOfBusiness',
        'highestQualification',
        'degree',
        'college',
        'learnAstrology',
        'astrologerCategoryId',
        'instaProfileLink',
        'facebookProfileLink',
        'linkedInProfileLink',
        'youtubeChannelLink',
        'websiteProfileLink',
        'isAnyBodyRefer',
        'minimumEarning',
        'maximumEarning',
        'loginBio',
        'NoofforeignCountriesTravel',
        'currentlyworkingfulltimejob',
        'goodQuality',
        'biggestChallenge',
        'whatwillDo',
        'isVerified',
    ];

    public function token() {
        return $this->hasOne(UserDeviceDetail::class, 'userId', 'userId');
    }

}
