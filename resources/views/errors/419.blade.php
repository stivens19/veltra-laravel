@extends(view()->exists('errors.layout') ? 'errors.layout' : 'veltra::errors.layout')

@section('title', __('Sesión Expirada'))
@section('code', '419')
@section('badge', __('Error 419 • Token CSRF Expirado'))
@section('badge_class', 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 ring-indigo-200 dark:ring-indigo-800/60')
@section('message', __('Tu Sesión ha Expirado'))
@section('description', __('Por motivos de seguridad corporativa, el token de formulario ha caducado. Por favor recarga la página o ingresa de nuevo.'))
