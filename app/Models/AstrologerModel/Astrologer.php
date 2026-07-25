<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserModel\UserDeviceDetail;
use App\Models\UserModel\CallRequest;

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
        'videoCallRate',
        'reportRate ',
        'nameofplateform',
        'monthlyEarning',
        'referedPerson'
    ];

    public function token() {
        return $this->hasOne(UserDeviceDetail::class, 'userId', 'userId');
    }
    
    public function availability() {
        return $this->hasMany(AstrologerAvailability::class, 'astrologerId');
    }
    public function deviceDetail() {
        return $this->hasMany(UserDeviceDetail::class, 'userId', 'userId');
    }
    
    public function callRequest() {
        return $this->hasMany(CallRequest::class, 'astrologerId')->whereIn('callStatus', ['Pending', 'Accepted', 'Confirmed']);
    }

}
