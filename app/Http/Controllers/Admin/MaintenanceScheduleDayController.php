<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceScheduleDay;
use App\Support\PublicImageStorage;
use Illuminate\Http\Request;

class MaintenanceScheduleDayController extends Controller
{
    public function index(Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        $this->ensureScheduleBelongsToMaintenance($maintenance, $schedule);

        $schedule->load(['vehicle', 'responsible', 'days' => fn ($query) => $query->orderBy('fecha')]);

        return view('admin.maintenances.days.index', compact('maintenance', 'schedule'));
    }

    public function edit(Maintenance $maintenance, MaintenanceSchedule $schedule, MaintenanceScheduleDay $day)
    {
        $this->ensureScheduleBelongsToMaintenance($maintenance, $schedule);
        $this->ensureDayBelongsToSchedule($schedule, $day);

        return view('admin.maintenances.days.edit', compact('maintenance', 'schedule', 'day'));
    }

    public function update(Request $request, Maintenance $maintenance, MaintenanceSchedule $schedule, MaintenanceScheduleDay $day)
    {
        $this->ensureScheduleBelongsToMaintenance($maintenance, $schedule);
        $this->ensureDayBelongsToSchedule($schedule, $day);

        $data = $request->validate([
            'observacion' => ['nullable', 'string', 'max:1000'],
            'realizado' => ['nullable', 'boolean'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:2048'],
        ], [
            'observacion.max' => 'La observacion no debe superar los 1000 caracteres.',
            'imagen.image' => 'El archivo debe ser una imagen valida.',
            'imagen.mimes' => 'La imagen debe ser de tipo JPEG, JPG, PNG, WEBP o GIF.',
            'imagen.max' => 'La imagen no debe pesar mas de 2MB.',
        ]);

        if ($request->hasFile('imagen')) {
            PublicImageStorage::delete($day->imagen);
            $data['imagen'] = PublicImageStorage::store($request->file('imagen'), 'maintenance_days');
        }

        $data['realizado'] = $request->boolean('realizado');

        $day->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Dia actualizado correctamente.',
                'redirect' => route('admin.maintenance.schedule.days.index', [$maintenance, $schedule]),
            ]);
        }

        return redirect()->route('admin.maintenance.schedule.days.index', [$maintenance, $schedule])
            ->with('success', 'Dia actualizado correctamente.');
    }

    private function ensureScheduleBelongsToMaintenance(Maintenance $maintenance, MaintenanceSchedule $schedule): void
    {
        abort_unless($schedule->maintenance_id === $maintenance->id, 404);
    }

    private function ensureDayBelongsToSchedule(MaintenanceSchedule $schedule, MaintenanceScheduleDay $day): void
    {
        abort_unless($day->maintenance_schedule_id === $schedule->id, 404);
    }
}
