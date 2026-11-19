@extends(view()->exists('errors.layout') ? 'errors.layout' : 'veltra::errors.layout')

@section('title', __('Servicio en Mantenimiento'))
@section('code', '503')
@section('badge', __('Error 503 • Mantenimiento Preventivo'))
@section('badge_class', 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 ring-sky-200 dark:ring-sky-800/60')
@section('message', __('Plataforma en Mantenimiento'))
@section('description', __('Estamos realizando labores de optimización y mantenimiento programado. Los servicios se restablecerán a la brevedad.'))
