<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Characters;  // Importa el modelo Characters
use App\Models\SkinColor;
use App\Models\HairColor;
use Illuminate\Database\QueryException;

class SWAPIController extends Controller
{
    public function fetchAndStoreCharacters()
{
    // Lista de URLs de personajes humanos
    $humanCharacterUrls = [
        "https://swapi.dev/api/people/66/",
        "https://swapi.dev/api/people/67/",
        "https://swapi.dev/api/people/68/",
        "https://swapi.dev/api/people/74/"
    ];

    foreach ($humanCharacterUrls as $characterUrl) {
        $characterResponse = Http::get($characterUrl);
        $characterData = $characterResponse->json();

        // Verifica que la especie sea "Human" o tiene las características de los humanos
        if ($characterData['species'][0] === 'https://swapi.dev/api/species/1/') {
            try {
                // Busca o crea registros para colores de piel y cabello
                $skinColor = SkinColor::firstOrCreate(['nombre' => $characterData['skin_color']]);
                $hairColor = HairColor::firstOrCreate(['nombre' => $characterData['hair_color']]);

                // Guarda el personaje en la base de datos
                Characters::create([
                    'nombre' => $characterData['name'],
                    'height' => $characterData['height'],
                    'mass' => $characterData['mass'],
                    'eye_color' => $characterData['eye_color'],
                    'birth_year' => $characterData['birth_year'],
                    'gender' => $characterData['gender'],
                    'skin_id' => $skinColor->id,
                    'hair_id' => $hairColor->id,
                ]);
            } catch (QueryException $e) {
                // Manejar el error (por ejemplo, registrar un mensaje de error)
                // Aquí puedes manejar el error de acuerdo a tus necesidades
                // ...
            }
        }
    }

    return response()->json(['message' => 'Datos de personajes humanos guardados en la base de datos']);
}

/*public function search(Request $request)
{
    $searchTerm = $request->input('search_term');

    $characters = Characters::whereHas('skinColor', function ($query) use ($searchTerm) {
        $query->where('nombre', 'like', '%' . $searchTerm . '%');
    })
    ->orWhereHas('hairColor', function ($query) use ($searchTerm) {
        $query->where('nombre', 'like', '%' . $searchTerm . '%');
    })
    ->get();

    // dd($characters); // Descomenta esto para verificar si los datos son correctos

    return view('characters_finder', compact('characters'));
}*/

public function search(Request $request)
{
    $searchTerm = $request->input('search_term'); // Obtiene el término de búsqueda del usuario desde el formulario

    // Realiza la consulta para obtener los personajes que coinciden con el color de piel o cabello
    $characters = Characters::whereHas('skinColor', function ($query) use ($searchTerm) {
        $query->where('nombre', 'like', '%' . $searchTerm . '%');
    })
    ->orWhereHas('hairColor', function ($query) use ($searchTerm) {
        $query->where('nombre', 'like', '%' . $searchTerm . '%');
    })
    ->get();

    // Obtén los valores únicos de colores de pelo y piel
    $hairColors = HairColor::distinct()->pluck('nombre');
    $skinColors = SkinColor::distinct()->pluck('nombre');

    return view('characters_finder', compact('characters', 'hairColors', 'skinColors'));
}




}
