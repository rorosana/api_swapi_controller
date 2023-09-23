<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\Character;
use App\Models\SkinColor;
use App\Models\HairColor;

class Characters extends Controller
{

    /*public function filter(Request $request)
    //manage select function
    {
        $hairColor = $request->input('hair_color');
        $skinColor = $request->input('skin_color');


        $client = new Client();


        $url = 'https://swapi.dev/api/people/?format=json';
        if (!empty($hairColor)) {
            $url .= "&search=hair_color:$hairColor";
        }
        if (!empty($skinColor)) {
            $url .= "&search=skin_color:$skinColor";
        }

        info($url);

        try {

            $response = $client->get($url);


            $data = json_decode($response->getBody(), true);


            $filteredCharacters = $data['results'];

            return response()->json(['characters' => $filteredCharacters]);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Error al filtrar los datos']);
        }
    }

    //
    /*public function search(Request $request)
    {
        $searchText = $request->input('searchText');

        $apiUrl = 'https://swapi.dev/api/people/?search=' . urlencode($searchText);
        $apiResponse = file_get_contents($apiUrl);
        $characters = json_decode($apiResponse)->results;

        $selectedHairColor = $request->input('hair_color');
        $selectedSkinColor = $request->input('skin_color');

        $filteredCharacters = array_filter($characters, function ($character) use ($selectedHairColor, $selectedSkinColor) {
            $hairColorMatch = $selectedHairColor === 'all' || in_array($character->hair_color, explode(',', $selectedHairColor));
            $skinColorMatch = $selectedSkinColor === 'all' || in_array($character->skin_color, explode(',', $selectedSkinColor));
            return $hairColorMatch && $skinColorMatch;
        });

        return response()->json(['characters' => $filteredCharacters]);
    }*/

    /*public function search(Request $request)
        {
            // Obtiene la cadena ingresada por el usuario desde el formulario
            $word = $request->input('search_words');

            $url = "https://swapi.dev/api/people/?search=" . urlencode($word);
            \Log::info("URL de la solicitud a SWAPI: " . $url);


            // Realiza una solicitud a la API de SWAPI para obtener datos de personajes humanos
            $client = new Client();
            $response = $client->get('https://swapi.dev/api/species/1/');
            $speciesData = json_decode($response->getBody(), true);

            // Obtiene los URLs de los personajes humanos
            $peopleUrls = $speciesData['people'];

            // Inicializa un arreglo para almacenar los personajes que coinciden con la cadena de búsqueda
            $matchingCharacters = [];

            // Itera a través de los URLs de los personajes y filtra por la cadena de búsqueda
            foreach ($peopleUrls as $personUrl) {
                $personResponse = $client->get($personUrl);
                $personData = json_decode($personResponse->getBody(), true);

                // Verifica si la cadena de búsqueda coincide con el color de pelo o piel
                if (
                    strpos(strtolower($personData['hair_color']), strtolower($word)) !== false ||
                    strpos(strtolower($personData['skin_color']), strtolower($word)) !== false
                ) {
                    $matchingCharacters[] = $personData['name'];
                }
            }

            // Devuelve los personajes coincidentes como HTML
            $resultHtml = '';
            foreach ($matchingCharacters as $character) {
                $resultHtml .= "<tr><td>$character</td></tr>";
            }

            return $resultHtml;
        }*/

        public function search(Request $request)
{
    // Obtiene la cadena ingresada por el usuario desde el formulario
    $searchWords = $request->input('search_words');
    //dd($request->all());

    // Divide la cadena en dos palabras: color de pelo y color de piel
    $searchWordsArray = explode(' ', $searchWords);

    // Verifica si se proporcionaron ambas palabras
    if (count($searchWordsArray) < 2) {
        return response()->json(['error' => 'Debes proporcionar tanto el color de pelo como el color de piel.']);
    }

    $hairColor = $searchWordsArray[0];
    $skinColor = $searchWordsArray[1];

    // Realiza una solicitud a la API de SWAPI para obtener datos de personajes humanos
    $client = new Client();
    $response = $client->get('https://swapi.dev/api/species/1/');
    $speciesData = json_decode($response->getBody(), true);

    // Obtiene los URLs de los personajes humanos
    $peopleUrls = $speciesData['people'];

    // Inicializa un arreglo para almacenar los personajes que coinciden con los criterios de búsqueda
    $matchingCharacters = [];

    // Itera a través de los URLs de los personajes y filtra por el color de pelo y piel
    foreach ($peopleUrls as $personUrl) {
        $personResponse = $client->get($personUrl);
        $personData = json_decode($personResponse->getBody(), true);

        // Verifica si el color de pelo y piel coinciden con los criterios de búsqueda
        if (
            strpos(strtolower($personData['hair_color']), strtolower($hairColor)) !== false &&
            strpos(strtolower($personData['skin_color']), strtolower($skinColor)) !== false
        ) {
            $matchingCharacters[] = $personData['name'];
        }
    }

    // Devuelve los personajes coincidentes como JSON
    return response()->json(['characters' => $matchingCharacters]);


}



}
