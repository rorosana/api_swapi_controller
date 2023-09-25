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

/**
 * Obtiene datos de personajes humanos de la API de Star Wars y los almacena en caché.
 *
 * @return \Illuminate\Http\JsonResponse
 */
   public function fetchAndStoreCharacters()
{
    if (cache()->has('swapi_characters')) {
        $characters = cache('swapi_characters');
    } else {
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
                $result[] = $characterData;
            }
        }

        cache()->put('swapi_characters', $result, now()->addHours(24));

        $characters = $result;
    }

    return response()->json(['message' => 'Datos de personajes humanos', 'characters' => $characters]);
}

/**
 * Actualiza la caché de datos de personajes humanos obtenidos de la API de Star Wars.
 *
 * @return \Illuminate\Http\RedirectResponse
 */
public function refreshCache()
{
    try {
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

            $updatedData[] = $characterData;
        }


        cache()->put('swapi_characters', $updatedData, now()->addHours(24));

        return redirect()->route('home')->with('success', 'Caché actualizada con éxito');
    } catch (\Exception $e) {
        return redirect()->route('home')->with('error', 'Error al actualizar la caché: ' . $e->getMessage());
    }
}


/**
 * Muestra la página de búsqueda de personajes con filtros. Si no las variables aparecían como undefined, no se cargaba la vista.
 *
 * @return \Illuminate\View\View
 */

public function index()
{
    $hairColors = HairColor::distinct()->pluck('nombre');
    $skinColors = SkinColor::distinct()->pluck('nombre');
    $characters = [];

    //dd($characters);


    return view('characters_finder', compact('characters', 'hairColors', 'skinColors'));
}

/**
 * Obtiene una lista de colores de cabello y piel y devuelve una respuesta JSON. Para rellenar los options de los selects.
 *
 * @return \Illuminate\Http\JsonResponse
 */


public function getColors()
{
    $hairColors = HairColor::distinct()->pluck('nombre');
    $skinColors = SkinColor::distinct()->pluck('nombre');

    return response()->json(['hairColors' => $hairColors, 'skinColors' => $skinColors]);
}

/**
 * Realiza una búsqueda de personajes según el término de búsqueda y devuelve una vista con los resultados.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Contracts\View\View
 */

public function search(Request $request)
{
    try {
        $searchTerm = $request->input('search_term');

        if (!empty($searchTerm)) {
            $characters = Characters::whereHas('skinColor', function ($query) use ($searchTerm) {
                $query->where('nombre', 'like', '%' . $searchTerm . '%');
            })
            ->orWhereHas('hairColor', function ($query) use ($searchTerm) {
                $query->where('nombre', 'like', '%' . $searchTerm . '%');
            })
            ->get();
        } else {
            $characters = [];
        }

        return view('characters_finder', compact('characters'));
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

/**
 * Realiza una búsqueda de personajes con filtros de color de piel y cabello y devuelve los resultados en formato JSON.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\JsonResponse
 */

public function searchWithFilters(Request $request)
{
    $hairColor = $request->input('hair_color');
    $skinColor = $request->input('skin_color');

    if ($hairColor != 'Select hair color' || $skinColor != 'Select skin color') {

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
    } else {
        $characters = [];
    }

    return response()->json($characters);
}

}
