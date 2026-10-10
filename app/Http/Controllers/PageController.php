<?php

namespace App\Http\Controllers;

use App\Models\TalentProfile;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $talents = TalentProfile::visible()->with('user')->latest()->take(4)->get();

        return view('site.home', compact('talents'));
    }

    public function offline(): View
    {
        return view('site.offline');
    }
}
