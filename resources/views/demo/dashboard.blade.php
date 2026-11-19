<x-veltra::layout.app title="Panel de Analítica" subtitle="Métricas de rendimiento y actividad en tiempo real">

    <x-slot:actions>
        <x-veltra::button variant="outline" icon="download">
            Exportar
        </x-veltra::button>
        <x-veltra::button variant="primary" icon="plus" data-vlt-toggle="modal" data-vlt-target="#modal-demo">
            Nueva Transacción
        </x-veltra::button>
    </x-slot:actions>

    <!-- Alerta de Bienvenida -->
    <x-veltra::alert type="info" title="¡Bienvenido a VeltraLaravel!">
        Tu suite de administración empresarial basada en Tailwind CSS v4 y Vanilla JS ya está lista. Puedes crear nuevas páginas con <code class="font-bold bg-cyan-100 dark:bg-cyan-950/80 px-1.5 py-0.5 rounded text-cyan-800 dark:text-cyan-200">php artisan veltra:make-page MiPanel --template=analytics</code>.
    </x-veltra::alert>

    <!-- 1. Fila de KPI Stat Boxes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <x-veltra::stat-box
            color="cyan"
            title="Nuevas Órdenes"
            value="1,428"
            icon="shopping-bag"
            badge="+14.2%"
            meta="↑ Crecimiento respecto al mes anterior"
            url="#"
        />

        <x-veltra::stat-box
            color="emerald"
            title="Ingresos Totales"
            value="$68,490<span class='text-xs font-normal text-slate-400'>.00</span>"
            icon="dollar-sign"
            badge="Superado"
            meta="↑ 18% sobre la meta proyectada"
            url="#"
        />

        <x-veltra::stat-box
            color="amber"
            title="Tasa de Conversión"
            value="4.85<sup class='text-sm font-normal'>%</sup>"
            icon="pie-chart"
            badge="+0.6%"
            meta="↑ Rendimiento de embudos activos"
            url="#"
        />

        <x-veltra::stat-box
            color="rose"
            title="Tickets Pendientes"
            value="19"
            icon="life-buoy"
            badge="Prioridad"
            meta="↓ Tiempo medio de respuesta: 14 min"
            url="#"
        />
    </div>

    <!-- 2. Sección Principal: Gráfico & Estado -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Tarjeta de Gráfica / Resumen -->
        <div class="lg:col-span-2">
            <x-veltra::card title="Rendimiento de Ventas Semanales" subtitle="Comparativa entre ventas directas y canales de afiliados">
                <div class="h-64 flex flex-col items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-dashed border-slate-200 dark:border-slate-800 p-6 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/40 text-cyan-500 flex items-center justify-center mb-3">
                        <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                    </div>
                    <p class="font-bold text-sm text-slate-800 dark:text-slate-200">Área de Visualización Reactiva</p>
                    <p class="text-xs text-slate-400 max-w-sm mt-1">
                        Puedes incrustar bibliotecas como Chart.js, ApexCharts o componentes Livewire directamente en esta sección.
                    </p>
                </div>
            </x-veltra::card>
        </div>

        <!-- Tarjeta de Desglose de Canales -->
        <div>
            <x-veltra::card title="Distribución de Tráfico" subtitle="Porcentaje por origen de visitante">
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span>Búsqueda Orgánica (Google)</span>
                            <span class="text-cyan-600">48%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-cyan-500 h-full rounded-full" style="width: 48%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span>Campañas Sociales</span>
                            <span class="text-emerald-600">32%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: 32%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span>Directo & Marcadores</span>
                            <span class="text-amber-600">14%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: 14%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span>Referidos Externos</span>
                            <span class="text-rose-600">6%</span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-rose-500 h-full rounded-full" style="width: 6%"></div>
                        </div>
                    </div>
                </div>
            </x-veltra::card>
        </div>
    </div>

    <!-- 3. Tabla de Datos Recientes -->
    <x-veltra::card title="Últimas Transacciones Registradas" subtitle="Movimientos financieros de las últimas 24 horas">
        <x-veltra::table :hoverable="true" :striped="false">
            <x-slot:header>
                <tr>
                    <th class="py-3 px-4">Referencia</th>
                    <th class="py-3 px-4">Cliente</th>
                    <th class="py-3 px-4">Monto</th>
                    <th class="py-3 px-4">Método</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-right">Acción</th>
                </tr>
            </x-slot:header>

            <tr>
                <td class="py-3 px-4 font-mono text-xs font-bold text-cyan-600">#ORD-9842</td>
                <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-100">TechCorp Solutions</td>
                <td class="py-3 px-4 font-mono font-bold">$1,250.00</td>
                <td class="py-3 px-4">Tarjeta Débito</td>
                <td class="py-3 px-4"><x-veltra::badge color="emerald" :dot="true">Completado</x-veltra::badge></td>
                <td class="py-3 px-4 text-right"><x-veltra::button variant="ghost" size="sm" icon="eye">Ver</x-veltra::button></td>
            </tr>
            <tr>
                <td class="py-3 px-4 font-mono text-xs font-bold text-cyan-600">#ORD-9841</td>
                <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-100">Distribuidora Global SAC</td>
                <td class="py-3 px-4 font-mono font-bold">$4,820.00</td>
                <td class="py-3 px-4">Transferencia Bancaria</td>
                <td class="py-3 px-4"><x-veltra::badge color="amber" :dot="true">En Proceso</x-veltra::badge></td>
                <td class="py-3 px-4 text-right"><x-veltra::button variant="ghost" size="sm" icon="eye">Ver</x-veltra::button></td>
            </tr>
            <tr>
                <td class="py-3 px-4 font-mono text-xs font-bold text-cyan-600">#ORD-9840</td>
                <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-100">María Santos Lozano</td>
                <td class="py-3 px-4 font-mono font-bold">$189.50</td>
                <td class="py-3 px-4">PayPal</td>
                <td class="py-3 px-4"><x-veltra::badge color="emerald" :dot="true">Completado</x-veltra::badge></td>
                <td class="py-3 px-4 text-right"><x-veltra::button variant="ghost" size="sm" icon="eye">Ver</x-veltra::button></td>
            </tr>
        </x-veltra::table>
    </x-veltra::card>

    <!-- Modal de Demostración -->
    <x-veltra::modal id="modal-demo" title="Nueva Transacción de Prueba" size="md">
        <form class="space-y-4">
            <x-veltra::form.input name="customer" label="Nombre del Cliente" placeholder="Ej: Acme Corp" icon="user" required />
            <x-veltra::form.input name="amount" type="number" label="Monto ($)" placeholder="0.00" icon="dollar-sign" required />
            <x-veltra::form.select name="gateway" label="Pasarela de Pago" :options="['stripe' => 'Stripe', 'paypal' => 'PayPal', 'wire' => 'Transferencia Bancaria']" />
            <x-veltra::form.toggle name="notify" label="Enviar recibo automático por correo al cliente" checked />
        </form>

        <x-slot:footer>
            <x-veltra::button variant="outline" data-vlt-dismiss="modal">Cancelar</x-veltra::button>
            <x-veltra::button variant="primary" data-vlt-dismiss="modal">Guardar Transacción</x-veltra::button>
        </x-slot:footer>
    </x-veltra::modal>

</x-veltra::layout.app>

