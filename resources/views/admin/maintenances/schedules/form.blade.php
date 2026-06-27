<div
    class="grid grid-cols-1 md:grid-cols-2 gap-6"
    data-schedule-overlap-url="{{ route('admin.maintenance.schedule.check-overlap', $maintenance) }}"
    data-schedule-id="{{ $schedule?->id }}"
>
    <div>
        <x-input-label for="vehicle_id" :value="__('Vehiculo')" />
        <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
            <option value="">Seleccione un vehiculo</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $schedule?->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                    {{ $vehicle->name }} - {{ $vehicle->plate }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('vehicle_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="responsible_id" :value="__('Responsable')" />
        <select id="responsible_id" name="responsible_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
            <option value="">Seleccione un responsable</option>
            @foreach($responsibles as $responsible)
                <option value="{{ $responsible->id }}" {{ old('responsible_id', $schedule?->responsible_id) == $responsible->id ? 'selected' : '' }}>
                    {{ $responsible->name }} {{ $responsible->last_name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('responsible_id')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="tipo_mantenimiento" :value="__('Tipo de Mantenimiento')" />
        <select id="tipo_mantenimiento" name="tipo_mantenimiento" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
            <option value="">Seleccione un tipo</option>
            @foreach($types as $value => $label)
                <option value="{{ $value }}" {{ old('tipo_mantenimiento', $schedule?->tipo_mantenimiento) === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('tipo_mantenimiento')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="dia_semana" :value="__('Dia de la Semana')" />
        <select id="dia_semana" name="dia_semana" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring focus:ring-emerald-200 focus:ring-opacity-50 text-sm" required>
            <option value="">Seleccione un dia</option>
            @foreach($days as $value => $label)
                <option value="{{ $value }}" {{ old('dia_semana', $schedule?->dia_semana) === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('dia_semana')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="hora_inicio" :value="__('Hora de Inicio')" />
        <x-text-input id="hora_inicio" name="hora_inicio" type="time" value="{{ old('hora_inicio', $schedule ? substr($schedule->hora_inicio, 0, 5) : '08:00') }}" class="block mt-1 w-full text-sm" required />
        <x-input-error :messages="$errors->get('hora_inicio')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="hora_fin" :value="__('Hora de Fin')" />
        <x-text-input id="hora_fin" name="hora_fin" type="time" value="{{ old('hora_fin', $schedule ? substr($schedule->hora_fin, 0, 5) : '09:00') }}" class="block mt-1 w-full text-sm" required />
        <x-input-error :messages="$errors->get('hora_fin')" class="mt-1" />
        <p id="schedule-overlap-error" class="mt-1 text-sm text-red-600 hidden"></p>
    </div>
</div>

<script>
    (() => {
        const root = document.querySelector('[data-schedule-overlap-url]');
        if (!root) return;

        const form = root.closest('form');
        const submit = form?.querySelector('button[type="submit"]');
        const error = document.getElementById('schedule-overlap-error');
        const fields = ['vehicle_id', 'dia_semana', 'hora_inicio', 'hora_fin']
            .map((name) => form.querySelector(`[name="${name}"]`));
        let timeoutId;

        const setBlocked = (message) => {
            if (error) {
                error.textContent = message || '';
                error.classList.toggle('hidden', !message);
            }
            if (submit) {
                submit.disabled = Boolean(message);
                submit.classList.toggle('opacity-60', Boolean(message));
                submit.classList.toggle('cursor-not-allowed', Boolean(message));
            }
        };

        const checkOverlap = async () => {
            const [vehicle, day, start, end] = fields;

            if (!vehicle.value || !day.value || !start.value || !end.value || start.value >= end.value) {
                setBlocked('');
                return;
            }

            const body = new FormData();
            body.append('vehicle_id', vehicle.value);
            body.append('dia_semana', day.value);
            body.append('hora_inicio', start.value);
            body.append('hora_fin', end.value);

            if (root.dataset.scheduleId) {
                body.append('schedule_id', root.dataset.scheduleId);
            }

            const response = await fetch(root.dataset.scheduleOverlapUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body,
            });

            if (!response.ok) return;

            const data = await response.json();
            setBlocked(data.overlap ? data.message : '');
        };

        fields.forEach((field) => {
            field.addEventListener('change', () => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(checkOverlap, 250);
            });
        });

        checkOverlap();
    })();
</script>
