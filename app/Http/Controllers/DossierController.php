<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\DossierDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DossierController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (! $user->needsDossier()) {
            return redirect()->route('dashboard');
        }

        $dossier = $user->dossier ?? Dossier::openFor($user);
        $dossier->load(['documents', 'advisor']);

        return view('app.dossier', compact('dossier'));
    }

    public function upload(Request $request, DossierDocument $document): RedirectResponse
    {
        abort_unless($document->dossier->user_id === $request->user()->id, 403);
        abort_unless($document->canUpload(), 422, 'Ce document est déjà en cours de vérification.');

        $request->validate([
            'file' => ['required', 'file', 'mimes:'.config('backtoburkina.uploads.document_mimes'), 'max:'.config('backtoburkina.uploads.max_kb')],
        ], ['file.mimes' => 'Formats acceptés : PDF, JPG ou PNG.']);

        $file = $request->file('file');
        $document->update([
            'path' => $file->store('dossiers/'.$document->dossier_id, 'local'),
            'original_name' => $file->getClientOriginalName(),
            'status' => DossierDocument::PENDING,
            'submitted_at' => now(),
            'note' => null,
        ]);

        return back()->with('status', '« '.$document->label.' » a été envoyé. Votre conseiller va le vérifier.');
    }
}
