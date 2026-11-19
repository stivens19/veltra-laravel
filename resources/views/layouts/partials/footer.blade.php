<footer class="vlt-footer">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500 dark:text-slate-400">
        <div>
            {{ config('veltra.layout.footer_text', '© ' . date('Y') . ' VeltraUI Suite. Todos los derechos reservados.') }}
        </div>
        <div class="flex items-center gap-4">
            <span class="inline-flex items-center gap-1.5 font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                VeltraLaravel v{{ \Veltra\VeltraLaravel\Facades\Veltra::version() }}
            </span>
        </div>
    </div>
</footer>

