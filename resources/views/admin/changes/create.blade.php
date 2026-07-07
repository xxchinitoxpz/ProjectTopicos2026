<x-app-layout>
    <x-slot:title>
        Cambio Masivo
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-7xl">
        <div data-modal-size="max-w-7xl" class="w-full max-w-7xl mx-auto overflow-hidden rounded-sm bg-white shadow-2xl">
            <div class="flex items-center justify-between bg-blue-900 px-5 py-4 text-white">
                <div class="flex items-center gap-3">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V8m0 0l-4 4m4-4l4 4m10-4H9m8 8H9" />
                    </svg>
                    <h3 class="text-2xl font-bold">Cambio Masivo</h3>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.change.index') }}'" class="text-white/80 transition hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.change.store') }}" method="POST" class="p-6"
                  data-turbo="false" data-turbo-frame="_top"
                  x-ref="changeForm"
                  x-data="{
                      changeType: '{{ old('change_type', 'turno') }}',
                      reasonType: @js(old('reason_type', '')),
                      additionalDescription: @js(old('additional_description', '')),
                      oldTargetId: @js((string) old('old_target_id', '')),
                      newTargetId: @js((string) old('new_target_id', '')),
                      summaryOldTargetLabel: '',
                      summaryNewTargetLabel: '',
                      confirmOpen: false,
                      planningDaysCount: {{ (int) $planningDaysCount }},
                      zoneSearch: '',
                      zonesOpen: false,
                      zones: @js($zones->map(fn ($zone) => ['id' => $zone->id, 'name' => $zone->name])->values()),
                      selectedZoneIds: @js(collect(old('zone_ids', []))->map(fn ($id) => (int) $id)->values()),
                      get fullDescription() {
                          const reason = (this.reasonType || '').trim();
                          const extra = (this.additionalDescription || '').trim();
                          if (reason && extra) return `${reason} - ${extra}`;
                          return reason || extra;
                      },
                      get filteredZones() {
                          const term = this.zoneSearch.trim().toLowerCase();
                          return this.zones.filter(zone => {
                              const alreadySelected = this.selectedZoneIds.includes(zone.id);
                              if (alreadySelected) return false;
                              if (!term) return true;
                              return zone.name.toLowerCase().includes(term);
                          }).slice(0, 8);
                      },
                      get selectedZones() {
                          return this.zones.filter(zone => this.selectedZoneIds.includes(zone.id));
                      },
                      get selectedZoneNames() {
                          if (!this.selectedZones.length) {
                              return 'Todas las zonas';
                          }

                          return this.selectedZones.map(zone => zone.name).join(', ');
                      },
                      get changeTypeLabel() {
                          return {
                              turno: 'Cambio de Turno',
                              vehiculo: 'Cambio de Vehiculo',
                              conductor: 'Cambio de Conductor',
                          }[this.changeType] ?? 'Cambio de Programacion';
                      },
                      get activeOldTargetField() {
                          return document.getElementById({
                              turno: 'old_shift_id',
                              vehiculo: 'old_vehicle_id',
                              conductor: 'old_driver_id',
                          }[this.changeType]);
                      },
                      get activeNewTargetField() {
                          return document.getElementById({
                              turno: 'new_shift_id',
                              vehiculo: 'new_vehicle_id',
                              conductor: 'new_driver_id',
                          }[this.changeType]);
                      },
                      openConfirmation() {
                          if (this.planningDaysCount === 0) {
                              alert('No existen programaciones registradas para modificar. Primero registre programaciones.');
                              return;
                          }

                          const oldField = this.activeOldTargetField;
                          const newField = this.activeNewTargetField;

                          if (newField) {
                              newField.setCustomValidity('');
                          }

                          if (!this.$refs.changeForm.reportValidity()) {
                              return;
                          }

                          if (oldField?.value && newField?.value && oldField.value === newField.value) {
                              newField.setCustomValidity('Seleccione un recurso diferente al que se reemplazara.');
                              newField.reportValidity();
                              return;
                          }

                          this.summaryOldTargetLabel = oldField?.selectedOptions?.[0]?.text?.trim() || '';
                          this.summaryNewTargetLabel = newField?.selectedOptions?.[0]?.text?.trim() || '';
                          this.confirmOpen = true;
                      },
                      addZone(zone) {
                          if (!this.selectedZoneIds.includes(zone.id)) {
                              this.selectedZoneIds.push(zone.id);
                          }
                          this.zoneSearch = '';
                          this.zonesOpen = false;
                      },
                      removeZone(zoneId) {
                          this.selectedZoneIds = this.selectedZoneIds.filter(id => id !== zoneId);
                      }
                  }"
                  x-init="$nextTick(() => { if (!@js(old('full_description'))) { $refs.fullDescription.value = fullDescription; } })"
                  x-effect="$refs.fullDescription.value = fullDescription">
                @csrf

                @if($planningDaysCount === 0)
                    <div class="mb-5 rounded-sm border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                        No existen programaciones registradas para modificar. Primero registre programaciones y luego aplique un cambio masivo.
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="date_start" class="mb-2 block text-[15px] font-bold text-gray-800">Fecha de Inicio *</label>
                        <input id="date_start" x-ref="dateStart" type="date" name="date_start" value="{{ old('date_start', now()->toDateString()) }}" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200" required>
                        <x-input-error :messages="$errors->get('date_start')" class="mt-1" />
                    </div>

                    <div>
                        <label for="date_end" class="mb-2 block text-[15px] font-bold text-gray-800">Fecha de Fin *</label>
                        <input id="date_end" x-ref="dateEnd" type="date" name="date_end" value="{{ old('date_end', now()->toDateString()) }}" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200" required>
                        <x-input-error :messages="$errors->get('date_end')" class="mt-1" />
                    </div>

                    <div class="relative">
                        <label for="zone_search" class="mb-2 block text-[15px] font-bold text-gray-800">Zonas (Opcional)</label>
                        <div class="min-h-[3rem] w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:border-sky-500 focus-within:ring-1 focus-within:ring-sky-200">
                            <div class="mb-2 flex flex-wrap gap-2" x-show="selectedZones.length">
                                <template x-for="zone in selectedZones" :key="zone.id">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-sky-500 px-3 py-1 text-sm font-semibold text-white">
                                        <span x-text="zone.name"></span>
                                        <button type="button" @click="removeZone(zone.id)" class="text-white/90 hover:text-white">&times;</button>
                                    </span>
                                </template>
                            </div>
                            <input id="zone_search" type="text" x-model="zoneSearch" @focus="zonesOpen = true" @input="zonesOpen = true" @click.away="zonesOpen = false" class="w-full border-0 p-0 text-[15px] text-gray-700 focus:ring-0" placeholder="Buscar zonas...">
                            <div x-show="zonesOpen && filteredZones.length" class="absolute left-0 right-0 top-full z-20 mt-1 max-h-56 overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg" style="display: none;">
                                <template x-for="zone in filteredZones" :key="zone.id">
                                    <button type="button" @click="addZone(zone)" class="flex w-full items-center justify-between px-3 py-2 text-left text-sm text-gray-700 transition hover:bg-sky-50">
                                        <span x-text="zone.name"></span>
                                        <span class="text-sky-600">Agregar</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <template x-for="zoneId in selectedZoneIds" :key="zoneId">
                            <input type="hidden" name="zone_ids[]" :value="zoneId">
                        </template>
                        <p class="mt-2 text-sm text-gray-500">Dejar vacio para aplicar a todas las zonas</p>
                        <x-input-error :messages="$errors->get('zone_ids')" class="mt-1" />
                    </div>

                    <div>
                        <label for="change_type" class="mb-2 block text-[15px] font-bold text-gray-800">Tipo de Cambio *</label>
                        <select id="change_type" name="change_type" x-model="changeType" @change="oldTargetId = ''; newTargetId = ''; activeNewTargetField?.setCustomValidity('')" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200" required>
                            <option value="turno">Cambio de Turno</option>
                            <option value="vehiculo">Cambio de Vehiculo</option>
                            <option value="conductor">Cambio de Conductor</option>
                        </select>
                        <x-input-error :messages="$errors->get('change_type')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div x-show="changeType === 'turno'">
                        <label for="old_shift_id" class="mb-2 block text-[15px] font-bold text-gray-800">Turno a Reemplazar *</label>
                        <select id="old_shift_id" name="old_target_id" x-model="oldTargetId" x-bind:disabled="changeType !== 'turno'" x-bind:required="changeType === 'turno'" @change="activeNewTargetField?.setCustomValidity(''); if (newTargetId === oldTargetId) newTargetId = ''" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                            <option value="">Seleccione un turno</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" @selected(old('old_target_id') == $shift->id)>{{ $shift->name }}@if($shift->check_in && $shift->check_out) ({{ $shift->check_in }} - {{ $shift->check_out }}) @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="changeType === 'turno'">
                        <label for="new_shift_id" class="mb-2 block text-[15px] font-bold text-gray-800">Nuevo Turno *</label>
                        <select id="new_shift_id" name="new_target_id" x-model="newTargetId" x-bind:disabled="changeType !== 'turno'" x-bind:required="changeType === 'turno'" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                            <option value="">Seleccione un turno</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}" x-bind:disabled="changeType === 'turno' && oldTargetId === '{{ $shift->id }}'" @selected(old('new_target_id') == $shift->id)>{{ $shift->name }}@if($shift->check_in && $shift->check_out) ({{ $shift->check_in }} - {{ $shift->check_out }}) @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="changeType === 'vehiculo'">
                        <label for="old_vehicle_id" class="mb-2 block text-[15px] font-bold text-gray-800">Vehiculo a Reemplazar *</label>
                        <select id="old_vehicle_id" name="old_target_id" x-model="oldTargetId" x-bind:disabled="changeType !== 'vehiculo'" x-bind:required="changeType === 'vehiculo'" @change="activeNewTargetField?.setCustomValidity(''); if (newTargetId === oldTargetId) newTargetId = ''" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                            <option value="">Seleccione un vehiculo</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" @selected(old('old_target_id') == $vehicle->id)>{{ $vehicle->code }}{{ $vehicle->name ? ' - ' . $vehicle->name : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="changeType === 'vehiculo'">
                        <label for="new_vehicle_id" class="mb-2 block text-[15px] font-bold text-gray-800">Nuevo Vehiculo *</label>
                        <select id="new_vehicle_id" name="new_target_id" x-model="newTargetId" x-bind:disabled="changeType !== 'vehiculo'" x-bind:required="changeType === 'vehiculo'" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                            <option value="">Seleccione un vehiculo</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" x-bind:disabled="changeType === 'vehiculo' && oldTargetId === '{{ $vehicle->id }}'" @selected(old('new_target_id') == $vehicle->id)>{{ $vehicle->code }}{{ $vehicle->name ? ' - ' . $vehicle->name : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="changeType === 'conductor'">
                        <label for="old_driver_id" class="mb-2 block text-[15px] font-bold text-gray-800">Conductor a Reemplazar *</label>
                        <select id="old_driver_id" name="old_target_id" x-model="oldTargetId" x-bind:disabled="changeType !== 'conductor'" x-bind:required="changeType === 'conductor'" @change="activeNewTargetField?.setCustomValidity(''); if (newTargetId === oldTargetId) newTargetId = ''" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                            <option value="">Seleccione un conductor</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" @selected(old('old_target_id') == $driver->id)>{{ $driver->name }} {{ $driver->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="changeType === 'conductor'">
                        <label for="new_driver_id" class="mb-2 block text-[15px] font-bold text-gray-800">Nuevo Conductor *</label>
                        <select id="new_driver_id" name="new_target_id" x-model="newTargetId" x-bind:disabled="changeType !== 'conductor'" x-bind:required="changeType === 'conductor'" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200">
                            <option value="">Seleccione un conductor</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" x-bind:disabled="changeType === 'conductor' && oldTargetId === '{{ $driver->id }}'" @selected(old('new_target_id') == $driver->id)>{{ $driver->name }} {{ $driver->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <x-input-error :messages="$errors->get('old_target_id')" class="mt-2" />
                <x-input-error :messages="$errors->get('new_target_id')" class="mt-1" />

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="reason_type" class="mb-2 block text-[15px] font-bold text-gray-800">Motivo Predefinido *</label>
                        <select id="reason_type" name="reason_type" x-model="reasonType" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200" required>
                            <option value="">Seleccione un motivo</option>
                            @foreach($motives as $motive)
                                <option value="{{ $motive->name }}" @selected(old('reason_type') === $motive->name)>{{ $motive->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Seleccione un motivo predefinido para el cambio</p>
                        <x-input-error :messages="$errors->get('reason_type')" class="mt-1" />
                    </div>

                    <div>
                        <label for="additional_description" class="mb-2 block text-[15px] font-bold text-gray-800">Descripcion Adicional (Opcional)</label>
                        <input id="additional_description" type="text" name="additional_description" x-model="additionalDescription" value="{{ old('additional_description') }}" class="block h-12 w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200" placeholder="Complemento al motivo predefinido">
                        <p class="mt-2 text-sm text-gray-500">Complemento al motivo predefinido</p>
                        <x-input-error :messages="$errors->get('additional_description')" class="mt-1" />
                    </div>
                </div>

                <div class="mt-6">
                    <label for="full_description" class="mb-2 block text-[15px] font-bold text-gray-800">Descripcion Completa del Cambio *</label>
                    <textarea id="full_description" name="full_description" rows="4" x-ref="fullDescription" class="block w-full rounded-md border-gray-300 text-[15px] text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-200" required>{{ old('full_description') }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">Este campo se completa automaticamente con el motivo seleccionado + detalles adicionales</p>
                    <x-input-error :messages="$errors->get('full_description')" class="mt-1" />
                </div>

                <input type="hidden" name="details" x-bind:value="fullDescription">

                <div class="mt-8 flex items-center justify-end gap-3">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.change.index') }}'" class="inline-flex items-center rounded-md bg-red-500 px-5 py-3 text-base font-semibold text-white transition hover:bg-red-600">
                        <svg class="me-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </button>
                    <button type="button" @click="openConfirmation()" class="inline-flex items-center rounded-md bg-sky-600 px-5 py-3 text-base font-semibold text-white transition hover:bg-sky-700">
                        <svg class="me-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Guardar
                    </button>
                </div>

                <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 px-4 py-8" style="display: none;">
                    <div @click.outside="confirmOpen = false" class="w-full max-w-5xl overflow-hidden rounded-sm bg-white shadow-2xl">
                        <div class="border-b border-gray-200 px-6 py-5 text-center">
                            <h4 class="text-2xl font-bold text-blue-900">Resumen de la operacion</h4>
                            <p class="mt-1 text-base text-gray-500">Revise cuidadosamente los detalles antes de proceder</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 px-6 py-6 lg:grid-cols-2">
                            <div class="overflow-hidden rounded-sm border border-gray-200">
                                <div class="bg-blue-900 px-5 py-4 text-xl font-bold text-white">
                                    Configuracion General
                                </div>
                                <div class="space-y-5 px-5 py-5 text-gray-700">
                                    <div class="border-b border-gray-200 pb-4">
                                        <div class="text-base font-semibold text-gray-500">Tipo de Cambio</div>
                                        <div class="mt-2 text-3xl font-semibold text-blue-900" x-text="changeTypeLabel"></div>
                                    </div>

                                    <div class="border-b border-gray-200 pb-4">
                                        <div class="text-base font-semibold text-gray-500">Periodo</div>
                                        <div class="mt-2 flex justify-between gap-4 text-2xl">
                                            <span>Inicio:</span>
                                            <span class="font-semibold text-emerald-600" x-text="$refs.dateStart?.value || '-'"></span>
                                        </div>
                                        <div class="flex justify-between gap-4 text-2xl">
                                            <span>Fin:</span>
                                            <span class="font-semibold text-emerald-600" x-text="$refs.dateEnd?.value || '-'"></span>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="text-base font-semibold text-gray-500">Ambito de Aplicacion</div>
                                        <div class="mt-2 text-3xl font-semibold text-amber-500" x-text="selectedZoneNames"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-hidden rounded-sm border border-gray-200">
                                <div class="bg-sky-600 px-5 py-4 text-xl font-bold text-white">
                                    Gestion de Recursos
                                </div>
                                <div class="space-y-5 px-5 py-5 text-gray-700">
                                    <div class="border-b border-gray-200 pb-4">
                                        <div class="text-base font-semibold text-gray-500">Recurso a Reemplazar</div>
                                        <div class="mt-2 text-3xl font-semibold text-red-400" x-text="summaryOldTargetLabel"></div>
                                    </div>

                                    <div class="border-b border-gray-200 pb-4">
                                        <div class="text-base font-semibold text-gray-500">Nuevo Recurso</div>
                                        <div class="mt-2 text-3xl font-semibold text-emerald-500" x-text="summaryNewTargetLabel"></div>
                                    </div>

                                    <div>
                                        <div class="text-base font-semibold text-gray-500">Motivo Predefinido</div>
                                        <div class="mt-3 rounded-sm border border-gray-200 bg-gray-50 px-4 py-4 text-center text-2xl font-medium text-blue-900" x-text="fullDescription || 'Sin descripcion'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 pb-4">
                            <div class="rounded-sm bg-amber-400 px-6 py-4 text-center">
                                <div class="text-2xl font-bold text-blue-900">Advertencia del Sistema</div>
                                <div class="mt-2 text-base text-gray-800">
                                    Esta operacion modificara multiples programaciones existentes. La accion es irreversible y requiere confirmacion expresa.
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center gap-4 px-6 pb-6 pt-2">
                            <button type="button" @click="$refs.changeForm.submit()" class="inline-flex items-center rounded-sm bg-sky-600 px-8 py-3 text-xl font-semibold text-white transition hover:bg-sky-700">
                                <svg class="me-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Confirmar
                            </button>
                            <button type="button" @click="confirmOpen = false" class="inline-flex items-center rounded-sm border border-red-400 bg-white px-8 py-3 text-xl font-semibold text-red-500 transition hover:bg-red-50">
                                <svg class="me-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
