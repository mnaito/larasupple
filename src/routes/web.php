<?php
// only for test
Route::get('test', TestController::class.'@index');

// default debug method
Route::get('debug', DebugController::class.'@index');

// Catch all undefined route here as fallback
Route::namespace('\App\Http\Controllers')->group(function() {
    Route::fallback('\\Mits430\\Larasupple\\Controllers\\RouteFallbackController@handle');
});