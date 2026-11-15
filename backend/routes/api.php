<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvacuationCenterController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ReliefController;
use App\Http\Controllers\SmsAlertController;
use App\Models\Advisory;
use App\Models\EmergencyContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HouseholdMemberController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// public: the newest active advisory, shown on the sign-in page
Route::get('/advisories/latest', fn () => response()->json(
    Advisory::where('status', 'active')->orderByDesc('issued_at')->first()
));

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', fn (Request $r) => $r->user()->load('household'));

    // households
    Route::get('/households', [HouseholdController::class, 'index']);
    Route::post('/households', [HouseholdController::class, 'store']);
    Route::put('/households/{household}', [HouseholdController::class, 'update']);
    Route::patch('/households/{household}/safety', [HouseholdController::class, 'updateSafety']);
    Route::put('/households/{household}', [HouseholdController::class, 'update']);

    // evacuation centers
    Route::get('/evacuation-centers', [EvacuationCenterController::class, 'index']);
    Route::post('/evacuation-centers', [EvacuationCenterController::class, 'store']);
    Route::patch('/evacuation-centers/{center}/occupants', [EvacuationCenterController::class, 'updateOccupants']);

    // read-only lists
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
    Route::get('/sms-alerts', [SmsAlertController::class, 'index']);
    Route::delete('/sms-alerts/{smsLog}', [SmsAlertController::class, 'destroy']);

        // family members of a household
    Route::get('/households/{household}/members', [HouseholdMemberController::class, 'index']);
    Route::post('/households/{household}/members', [HouseholdMemberController::class, 'store']);
    Route::delete('/household-members/{member}', [HouseholdMemberController::class, 'destroy']);
});