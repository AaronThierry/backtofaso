<?php

namespace App\Http\Controllers;

use App\Models\TalentProfile;
use App\Models\User;
use App\Support\Faq;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $talents = TalentProfile::visible()->with('user')->latest()->take(4)->get();

        return view('site.home', ['talents' => $talents, 'faq' => Faq::featured()]);
    }

    public function study(): View
    {
        return view('site.study', ['fields' => User::FIELDS]);
    }

    public function companies(): View
    {
        $talents = TalentProfile::visible()->with('user')->latest()->take(3)->get();
        $fields = TalentProfile::visible()->selectRaw('field, count(*) as total')->groupBy('field')->pluck('total', 'field');

        return view('site.companies', compact('talents', 'fields'));
    }

    public function faq(): View
    {
        return view('site.faq', ['faq' => Faq::all()]);
    }

    /** Pages légales : mentions, confidentialité, conditions */
    public function legal(string $page): View
    {
        return view('site.legal.'.$page);
    }

    public function sitemap(): Response
    {
        $urls = collect(['home', 'study', 'companies', 'faq', 'contact', 'legal.notice', 'legal.privacy', 'legal.terms', 'welcome', 'login'])
            ->map(fn ($name) => route($name));

        return response()->view('site.sitemap', ['urls' => $urls])->header('Content-Type', 'application/xml');
    }

    public function offline(): View
    {
        return view('site.offline');
    }
}
