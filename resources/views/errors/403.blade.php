@extends(view()->exists('errors.layout') ? 'errors.layout' : 'veltra::errors.layout')

@section('title', __('Acceso Prohibido'))
@section('code', '403')
@section('badge', __('Error 403 • Acceso Denegado'))
@section('badge_class', 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 ring-amber-200 dark:ring-amber-800/60')
@section('message', __('Acceso Restringido'))
@section('description', __('No cuentas con los permisos o privilegios suficientes para consultar este recurso o sección.'))
