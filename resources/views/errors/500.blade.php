@extends(view()->exists('errors.layout') ? 'errors.layout' : 'veltra::errors.layout')

@section('title', __('Error del Servidor'))
@section('code', '500')
@section('badge', __('Error 500 • Error Interno'))
@section('badge_class', 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 ring-rose-200 dark:ring-rose-800/60')
@section('message', __('Error en el Servidor'))
@section('description', __('Ha ocurrido un error inesperado al procesar tu solicitud. El equipo técnico ha sido notificado.'))
