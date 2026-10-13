@extends('errors.layout')

@section('code', '503')
@section('title', 'Maintenance en cours')
@section('heading', 'Nous améliorons le site.')
@section('message', 'Back to Burkina est en cours de mise à jour. Revenez dans quelques minutes : vos informations sont en sécurité.')

@section('actions')
    <a href="javascript:location.reload()" class="btn-gold">Réessayer</a>
@endsection
