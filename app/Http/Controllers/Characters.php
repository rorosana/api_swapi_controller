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

    public function filter(Request $request)
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
    public function search(Request $request)
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
    }
}
