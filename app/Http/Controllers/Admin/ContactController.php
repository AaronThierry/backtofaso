<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Messages reçus par le formulaire de contact du site */
class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filtre') === 'traites' ? 'traites' : 'a-traiter';

        return view('admin.contacts.index', [
            'messages' => ContactMessage::when($filter === 'a-traiter', fn ($w) => $w->whereNull('handled_at'), fn ($w) => $w->whereNotNull('handled_at'))
                ->latest()->paginate(20)->withQueryString(),
            'filter' => $filter,
            'counts' => [
                'a-traiter' => ContactMessage::whereNull('handled_at')->count(),
                'traites' => ContactMessage::whereNotNull('handled_at')->count(),
            ],
        ]);
    }

    public function toggle(ContactMessage $message): RedirectResponse
    {
        $message->forceFill(['handled_at' => $message->handled_at ? null : now()])->save();

        return back()->with('status', $message->handled_at ? 'Message marqué comme traité.' : 'Message remis « à traiter ».');
    }
}
