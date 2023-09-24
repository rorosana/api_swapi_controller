<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Characters;
use App\Models\SkinColor;
use App\Models\HairColor;
use Illuminate\Database\QueryException;

class SWAPIController extends Controller
{
   public function fetchAndStoreCharacters()
{
    // Comprueba si los datos ya están en caché
    if (cache()->has('swapi_characters')) {
        // Si están en caché, obtén los datos desde la caché
        $characters = cache('swapi_characters');
    } else {
        // Si no están en caché, realiza la solicitud a la API y almacena los datos en caché
        $humanCharacterUrls = [
            "https://swapi.dev/api/people/66/",
            "https://swapi.dev/api/people/67/",
            "https://swapi.dev/api/people/68/",
            "https://swapi.dev/api/people/74/"
        ];

        $result = [];

        foreach ($humanCharacterUrls as $characterUrl) {
            $characterResponse = Http::get($characterUrl);
            $characterData = $characterResponse->json();

            if ($characterData['species'][0] === 'https://swapi.dev/api/species/1/') {
                // Procesa y almacena los datos en el resultado
                $result[] = $characterData;
            }
        }

        // Almacena los datos en caché durante 24 horas
        cache()->put('swapi_characters', $result, now()->addHours(24));

        // Establece la variable $characters con los datos recién obtenidos
        $characters = $result;
    }

    return response()->json(['message' => 'Datos de personajes humanos', 'characters' => $characters]);
}


public function refreshCache()
{
    try {
        // Realiza solicitudes a swapi.com para obtener los datos más recientes
        $humanCharacterUrls = [
            "https://swapi.dev/api/people/66/",
            "https://swapi.dev/api/people/67/",
            "https://swapi.dev/api/people/68/",
            "https://swapi.dev/api/people/74/"
        ];

        $updatedData = [];

        foreach ($humanCharacterUrls as $characterUrl) {
            $characterResponse = Http::get($characterUrl);
            $characterData = $characterResponse->json();

            // Agrega los datos actualizados al arreglo
            $updatedData[] = $characterData;
        }

        // Actualiza la caché con los datos actualizados
        cache()->put('swapi_characters', $updatedData, now()->addHours(24));

        return redirect()->route('home')->with('success', 'Caché actualizada con éxito');
    } catch (\Exception $e) {
        // Maneja cualquier error que pueda ocurrir durante las solicitudes
        return redirect()->route('home')->with('error', 'Error al actualizar la caché: ' . $e->getMessage());
    }
}




public function index()
{
    $hairColors = HairColor::distinct()->pluck('nombre');
    $skinColors = SkinColor::distinct()->pluck('nombre');
    $characters = []; // Inicializa la variable de personajes vacía


    return view('characters_finder', compact('characters', 'hairColors', 'skinColors'));
}


public function getColors()
{
    $hairColors = HairColor::distinct()->pluck('nombre');
    $skinColors = SkinColor::distinct()->pluck('nombre');

    return response()->json(['hairColors' => $hairColors, 'skinColors' => $skinColors]);
}



public function search(Request $request)
{
    try {
        $searchTerm = $request->input('search_term'); // Obtiene el término de búsqueda del usuario desde el formulario

        // Realiza la consulta para obtener los personajes que coinciden con el color de piel o cabello
        $characters = Characters::whereHas('skinColor', function ($query) use ($searchTerm) {
            $query->where('nombre', 'like', '%' . $searchTerm . '%');
        })
        ->orWhereHas('hairColor', function ($query) use ($searchTerm) {
            $query->where('nombre', 'like', '%' . $searchTerm . '%');
        })
        ->get();

        return view('characters_finder', compact('characters'));
    } catch (\Exception $e) {
        // Maneja la excepción, por ejemplo, registra un mensaje de error
        return response()->json(['error' => $e->getMessage()], 500);
    }
}




public function searchWithFilters(Request $request)
{
    $hairColor = $request->input('hair_color');
    $skinColor = $request->input('skin_color');

    // Realiza la consulta para obtener los personajes que coinciden con los filtros
    $query = Characters::query();

    if ($hairColor != 'Select hair color') {
        $query->whereHas('hairColor', function ($query) use ($hairColor) {
            $query->where('nombre', $hairColor);
        });
    }

    if ($skinColor != 'Select skin color') {
        $query->whereHas('skinColor', function ($query) use ($skinColor) {
            $query->where('nombre', $skinColor);
        });
    }

    $characters = $query->get();

    return response()->json($characters);
}
}
