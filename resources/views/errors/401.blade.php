@extends(view()->exists('errors.layout') ? 'errors.layout' : 'veltra::errors.layout')

@section('title', __('No Autorizado'))
@section('code', '401')
@section('badge', __('Error 401 • Autenticación Requerida'))
@section('badge_class', 'bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 ring-cyan-200 dark:ring-cyan-800/60')
@section('message', __('Autenticación Requerida'))
@section('description', __('Debes autenticarte con tus credenciales de usuario para poder acceder y visualizar esta sección.'))
