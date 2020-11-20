<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HighestRatedGames extends Component
{
    public $highestRatedGames= [];

    public function loadhighestRatedGames()
    {
        $before=Carbon::now()->subMonths(2)->timestamp;
        $after=Carbon::now()->addMonths(2)->timestamp;
         $this->highestRatedGames = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => (env('IGDB_ACCESS_TOKEN')),
        ])
            ->withBody(
                "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, slug, summary;
                where platforms = (48,49,130,6)
                & (first_release_date >= {$before}
                & first_release_date <  {$after}
                & total_rating_count > 5);
                sort total_rating_count desc;
                limit 12;",
                'text/plain'
            )
            ->post('https://api.igdb.com/v4/games')->json();

       /*  dump($highestRatedGames); */

    }
    
    public function render()
    {
        return view('livewire.highest-rated-games');
    }
}
