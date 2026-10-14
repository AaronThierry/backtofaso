<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dossier;
use App\Models\DossierDocument;
use App\Models\User;
use App\Support\Destinations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Suivi des dossiers de candidature par l'équipe */
class DossierController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $filters = $request->only(['etape', 'pays', 'conseiller', 'documents', 'statut']);

        $dossiers = Dossier::with(['user', 'advisor'])
            ->withCount([
                'documents as pending_count' => fn ($d) => $d->where('status', DossierDocument::PENDING),
                'documents as todo_count' => fn ($d) => $d->whereIn('status', [DossierDocument::MISSING, DossierDocument::TO_COMPLETE]),
            ])
            ->when($q !== '', fn ($w) => $w->whereHas('user', fn ($u) => $u->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%")))
            ->when($filters['etape'] ?? null, fn ($w, $v) => $w->where('current_step', (int) $v))
            ->when($filters['pays'] ?? null, fn ($w, $v) => $w->where('country', $v))
            ->when(($filters['conseiller'] ?? null) === 'aucun', fn ($w) => $w->whereNull('advisor_id'))
            ->when(is_numeric($filters['conseiller'] ?? null), fn ($w) => $w->where('advisor_id', (int) $filters['conseiller']))
            ->when(($filters['documents'] ?? null) === 'a-verifier', fn ($w) => $w->whereHas('documents', fn ($d) => $d->where('status', DossierDocument::PENDING)))
            ->where('status', in_array($filters['statut'] ?? null, ['open', 'completed', 'cancelled'], true) ? $filters['statut'] : 'open')
            ->orderByDesc('pending_count')
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.dossiers.index', [
            'dossiers' => $dossiers,
            'q' => $q,
            'filters' => $filters,
            'advisors' => User::whereIn('role', [User::ROLE_AGENT, User::ROLE_ADMIN])->orderBy('first_name')->get(),
            'countries' => Destinations::options(),
        ]);
    }

    public function show(Dossier $dossier): View
    {
        $dossier->load(['user', 'advisor', 'documents']);

        return view('admin.dossiers.show', [
            'dossier' => $dossier,
            'advisors' => User::whereIn('role', [User::ROLE_AGENT, User::ROLE_ADMIN])->orderBy('first_name')->get(),
            'countries' => Destinations::options(),
        ]);
    }

    public function update(Request $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validate([
            'program' => ['nullable', 'string', 'max:160'],
            'university' => ['nullable', 'string', 'max:160'],
            'country' => ['nullable', Rule::in(array_keys(Destinations::options()))],
            'city' => ['nullable', 'string', 'max:80'],
            'intake_year' => ['nullable', 'integer', 'between:2000,2100'],
            'current_step' => ['required', 'integer', Rule::in(array_keys(Dossier::STEPS))],
            'status' => ['required', Rule::in(['open', 'completed', 'cancelled'])],
            'advisor_id' => ['nullable', Rule::exists('users', 'id')->whereIn('role', [User::ROLE_AGENT, User::ROLE_ADMIN])],
        ]);

        $dossier->update($data);

        return redirect()->route('admin.dossiers.show', $dossier)->with('status', 'Dossier mis à jour.');
    }

    /** Téléchargement d'une pièce (stockage privé) */
    public function download(DossierDocument $document): StreamedResponse
    {
        abort_unless($document->path && Storage::disk('local')->exists($document->path), 404);

        $name = $document->original_name ?: Str::slug($document->label).'.'.pathinfo($document->path, PATHINFO_EXTENSION);

        return Storage::disk('local')->response($document->path, $name);
    }

    /** Vérification d'une pièce : valider, demander un complément (avec note) ou réinitialiser */
    public function review(Request $request, DossierDocument $document): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', Rule::in(['validate', 'complete', 'reset'])],
            'note' => ['nullable', 'required_if:decision,complete', 'string', 'max:500'],
        ], ['note.required_if' => 'Expliquez au candidat ce qu’il doit corriger.']);

        $document->update(match ($data['decision']) {
            'validate' => ['status' => DossierDocument::VALIDATED, 'note' => null],
            'complete' => ['status' => DossierDocument::TO_COMPLETE, 'note' => $data['note']],
            'reset' => ['status' => DossierDocument::MISSING, 'note' => $data['note'] ?? null],
        });

        $label = ['validate' => 'validé', 'complete' => 'renvoyé au candidat', 'reset' => 'réinitialisé'][$data['decision']];

        return redirect()->to(route('admin.dossiers.show', $document->dossier_id).'#documents')->with('status', '« '.$document->label.' » '.$label.'.');
    }

    /** Demander une pièce supplémentaire */
    public function addDocument(Request $request, Dossier $dossier): RedirectResponse
    {
        $data = $request->validate(['label' => ['required', 'string', 'max:120']]);
        $type = Str::slug($data['label']);

        abort_if($dossier->documents()->where('type', $type)->exists(), 422, 'Cette pièce est déjà demandée.');
        $dossier->documents()->create(['type' => $type, 'label' => $data['label'], 'status' => DossierDocument::MISSING]);

        return redirect()->to(route('admin.dossiers.show', $dossier).'#documents')->with('status', 'Pièce « '.$data['label'].' » ajoutée au dossier.');
    }
}
