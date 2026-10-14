<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TalentController;
use Illuminate\Support\Facades\Route;

// ---------- Site public ----------
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/etudier-a-l-etranger', [PageController::class, 'study'])->name('study');
Route::permanentRedirect('/etudier-en-chine', '/etudier-a-l-etranger'); // ancienne adresse
Route::get('/entreprises', [PageController::class, 'companies'])->name('companies');
Route::get('/questions-frequentes', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,10')->name('contact.store');
Route::get('/mentions-legales', [PageController::class, 'legal'])->defaults('page', 'notice')->name('legal.notice');
Route::get('/confidentialite', [PageController::class, 'legal'])->defaults('page', 'privacy')->name('legal.privacy');
Route::get('/conditions-utilisation', [PageController::class, 'legal'])->defaults('page', 'terms')->name('legal.terms');
Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::get('/hors-ligne', [PageController::class, 'offline'])->name('offline');

// ---------- Invités : inscription et connexion ----------
Route::middleware('guest')->group(function () {
    Route::get('/bienvenue', [RegisterController::class, 'welcome'])->name('welcome');

    // Étape 1 : compte
    Route::get('/inscription', [RegisterController::class, 'create'])->name('register');
    Route::post('/inscription', [RegisterController::class, 'store'])->middleware('throttle:6,1')->name('register.store');
    // Étape 2 : code reçu par e-mail
    Route::get('/inscription/verification', [RegisterController::class, 'verifyForm'])->name('register.verify');
    Route::post('/inscription/verification', [RegisterController::class, 'verify'])->middleware('throttle:10,1')->name('register.verify.store');
    Route::post('/inscription/renvoyer', [RegisterController::class, 'resend'])->middleware('throttle:3,1')->name('register.resend');

    // Connexion : e-mail + mot de passe, puis code reçu par e-mail
    Route::get('/connexion', [LoginController::class, 'create'])->name('login');
    Route::post('/connexion', [LoginController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/connexion/verification', [LoginController::class, 'verifyForm'])->name('login.verify');
    Route::post('/connexion/verification', [LoginController::class, 'verify'])->middleware('throttle:10,1')->name('login.verify.store');
    Route::post('/connexion/renvoyer', [LoginController::class, 'resend'])->middleware('throttle:3,1')->name('login.resend');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [LoginController::class, 'destroy'])->name('logout');

    // Étapes 3 et 4 de l'inscription
    Route::get('/inscription/profil', [OnboardingController::class, 'profile'])->name('onboarding.profile');
    Route::post('/inscription/profil', [OnboardingController::class, 'saveProfile'])->name('onboarding.profile.store');
    Route::get('/inscription/preferences', [OnboardingController::class, 'preferences'])->name('onboarding.preferences');
    Route::post('/inscription/preferences', [OnboardingController::class, 'savePreferences'])->name('onboarding.preferences.store');
    Route::get('/inscription/termine', [OnboardingController::class, 'done'])->name('onboarding.done');

    Route::middleware('onboarded')->group(function () {
        Route::get('/accueil', DashboardController::class)->name('dashboard');

        Route::get('/dossier', [DossierController::class, 'show'])->name('dossier');
        Route::post('/dossier/documents/{document}', [DossierController::class, 'upload'])->name('dossier.upload');

        Route::get('/talents', [TalentController::class, 'index'])->name('talents.index');
        Route::get('/talents/{profile}', [TalentController::class, 'show'])->name('talents.show');
        Route::get('/mon-profil-talent', [TalentController::class, 'edit'])->name('talents.edit');
        Route::put('/mon-profil-talent', [TalentController::class, 'update'])->name('talents.update');

        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
        Route::get('/chat/{conversation}/messages', [ChatController::class, 'messages'])->name('chat.messages');
        Route::post('/chat/{conversation}/messages', [ChatController::class, 'store'])->middleware('throttle:30,1')->name('chat.store');
        Route::post('/chat/direct/{user}', [ChatController::class, 'direct'])->name('chat.direct');

        Route::get('/groupes/nouveau', [GroupController::class, 'create'])->name('groups.create');
        Route::post('/groupes', [GroupController::class, 'store'])->name('groups.store');
        Route::post('/groupes/{conversation}/rejoindre', [GroupController::class, 'join'])->name('groups.join');

        Route::get('/compte', [AccountController::class, 'show'])->name('account');
    });
});
