<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        $subject = $request->query('sujet');

        return view('site.contact', [
            'subjects' => ContactMessage::SUBJECTS,
            'subject' => is_string($subject) && array_key_exists($subject, ContactMessage::SUBJECTS) ? $subject : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Piège à robots : champ caché que seuls les robots remplissent
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', Rule::in(array_keys(ContactMessage::SUBJECTS))],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
        ], [
            'name.required' => 'Indiquez votre nom.',
            'email.required' => 'Indiquez votre adresse e-mail pour que nous puissions vous répondre.',
            'email.email' => 'Adresse e-mail invalide.',
            'message.required' => 'Écrivez votre message.',
            'message.min' => 'Votre message est un peu court : précisez votre demande.',
            'consent.accepted' => 'Merci d’accepter que nous utilisions ces informations pour vous répondre.',
        ]);

        $contact = ContactMessage::create([...$data, 'ip' => $request->ip()]);

        try {
            Mail::to(config('backtoburkina.contact.email'))->send(new ContactMessageMail($contact));
        } catch (\Throwable $e) {
            // Le message est enregistré : l'équipe le retrouvera même si l'e-mail n'est pas parti
            report($e);
        }

        return redirect()->route('contact')->with('sent', true);
    }
}
