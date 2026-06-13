<x-app-layout>
    <x-slot:title>Mapa de Zonas</x-slot:title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Mapa General de Zonas</h3>
                <p class="text-xs text-gray-400">Visualización de todas las zonas de recolección activas.</p>
            </div>
            <a href="{{ route('admin.zone.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">
                ← Volver al listado
            </a>
        </div>

        <!-- Map + Legend -->
        <div class="flex gap-4">
            <!-- Sidebar list -->
            <div class="w-64 flex-shrink-0 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col" style="max-height: calc(100vh - 200px);">
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <h4 class="text-xs font-bold text-gray-500 uppercase">Zonas ({{ $zones->count() }})</h4>
                </div>
                <div class="overflow-y-auto flex-1 divide-y divide-gray-50">
                    @forelse($zones as $zone)
                    <button onclick="flyToZone({{ $zone->id }})"
                            class="w-full text-left px-4 py-3 hover:bg-gray-50 transition group">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                                  style="background: {{ $zone->status === 'active' ? '#10b981' : '#9ca3af' }}"></span>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-800 truncate group-hover:text-usat-blue">{{ $zone->name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $zone->district?->name }}</div>
                            </div>
                        </div>
                    </button>
                    @empty
                    <div class="p-4 text-center text-xs text-gray-400">No hay zonas con coordenadas</div>
                    @endforelse
                </div>
            </div>

            <!-- Map -->
            <div class="flex-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" style="height: calc(100vh - 200px);">
                <div id="all-zones-map" style="width:100%;height:100%;"></div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    (function() {
        const zonesData = @json($zonesJson);

        const map = L.map('all-zones-map').setView([-6.7714, -79.8409], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const layerMap = {};
        const allBounds = [];

        zonesData.forEach(function(zone) {
            if (zone.coords.length < 3) return;

            const color = zone.status === 'active' ? '#10b981' : '#9ca3af';
            const poly = L.polygon(zone.coords, {
                color: color,
                fillColor: color,
                fillOpacity: 0.2,
                weight: 2
            }).addTo(map);

            poly.bindTooltip(
                `<strong>${zone.name}</strong><br>${zone.district ?? ''}${zone.waste ? '<br>' + zone.waste + ' kg/día' : ''}`,
                { sticky: true, className: 'leaflet-tooltip-custom' }
            );

            layerMap[zone.id] = poly;
            allBounds.push(...zone.coords);
        });

        if (allBounds.length > 0) {
            map.fitBounds(L.latLngBounds(allBounds), { padding: [30, 30] });
        }

        window.flyToZone = function(id) {
            const layer = layerMap[id];
            if (layer) {
                map.fitBounds(layer.getBounds(), { padding: [40, 40] });
                layer.openTooltip();
            }
        };
    })();
    </script>
</x-app-layout>
