@extends(view()->exists('errors.layout') ? 'errors.layout' : 'veltra::errors.layout')

@section('title', __('Página No Encontrada'))
@section('code', '404')
@section('badge', __('Error 404 • Recurso No Encontrado'))
@section('badge_class', 'bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 ring-cyan-200 dark:ring-cyan-800/60')
@section('message', __('Página No Encontrada'))
@section('description', __('La página o recurso al que intentas acceder no existe, ha sido movido o ya no se encuentra disponible.'))
