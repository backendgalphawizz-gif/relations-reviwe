<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
    
class SessionController extends Controller
{

    public function storeSession(Request $request)
    {
        session()->flush();
    }

   }
