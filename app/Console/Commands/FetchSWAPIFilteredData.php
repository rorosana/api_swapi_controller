<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class FetchSWAPIFilteredData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swapi:fetch-filtered-data {hair_color} {skin_color}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch filtered data from SWAPI by hair color and skin color for human characters.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
                {

            $speciesData = $this->getSpeciesData();


            if ($speciesData) {
                $humanSpecies = json_decode($speciesData, true);


                $skinColors = $humanSpecies['skin_colors'];
                $hairColors = $humanSpecies['hair_colors'];



                $this->info("Data fetched from SWAPI based on hair and skin color filters for human characters.");
            } else {
                $this->error("Failed to fetch species data from SWAPI.");
            }
        }

}

private function getSpeciesData()
        {

            $client = new Client();
            $response = $client->get("https://swapi.dev/api/species/1/");


            if ($response->getStatusCode() === 200) {
                return $response->getBody();
            }

            return null;
            }
}
