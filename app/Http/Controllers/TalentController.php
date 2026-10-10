<?php

namespace App\Http\Controllers;

use App\Models\TalentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TalentController extends Controller
{
    public function index(Request $request): View
    {
        $field = $request->query('domaine');
        $q = trim((string) $request->query('q'));

        $profiles = TalentProfile::visible()
            ->with('user')
            ->when($field && in_array($field, User::FIELDS, true), fn ($query) => $query->where('field', $field))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('headline', 'like', "%{$q}%")
                        ->orWhere('location', 'like', "%{$q}%")
                        ->orWhere('skills', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%"));
                });
            })
            ->orderByRaw("availability = 'available' desc")
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('app.talents.index', [
            'profiles' => $profiles,
            'fields' => User::FIELDS,
            'field' => $field,
            'q' => $q,
        ]);
    }

    public function show(Request $request, TalentProfile $profile): View
    {
        $mine = $profile->user_id === $request->user()->id;
        abort_unless($mine || $request->user()->isStaff() || ($profile->is_public && $profile->verified_at), 404);

        $profile->load(['user', 'experiences']);

        return view('app.talents.show', compact('profile', 'mine'));
    }

    public function edit(Request $request): View|RedirectResponse
    {
        $profile = $request->user()->talentProfile;
        if (! $profile) {
            return redirect()->route('dashboard')->with('status', 'Le profil talent est réservé aux diplômés de retour.');
        }

        return view('app.talents.edit', ['profile' => $profile, 'fields' => User::FIELDS]);
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->talentProfile;
        abort_unless($profile, 404);

        $data = $request->validate([
            'headline' => ['required', 'string', 'max:120'],
            'field' => ['required', Rule::in(User::FIELDS)],
            'school' => ['nullable', 'string', 'max:120'],
            'school_city' => ['nullable', 'string', 'max:60'],
            'graduation_year' => ['nullable', 'integer', 'between:1980,2100'],
            'skills' => ['nullable', 'string', 'max:500'],
            'languages' => ['nullable', 'string', 'max:200'],
            'hsk_level' => ['nullable', 'string', 'max:10'],
            'availability' => ['required', Rule::in(array_keys(TalentProfile::AVAILABILITY))],
            'available_from' => ['nullable', 'string', 'max:40'],
            'location' => ['nullable', 'string', 'max:60'],
            'bio' => ['nullable', 'string', 'max:1500'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:'.config('backtoburkina.uploads.max_kb')],
            'is_public' => ['nullable', 'boolean'],
        ]);

        $split = fn (?string $v) => array_values(array_filter(array_map('trim', explode(',', (string) $v))));
        $data['skills'] = $split($data['skills'] ?? '');
        $data['languages'] = $split($data['languages'] ?? '');
        $data['is_public'] = $request->boolean('is_public');

        if ($request->hasFile('cv')) {
            $data['cv_path'] = $request->file('cv')->store('cv', 'public');
        }
        unset($data['cv']);

        $profile->update($data);

        return redirect()->route('talents.show', $profile)->with('status', 'Profil mis à jour.');
    }
}
