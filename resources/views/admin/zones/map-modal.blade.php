<x-app-layout>
<turbo-frame id="modal" data-modal-size="max-w-6xl">
<div data-modal-size="max-w-6xl" class="flex flex-col md:flex-row max-h-[90vh]">

    {{-- Panel izquierdo: información --}}
    <div class="w-full md:w-80 flex-shrink-0 overflow-y-auto bg-white border-r border-gray-100 p-6 space-y-5">
        {{-- Header --}}
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-usat-blue text-base leading-tight">{{ $zone->name }}</h3>
                    <p class="text-[11px] text-gray-400">Zona de recolección</p>
                </div>
            </div>
            <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Ubicación --}}
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>{{ $zone->district?->name }}</span>
        </div>

        {{-- Stats grid --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-blue-50 rounded-xl p-3 text-center">
                <div class="text-lg font-extrabold text-usat-blue">{{ $zone->zoneCoords->count() }}</div>
                <div class="text-[10px] text-blue-400 font-semibold uppercase">Puntos</div>
            </div>
            <div class="bg-emerald-50 rounded-xl p-3 text-center">
                <div class="text-lg font-extrabold text-emerald-700">
                    {{ $zone->avg_waste_kg ? number_format($zone->avg_waste_kg, 1).' kg' : 'N/A' }}
                </div>
                <div class="text-[10px] text-emerald-500 font-semibold uppercase">Residuos/día</div>
            </div>
            <div class="bg-gray-50 rounded-xl p-3 text-center">
                <div class="text-sm font-bold text-gray-700 leading-tight">{{ $zone->district?->province?->department?->name ?? '—' }}</div>
                <div class="text-[10px] text-gray-400 font-semibold uppercase">Departamento</div>
            </div>
            <div class="bg-purple-50 rounded-xl p-3 text-center">
                <div class="text-sm font-bold text-purple-700 leading-tight">
                    {{ $zone->area ? number_format((float)$zone->area, 4).' km²' : 'N/A' }}
                </div>
                <div class="text-[10px] text-purple-400 font-semibold uppercase">Área</div>
            </div>
        </div>

        {{-- Descripción --}}
        @if($zone->description)
        <div>
            <h4 class="text-[10px] font-bold uppercase text-gray-400 mb-1">Descripción de la zona</h4>
            <p class="text-xs text-gray-600 leading-relaxed">{{ $zone->description }}</p>
        </div>
        @endif

        {{-- Coordenadas --}}
        <div>
            <h4 class="text-[10px] font-bold uppercase text-gray-400 mb-2">Coordenadas del Polígono</h4>
            <div class="overflow-hidden rounded-xl border border-gray-100">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 text-gray-400 uppercase text-[9px] font-bold">
                        <tr>
                            <th class="py-2 px-3 text-center">#</th>
                            <th class="py-2 px-3">Latitud</th>
                            <th class="py-2 px-3">Longitud</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($zone->zoneCoords as $i => $coord)
                        <tr class="hover:bg-gray-50">
                            <td class="py-1.5 px-3 text-center text-gray-400">{{ $i + 1 }}</td>
                            <td class="py-1.5 px-3 font-mono text-gray-700">{{ number_format((float)$coord->latitude, 6) }}</td>
                            <td class="py-1.5 px-3 font-mono text-gray-700">{{ number_format((float)$coord->longitude, 6) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Panel derecho: mapa --}}
    <div class="flex-1 flex flex-col min-h-[450px]">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <svg class="w-4 h-4 text-usat-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
            </svg>
            <span class="text-xs font-bold text-usat-blue uppercase tracking-wide">Visualización en Mapa</span>
        </div>
        <div id="zone-view-map" class="flex-1" style="min-height: 450px;"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    const coords = @json($zone->zoneCoords->map(fn($c) => [(float)$c->latitude, (float)$c->longitude]));
    const zoneName = @json($zone->name);
    const districtName = @json($zone->district?->name ?? '');
    const waste = @json($zone->avg_waste_kg ? number_format((float)$zone->avg_waste_kg, 1).' kg' : 'N/A');

    function initMap() {
        const mapEl = document.getElementById('zone-view-map');
        if (!mapEl || mapEl._leaflet_id) return;

        const map = L.map('zone-view-map');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        if (window.ResizeObserver) {
            new ResizeObserver(() => {
                map.invalidateSize();
            }).observe(mapEl);
        }

        if (coords.length >= 3) {
            const polygon = L.polygon(coords, {
                color: '#10b981',
                fillColor: '#10b981',
                fillOpacity: 0.25,
                weight: 2.5
            }).addTo(map);

            const center = polygon.getBounds().getCenter();
            const label = L.divIcon({
                className: '',
                html: `<div style="background:rgba(30,64,175,0.9);color:#fff;padding:6px 10px;border-radius:8px;font-size:11px;font-weight:700;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,0.3)">
                    ${zoneName}<br>
                    <span style="font-weight:400;font-size:10px;opacity:.85">${districtName} · ${waste}</span>
                </div>`,
                iconAnchor: [0, 0]
            });
            L.marker(center, { icon: label }).addTo(map);
            map.fitBounds(polygon.getBounds(), { padding: [30, 30] });
        } else if (coords.length > 0) {
            map.setView(coords[0], 14);
            coords.forEach(c => L.marker(c).addTo(map));
        } else {
            map.setView([-6.7714, -79.8409], 12);
        }
    }

    // Initialize immediately (script runs after DOM content in turbo-frame)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        setTimeout(initMap, 50);
    }
})();
</script>
</turbo-frame>
</x-app-layout>
