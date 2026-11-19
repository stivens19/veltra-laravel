@props([
    'striped' => false,
    'hoverable' => true,
])

<div class="w-full overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-slate-800">
    <table {{ $attributes->merge(['class' => 'vlt-table w-full text-left text-xs sm:text-sm' . ($striped ? ' vlt-table-striped' : '') . ($hoverable ? ' vlt-table-hover' : '')]) }}>
        @if(isset($header))
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                {{ $header }}
            </thead>
        @endif
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
            {{ $slot }}
        </tbody>
        @if(isset($footer))
            <tfoot class="bg-slate-50 dark:bg-slate-800/60 font-semibold border-t border-slate-200 dark:border-slate-800">
                {{ $footer }}
            </tfoot>
        @endif
    </table>
</div>

