<?php
// default debug method
Route::get('debug', DebugController::class.'@index');

// Catch all undefined route here as fallback
Route::namespace('\App\Http\Controllers')->group(function() {
    Route::fallback('\\Mits430\\Larasupple\\Controllers\\RouteFallbackController@handle');
});