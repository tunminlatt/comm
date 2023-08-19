<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomLinkController extends Controller
{
    public function privacyPolicy()
    {
    	return view('web.privacy-policy');
    }

    public function appDownload()
    {
    	return view('web.application-download');
    }
}
