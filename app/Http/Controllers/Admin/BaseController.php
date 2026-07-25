<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public $imageURL;
    public function imageUr()
    {
        $this->imageURL = "https://astro.codefuse.org/api/api/";
    }
}
