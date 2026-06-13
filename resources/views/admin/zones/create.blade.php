<x-app-layout>
    <x-slot:title>Nueva Zona</x-slot:title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@2.16.0/dist/leaflet-geoman.css"/>
    <style>
        .leaflet-pm-toolbar { display:none !important; }
    </style>

    <div x-data="zoneForm()" class="space-y-4">

        <div class="flex items-center justify-between bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-usat-blue">Registrar Nueva Zona</h3>
                <p class="text-xs text-gray-400">Dibuja el perímetro en el mapa · Arrastra los vértices para ajustar</p>
            </div>
            <a href="{{ route('admin.zone.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition">← Volver</a>
        </div>

        <form action="{{ route('admin.zone.store') }}" method="POST" @submit.prevent="submitForm($event)">
            @csrf
            <template x-for="(c, i) in coords" :key="i">
                <div>
                    <input type="hidden" :name="`coords[${i}][latitude]`"  :value="c.lat">
                    <input type="hidden" :name="`coords[${i}][longitude]`" :value="c.lng">
                </div>
            </template>
            <input type="hidden" name="area" x-model="areaKm2">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Panel izquierdo: datos --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <h4 class="text-sm font-bold text-gray-700 border-b border-gray-100 pb-3">Información General</h4>

                    <div>
                        <x-input-label for="name" value="Nombre de la Zona *"/>
                        <x-text-input id="name" name="name" type="text" value="{{ old('name') }}"
                            placeholder="Ej: Zona Norte Centro" class="block mt-1 w-full text-sm" required autofocus/>
                        <x-input-error :messages="$errors->get('name')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label value="Departamento *"/>
                        <select x-model="deptId" @change="loadProvinces()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" required>
                            <option value="">— Seleccionar —</option>
                            @foreach($departments as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Provincia *"/>
                        <select x-model="provId" @change="loadDistricts()" :disabled="!provinces.length"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm disabled:opacity-50" required>
                            <option value="">— Seleccionar departamento —</option>
                            <template x-for="p in provinces" :key="p.id">
                                <option :value="p.id" x-text="p.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <x-input-label value="Distrito *"/>
                        <select name="district_id" :disabled="!districts.length"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm disabled:opacity-50" required>
                            <option value="">— Seleccionar provincia —</option>
                            <template x-for="d in districts" :key="d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                        <x-input-error :messages="$errors->get('district_id')" class="mt-1"/>
                    </div>

                    <div>
                        <x-input-label for="description" value="Descripción"/>
                        <textarea id="description" name="description" rows="3"
                            placeholder="Características o notas de la zona..."
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="avg_waste_kg" value="Residuos Promedio (kg/día)"/>
                        <x-text-input id="avg_waste_kg" name="avg_waste_kg" type="number" step="0.01" min="0"
                            value="{{ old('avg_waste_kg') }}" placeholder="Ej: 150.50" class="block mt-1 w-full text-sm"/>
                    </div>

                    <div>
                        <x-input-label for="status" value="Estado *"/>
                        <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 text-sm" required>
                            <option value="active" selected>Activo</option>
                            <option value="inactive">Inactivo</option>
                        </select>
                    </div>

                    @if($errors->has('coords') || $errors->has('coords.*'))
                        <p class="text-sm text-red-600 font-medium">{{ $errors->first('coords') }}</p>
                    @endif

                    <div class="flex gap-3 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.zone.index') }}"
                            class="flex-1 text-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="flex-1 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                            Guardar Zona
                        </button>
                    </div>
                </div>

                {{-- Panel derecho: mapa --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">

                    {{-- Barra de acciones del mapa --}}
                    <div class="p-3 border-b border-gray-100 flex items-center gap-2 flex-wrap">
                        <div class="flex gap-1.5 flex-1 min-w-0">
                            <x-text-input type="text" id="addr-search"
                                placeholder="Buscar dirección para centrar el mapa..."
                                class="flex-1 text-xs min-w-0" @keydown.enter.prevent="searchAddress()"/>
                            <button type="button" @click="searchAddress()"
                                class="px-3 py-1.5 bg-usat-blue hover:bg-blue-800 text-white text-xs font-bold rounded-lg transition whitespace-nowrap">
                                Buscar
                            </button>
                        </div>
                        <div class="flex gap-1.5 flex-shrink-0">
                            <button type="button" @click="startDrawing()"
                                x-show="!isDrawing && !drawnLayer"
                                class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Dibujar zona
                            </button>
                            <button type="button" @click="cancelDrawing()"
                                x-show="isDrawing"
                                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition">
                                ✕ Cancelar
                            </button>
                            <button type="button" @click="redraw()"
                                x-show="!isDrawing && drawnLayer"
                                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Redibujar
                            </button>
                        </div>
                    </div>

                    {{-- Resultados búsqueda --}}
                    <div x-show="addrResults.length" class="border-b border-gray-100 bg-gray-50 max-h-28 overflow-y-auto">
                        <template x-for="r in addrResults" :key="r.place_id">
                            <button type="button" @click="flyTo(r)"
                                class="w-full text-left px-3 py-2 text-xs hover:bg-emerald-50 border-b border-gray-100 last:border-0 truncate">
                                <span x-text="r.display_name"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Mapa --}}
                    <div class="relative">
                        <div id="draw-map" style="height:380px;"></div>

                        {{-- Instrucción durante el dibujo --}}
                        <div x-show="isDrawing" x-transition
                            class="absolute top-3 left-1/2 -translate-x-1/2 bg-emerald-700/90 text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg z-[1000] pointer-events-none whitespace-nowrap">
                            Clic para agregar puntos · Doble clic para terminar
                        </div>

                        {{-- Badge área --}}
                        <div x-show="coords.length >= 3" x-transition
                            class="absolute bottom-3 left-3 bg-white/95 rounded-xl shadow px-3 py-1.5 z-[1000] text-xs font-semibold text-gray-700 pointer-events-none">
                            Área: <span class="text-emerald-600" x-text="areaHa + ' ha'"></span>
                            <span class="text-gray-400 ml-1">(<span x-text="coords.length"></span> pts)</span>
                        </div>

                        {{-- Hint inicial --}}
                        <div x-show="!isDrawing && !drawnLayer"
                            class="absolute inset-0 flex items-center justify-center pointer-events-none z-[500]">
                            <div class="bg-white/85 backdrop-blur rounded-2xl px-6 py-3 text-center shadow-lg">
                                <p class="text-sm font-bold text-gray-600">Presiona "Dibujar zona"</p>
                                <p class="text-xs text-gray-400 mt-0.5">para marcar el perímetro en el mapa</p>
                            </div>
                        </div>

                        {{-- Hint de edición --}}
                        <div x-show="!isDrawing && drawnLayer" x-transition
                            class="absolute bottom-3 right-3 bg-white/90 rounded-xl shadow px-2.5 py-1.5 z-[1000] text-[10px] text-gray-500 pointer-events-none leading-snug max-w-[200px] text-right">
                            <span class="font-semibold text-gray-700">Editar:</span> arrastra vértices · clic en el borde para añadir · clic derecho en vértice para quitar
                        </div>
                    </div>

                    {{-- Lista de vértices --}}
                    <div class="border-t border-gray-100 overflow-y-auto" style="max-height:200px;">
                        <div class="px-4 py-2.5 flex items-center justify-between sticky top-0 bg-white z-10 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                                Vértices <span class="font-normal text-gray-400">(<span x-text="coords.length"></span>)</span>
                            </span>
                            <button type="button" @click="clearAll()" x-show="drawnLayer"
                                class="text-xs text-red-400 hover:text-red-600 font-semibold transition">
                                Eliminar polígono
                            </button>
                        </div>
                        <div x-show="!coords.length" class="px-4 py-5 text-center text-xs text-gray-400 italic">
                            Sin vértices — dibuja el polígono en el mapa
                        </div>
                        <table x-show="coords.length" class="w-full text-xs">
                            <thead class="bg-gray-50 text-gray-400 text-[10px] uppercase font-bold">
                                <tr>
                                    <th class="py-1.5 px-3 text-center w-8">#</th>
                                    <th class="py-1.5 px-3">Latitud</th>
                                    <th class="py-1.5 px-3">Longitud</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(c, i) in coords" :key="i">
                                    <tr class="border-t border-gray-50 hover:bg-gray-50">
                                        <td class="py-1 px-3 text-center font-bold text-emerald-600" x-text="i+1"></td>
                                        <td class="py-1 px-3 font-mono text-gray-600" x-text="c.lat"></td>
                                        <td class="py-1 px-3 font-mono text-gray-600" x-text="c.lng"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@2.16.0/dist/leaflet-geoman.min.js"></script>
    <script>
    function zoneForm() {
        return {
            deptId: '', provId: '',
            provinces: [], districts: [],
            coords: [],
            drawnLayer: null,
            isDrawing:  false,
            areaHa: '0.00', areaKm2: '',
            addrResults: [],
            map: null,

            init() {
                this.$nextTick(() => this.initMap());
            },

            initMap() {
                this.map = L.map('draw-map').setView([-6.7714, -79.8409], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors', maxZoom: 19
                }).addTo(this.map);

                this.map.pm.setLang('es');
                this.loadRefZones();

                // Polígono terminado de dibujar
                this.map.on('pm:create', (e) => {
                    this.drawnLayer = e.layer;
                    this.isDrawing  = false;
                    this.drawnLayer.setStyle({ color:'#10b981', fillColor:'#10b981', fillOpacity:0.2, weight:2.5 });

                    // Activar edición de vértices inmediatamente
                    this.drawnLayer.pm.enable({ allowSelfIntersection: false });

                    // Sincronizar en cada cambio (mover / agregar / quitar vértice)
                    this.drawnLayer.on('pm:change', () => this._syncCoords());
                    this._syncCoords();
                });
            },

            loadRefZones() {
                fetch('{{ route('admin.zones.geojson') }}')
                    .then(r => r.json())
                    .then(data => {
                        L.geoJSON(data, {
                            style: { color:'#3b82f6', fillColor:'#3b82f6', fillOpacity:0.07, weight:1.5, dashArray:'5' },
                            onEachFeature: (f, l) => l.bindTooltip(f.properties.name, { sticky:true })
                        }).addTo(this.map);
                    }).catch(() => {});
            },

            startDrawing() {
                this.map.pm.enableDraw('Polygon', {
                    snappable: false,
                    finishOn: 'dblclick',
                    pathOptions: { color:'#10b981', fillColor:'#10b981', fillOpacity:0.12, weight:2, dashArray:'5' }
                });
                this.isDrawing = true;
            },

            cancelDrawing() {
                this.map.pm.disableDraw();
                this.isDrawing = false;
            },

            redraw() {
                if (this.drawnLayer) {
                    this.drawnLayer.pm.disable();
                    this.map.removeLayer(this.drawnLayer);
                    this.drawnLayer = null;
                }
                this.coords  = [];
                this.areaHa  = '0.00';
                this.areaKm2 = '';
                this.startDrawing();
            },

            clearAll() {
                if (!confirm('¿Eliminar el polígono dibujado?')) return;
                if (this.drawnLayer) {
                    this.drawnLayer.pm.disable();
                    this.map.removeLayer(this.drawnLayer);
                    this.drawnLayer = null;
                }
                this.coords  = [];
                this.areaHa  = '0.00';
                this.areaKm2 = '';
            },

            _syncCoords() {
                if (!this.drawnLayer) return;
                const lls = this.drawnLayer.getLatLngs()[0];
                this.coords = lls.map(ll => ({
                    lat: ll.lat.toFixed(6),
                    lng: ll.lng.toFixed(6)
                }));
                if (this.coords.length >= 3) {
                    const pts = this.coords.map(c => [parseFloat(c.lat), parseFloat(c.lng)]);
                    const a   = this._calcArea(pts);
                    this.areaHa  = (a * 100).toFixed(2);
                    this.areaKm2 = a.toFixed(6);
                } else {
                    this.areaHa = '0.00'; this.areaKm2 = '';
                }
            },

            _calcArea(pts) {
                if (pts.length < 3) return 0;
                const cLat  = pts.reduce((s, p) => s + p[0], 0) / pts.length;
                const latKm = 111.32;
                const lngKm = 111.32 * Math.cos(cLat * Math.PI / 180);
                let area = 0, n = pts.length;
                for (let i = 0; i < n; i++) {
                    const j = (i + 1) % n;
                    area += (pts[i][1] * lngKm) * (pts[j][0] * latKm);
                    area -= (pts[j][1] * lngKm) * (pts[i][0] * latKm);
                }
                return Math.abs(area / 2);
            },

            searchAddress() {
                const q = document.getElementById('addr-search').value.trim();
                if (!q) return;
                fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=5&countrycodes=pe`)
                    .then(r => r.json()).then(d => { this.addrResults = d; }).catch(() => {});
            },

            flyTo(r) {
                this.map.setView([parseFloat(r.lat), parseFloat(r.lon)], 16);
                this.addrResults = [];
                document.getElementById('addr-search').value = '';
            },

            loadProvinces() {
                this.provId = ''; this.districts = [];
                if (!this.deptId) { this.provinces = []; return; }
                fetch("{{ url('geo/provinces') }}/" + this.deptId).then(r => r.json()).then(d => { this.provinces = d; });
            },

            loadDistricts() {
                if (!this.provId) { this.districts = []; return; }
                fetch("{{ url('geo/districts') }}/" + this.provId).then(r => r.json()).then(d => { this.districts = d; });
            },

            submitForm(e) {
                if (this.coords.length < 3) {
                    alert('Necesitas al menos 3 vértices para definir el perímetro.\nUsa el botón "Dibujar zona" para marcarlo en el mapa.');
                    return;
                }
                e.target.submit();
            }
        };
    }
    </script>
</x-app-layout>
