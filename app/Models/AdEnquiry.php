<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdEnquiry extends Model
{
    use HasFactory;
    // protected $table = 'testimonials';
    protected $fillable = [
        'title',
        'message',
        'status'
    ];

    // protected $appends = [
    //     'user_link',
    //     'video_link'
    // ];

    // public function getUserLinkAttribute() {
    //     return asset('storage/' . $this->user_image);
    // }
    // public function getVideoLinkAttribute() {
    //     return asset('storage/' . $this->video_url);
    // }

}
