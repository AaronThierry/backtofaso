<?php

namespace App\Http\Controllers;

use App\Support\Phone;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('app.account', ['user' => $user, 'phone' => Phone::format($user->phone)]);
    }
}
