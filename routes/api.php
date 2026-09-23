<?php

use App\Http\Controllers\User\AgendaController;
use App\Http\Controllers\User\PreconferenceController;
use App\Http\Controllers\User\EventController;
use App\Http\Controllers\User\FacultyController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserWorkshopsController;
use App\Http\Controllers\User\JotformWebhookController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\User\StripeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1'], function () {
    Route::post('/jotform/webhook', [JotformWebhookController::class, 'handle']);
  
    Route::group(['prefix' => 'user'], function () {
        Route::get('export', [UserController::class, 'exportCSV']);
        Route::get('export-workshop', [UserController::class, 'exportWorkshopCSV']);

        Route::group(['prefix' => 'account'], function () {
            Route::post('profile', [UserController::class, 'profile']);
            Route::post('login', [UserController::class, 'login']);
            Route::post('register', [UserController::class, 'store']);
            Route::get('/users', [UserController::class, 'getAllUsers']);
            Route::post('/stripe/create-payment-intent', [StripeController::class, 'createIntent']);
        });
        Route::post('contact-us', [UserController::class, 'contact']);
        Route::group(['prefix' => 'event'], function () {
            Route::get('/', [EventController::class, 'index']);
            Route::get('/live', [EventController::class, 'live']);
            Route::get('/sponsors', [EventController::class, 'sponsors']);
            Route::get('/gallery', [EventController::class, 'gallery']);
            Route::post('/setLive/{id}', [EventController::class, 'setLive']);
            Route::get('/{id}', [EventController::class, 'show']);
        });
        Route::group(['prefix' => 'agenda'], function () {
            Route::get('/', [AgendaController::class, 'index']);
            Route::get('/pre_confrence', [AgendaController::class, 'preconfindex']);
        });
        Route::group(['prefix' => 'preconference'], function () {
            // Route::post('workshops', [PreconferenceController::class, 'registerWorkshop']);
            Route::post('workshops', [UserWorkshopsController::class, 'store']);
            // fetch the user's current bookings if any
            Route::get('workshops', [UserWorkshopsController::class, 'index']);
            Route::put('workshops', [UserWorkshopsController::class, 'update']);	

        });
        Route::group(['prefix' => 'faculty'], function () {
            Route::get('/', [FacultyController::class, 'index']);
            Route::get('/scientific', [FacultyController::class, 'scientific']);
        });
    });
    Route::group(['prefix' => 'admin'], function () {
        Route::apiResources([
            'agenda' => AgendaController::class,
            'event' => EventController::class,
            'faculty' => FacultyController::class,
        ]);
        Route::post('agenda-details/{id}', [AgendaController::class, 'storeDetails']);
        Route::put('agenda-details/{id}', [AgendaController::class, 'updateDetails']);
        Route::delete('agenda-details/{id}', [AgendaController::class, 'destroyDetails']);
        Route::post('attend', [AttendeeController::class, 'attend']);
        Route::get('attendees', [AttendeeController::class, 'getAttendees']);
        Route::delete('/attendees/delete', [AttendeeController::class, 'deleteAttendee']);
        Route::post('/badge-prints', [AttendeeController::class, 'storeBadgePrint']);
        Route::get('/badge-prints/stats', [AttendeeController::class, 'getBadgePrintStats']);
        Route::post('usersbulk', [JotformWebhookController::class, 'bulkStore']);
    }); 
});
