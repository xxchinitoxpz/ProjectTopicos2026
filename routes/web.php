<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BrandModelController;
use App\Http\Controllers\Admin\VehicleColorController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\VehicleTypeController;
use App\Http\Controllers\Admin\StaffTypeController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\VehicleImageController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\VacationController;
use App\Http\Controllers\Admin\AssistanceController;
use App\Http\Controllers\Admin\ZoneController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin CRUDs
    Route::resource('vehicle-colors', VehicleColorController::class)->names('admin.vehicle-color')->except(['show']);
    Route::resource('vehicle-types', VehicleTypeController::class)->names('admin.vehicle-type')->except(['show']);
    Route::resource('brands', BrandController::class)->names('admin.brand')->except(['show']);
    Route::resource('brand-models', BrandModelController::class)->names('admin.brand-model')->except(['show']);
    Route::get('vehicles/brand-models-by-brand/{brand}', [VehicleController::class, 'brandModelsByBrand'])
        ->name('admin.vehicle.brand-models-by-brand');
    Route::resource('vehicles', VehicleController::class)->names('admin.vehicle')->except(['show']);

    // Vehicle Images Management routes
    Route::get('vehicles/{vehicle}/images', [VehicleImageController::class, 'index'])->name('admin.vehicle.images.index');
    Route::post('vehicles/{vehicle}/images', [VehicleImageController::class, 'store'])->name('admin.vehicle.images.store');
    Route::delete('vehicle-images/{image}', [VehicleImageController::class, 'destroy'])->name('admin.vehicle.images.destroy');
    Route::patch('vehicle-images/{image}/profile', [VehicleImageController::class, 'setProfile'])->name('admin.vehicle.images.set-profile');

    Route::resource('staff-types', StaffTypeController::class)->names('admin.staff-type')->except(['show']);
    Route::resource('staff', StaffController::class)->names('admin.staff')->except(['show']);
    Route::resource('contracts', ContractController::class)->names('admin.contract')->except(['show']);
    Route::resource('shifts', ShiftController::class)->names('admin.shift')->except(['show']);
    Route::resource('vacations', VacationController::class)->names('admin.vacation')->except(['show']);
    Route::patch('vacations/{vacation}/approve', [VacationController::class, 'approve'])->name('admin.vacation.approve');
    Route::patch('vacations/{vacation}/reject', [VacationController::class, 'reject'])->name('admin.vacation.reject');

    Route::resource('assistances', AssistanceController::class)->names('admin.assistance')->except(['show']);

    // Zones
    Route::get('zones-map', [ZoneController::class, 'allZonesMap'])->name('admin.zone.all-map');
    Route::get('zones/{zone}/map', [ZoneController::class, 'showMap'])->name('admin.zone.show-map');
    Route::resource('zones', ZoneController::class)->names('admin.zone')->except(['show']);

    // Geo API (cascading selects)
    Route::get('geo/provinces/{department}', [ZoneController::class, 'provincesByDepartment'])->name('admin.geo.provinces');
    Route::get('geo/districts/{province}', [ZoneController::class, 'districtsByProvince'])->name('admin.geo.districts');
    Route::get('api/zones-geojson', [ZoneController::class, 'zonesGeoJson'])->name('admin.zones.geojson');
});

require __DIR__.'/auth.php';
