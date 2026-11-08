<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ReliefController;
use App\Http\Controllers\SmsAlertController;
use App\Models\Advisory;
use App\Models\EmergencyContact;
use App\Models\EvacuationCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', fn (Request $r) => $r->user()->load('household'));

    // households
    Route::get('/households', [HouseholdController::class, 'index']);
    Route::patch('/households/{household}/safety', [HouseholdController::class, 'updateSafety']);

    // read-only lists
    Route::get('/evacuation-centers', fn () => EvacuationCenter::with('puroks')->get());
    Route::get('/advisories', fn () => Advisory::orderByDesc('issued_at')->get());
    Route::get('/emergency-contacts', fn () => EmergencyContact::all());

    // incidents (assistance requests)
    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::patch('/incidents/{incident}', [IncidentController::class, 'update']);
    Route::post('/incidents/{incident}/notes', [IncidentController::class, 'addNote']);

    // relief
    Route::get('/relief-items', [ReliefController::class, 'items']);
    Route::get('/relief-distributions', [ReliefController::class, 'distributions']);
    Route::post('/relief-distributions', [ReliefController::class, 'distribute']);

    // staff
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/sms-alerts', [SmsAlertController::class, 'store']);
});