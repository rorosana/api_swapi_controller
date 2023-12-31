<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Characters;
use App\Http\Controllers\SWAPIController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {return view('characters_finder');});

/*Route::get('/', function () {
    $response = Http::get('https://swapi.dev/api/species/1/');
    $data = $response->json();


    if (array_key_exists('people', $data)) {
        foreach ($data['people'] as $personUrl) {
            $personResponse = Http::get($personUrl);
            $personData = $personResponse->json();

            echo $personData['name'];
            echo "<br>";
        }
    } else {
        echo "No se encontraron personas.";
    }
});*/

Route::get('/fetchandstorecharacters', [SWAPIController::class, 'fetchAndStoreCharacters']);
Route::post('/search', [SWAPIController::class, 'search'])->name('search');
Route::post('/searchWithFilters', [SWAPIController::class, 'searchWithFilters'])->name('searchWithFilters');
Route::get('/get-colors', [SWAPIController::class, 'getColors'])->name('getColors');
Route::get('/', [SWAPIController::class, 'index'])->name('home');
Route::get('/refresh-cache', [SWAPIController::class, 'refreshCache'])->name('refreshCache');


