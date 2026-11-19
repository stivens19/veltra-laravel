<aside class="vlt-sidebar border-r border-slate-200/80 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md">
    <!-- Sidebar Brand Header -->
    <div class="vlt-sidebar-brand">
        <a href="{{ config('veltra.brand.home_url', '/') }}" class="brand-link">
            <img src="{{ config('veltra.brand.logo', asset('vendor/veltra/img/logo.png')) }}" alt="{{ config('veltra.brand.name', 'VeltraUI') }}" class="brand-logo h-7 w-auto shrink-0 object-contain" />
            <span class="brand-text">
                @php
                    $bName = config('veltra.brand.name');
                    $bShort = config('veltra.brand.short_name');
                    $bSuffix = config('veltra.brand.suffix');
                @endphp
                @if($bName && $bName !== 'VeltraUI' && $bName !== 'Veltra')
                    {{ $bName }}
                @elseif($bShort)
                    {{ $bShort }}@if($bSuffix)<span class="text-cyan-500">{{ $bSuffix }}</span>@endif
                @else
                    Veltra<span class="text-cyan-500">UI</span>
                @endif
            </span>
        </a>
    </div>

    <!-- User Panel -->
    <div class="vlt-sidebar-user">
        <div class="relative shrink-0">
            @auth
                <div class="user-avatar bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center text-sm ring-2 ring-cyan-500/30">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
            @else
                <div class="user-avatar bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold flex items-center justify-center text-sm ring-2 ring-cyan-500/30">
                    US
                </div>
            @endauth
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full"></span>
        </div>
        <div class="user-info">
            <p class="user-name">{{ auth()->user()->name ?? 'Usuario Veltra' }}</p>
            <span class="user-role">{{ \Veltra\VeltraLaravel\Support\Navigation::getUserRoleName() }}</span>
        </div>
    </div>

    <!-- Sidebar Search -->
    <div class="px-3 py-2">
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none"></i>
            <input type="text" placeholder="Search menu..." class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 focus:outline-none focus:border-cyan-500 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:bg-slate-950 dark:text-slate-100" />
        </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="vlt-nav">
        @foreach(\Veltra\VeltraLaravel\Support\Navigation::filterAuthorized(config('veltra.navigation', [])) as $item)
            @if(isset($item['header']))
                <li class="vlt-nav-header">{{ $item['header'] }}</li>
            @elseif(isset($item['children']) && is_array($item['children']))
                @php
                    $hasActive = \Veltra\VeltraLaravel\Support\Navigation::hasActiveChild($item['children']);
                @endphp
                <li class="vlt-nav-item {{ $hasActive ? 'is-open' : '' }}">
                    <a class="vlt-nav-link {{ $hasActive ? 'active' : '' }}" data-vlt-toggle="treeview">
                        <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="vlt-nav-icon"></i>
                        <span class="vlt-nav-text">{{ $item['title'] }}</span>
                        <i data-lucide="chevron-right" class="vlt-nav-arrow"></i>
                    </a>
                    <ul class="vlt-nav-treeview">
                        @foreach($item['children'] as $child)
                            @php
                                $childUrl = \Veltra\VeltraLaravel\Support\Navigation::resolveUrl($child);
                                $isChildActive = \Veltra\VeltraLaravel\Support\Navigation::isActive($childUrl);
                            @endphp
                            <li class="vlt-nav-item">
                                <a href="{{ $childUrl }}" class="vlt-nav-link {{ $isChildActive ? 'active font-bold text-cyan-600' : '' }}">
                                    <i data-lucide="{{ $isChildActive ? 'circle-dot' : 'circle' }}" class="w-3.5 h-3.5 {{ $isChildActive ? 'text-cyan-500' : 'text-slate-400' }}"></i>
                                    <span class="vlt-nav-text">{{ $child['title'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            @else
                @php
                    $itemUrl = \Veltra\VeltraLaravel\Support\Navigation::resolveUrl($item);
                    $isActive = \Veltra\VeltraLaravel\Support\Navigation::isActive($itemUrl);
                @endphp
                <li class="vlt-nav-item">
                    <a href="{{ $itemUrl }}" class="vlt-nav-link {{ $isActive ? 'active' : '' }}">
                        <i data-lucide="{{ $item['icon'] ?? 'circle' }}" class="vlt-nav-icon"></i>
                        <span class="vlt-nav-text">{{ $item['title'] }}</span>
                        @if(isset($item['badge']))
                            <span class="vlt-nav-badge vlt-badge vlt-badge-{{ $item['badge']['color'] ?? 'cyan' }}">
                                {{ $item['badge']['text'] }}
                            </span>
                        @endif
                    </a>
                </li>
            @endif
        @endforeach
    </ul>

    <!-- Sidebar Footer Status -->
    <div class="p-3 border-t border-slate-200/80 dark:border-slate-800 mt-auto">
        <div class="flex items-center justify-between text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Online</span>
            </span>
            <span class="font-mono text-[11px] text-slate-400">v1.0.0</span>
        </div>
    </div>
</aside>
