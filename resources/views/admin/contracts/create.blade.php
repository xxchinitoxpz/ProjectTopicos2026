<x-app-layout>
    <x-slot:title>
        Nuevo Contrato
    </x-slot:title>

    <turbo-frame id="modal" data-modal-size="max-w-4xl">
        <div data-modal-size="max-w-4xl" class="max-w-4xl mx-auto space-y-6 p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-usat-blue">Registrar Contrato</h3>
                    <p class="text-xs text-gray-400">Asigna un nuevo contrato laboral a un miembro del personal.</p>
                </div>
                <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.contract.index') }}'" class="text-gray-400 hover:text-gray-500 transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Card -->
            <form action="{{ route('admin.contract.store') }}" method="POST" x-data="{ contractType: '{{ old('contract_type', 'permanente') }}' }" class="space-y-6">
                @csrf

                <!-- Grid Section 1: Staff and Contract Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="staff_id" :value="__('Miembro del Personal')" />
                        <select id="staff_id" name="staff_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required autofocus>
                            <option value="">-- Seleccionar Personal --</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ old('staff_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} {{ $staff->last_name }} (DNI: {{ $staff->dni }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('staff_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="contract_type" :value="__('Tipo de Contrato')" />
                        <select id="contract_type" name="contract_type" x-model="contractType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="permanente">Permanente</option>
                            <option value="nombrado">Nombrado</option>
                            <option value="temporal">Temporal</option>
                        </select>
                        <x-input-error :messages="$errors->get('contract_type')" class="mt-1" />
                    </div>
                </div>

                <!-- Grid Section 2: Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="date_start" :value="__('Fecha de Inicio')" />
                        <x-text-input id="date_start" name="date_start" type="date" value="{{ old('date_start', date('Y-m-d')) }}" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('date_start')" class="mt-1" />
                    </div>

                    <!-- End Date: Dynamic visibility and state with Alpine.js -->
                    <div x-show="contractType === 'temporal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                        <x-input-label for="date_end" :value="__('Fecha de Finalización')" />
                        <x-text-input id="date_end" name="date_end" type="date" value="{{ old('date_end') }}" ::disabled="contractType !== 'temporal'" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('date_end')" class="mt-1" />
                    </div>
                </div>

                <!-- Grid Section 3: Salary, Probation and State -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="salary" :value="__('Salario Mensual (S/.)')" />
                        <x-text-input id="salary" name="salary" type="number" step="0.01" min="0" value="{{ old('salary') }}" placeholder="Ej. 1500.00" class="block mt-1 w-full text-sm" required />
                        <x-input-error :messages="$errors->get('salary')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="probation" :value="__('Período de Prueba (Meses)')" />
                        <x-text-input id="probation" name="probation" type="number" step="1" min="0" value="{{ old('probation') }}" placeholder="Ej. 3 (Opcional)" class="block mt-1 w-full text-sm" />
                        <x-input-error :messages="$errors->get('probation')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="state" :value="__('Estado del Contrato')" />
                        <select id="state" name="state" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
                            <option value="active" {{ old('state', 'active') == 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ old('state') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            <option value="expired" {{ old('state') == 'expired' ? 'selected' : '' }}>Vencido</option>
                        </select>
                        <x-input-error :messages="$errors->get('state')" class="mt-1" />
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="typeof closeModal === 'function' ? closeModal() : window.location.href='{{ route('admin.contract.index') }}'" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-emerald-600/10">
                        Guardar Contrato
                    </button>
                </div>
            </form>
        </div>
    </turbo-frame>
</x-app-layout>
