@extends('errors.layout')

@section('code', '419')
@section('title', 'Session expirée')
@section('heading', 'Votre session a expiré.')
@section('message', 'Pour votre sécurité, la page est restée ouverte trop longtemps. Rechargez-la et recommencez votre action.')

@section('actions')
    <a href="javascript:location.reload()" class="btn-gold">Recharger la page</a>
    <a href="/" class="btn-outline-light">Retour à l’accueil</a>
@endsection
