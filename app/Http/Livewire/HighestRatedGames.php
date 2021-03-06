<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HighestRatedGames extends Component
{
    public $highestRatedGames= [];

    public function loadhighestRatedGames()
    {
        $before=Carbon::now()->subMonths(2)->timestamp;
        $after=Carbon::now()->addMonths(2)->timestamp;
         $highestRatedGamesUnformatted = Http::withHeaders([
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
                limit 18;",
                'text/plain'
            )
            ->post('https://api.igdb.com/v4/games')->json();

       /*  dump($highestRatedGames); */
      

    //    dump($this->formatForView($highestRatedGamesUnformatted));
       $this->highestRatedGames = $this->formatForView($highestRatedGamesUnformatted);
       collect($this->highestRatedGames)->filter(function($game){
        return $game['rating'];
       })->each(function($game){
         $this->emit('gameWithRatingAdded',[
             'slug'=>$game['slug'],
             'rating'=>$game['rating'] / 100,
         ]);
       });
         
    }

  
    
    public function render()
    {
        return view('livewire.highest-rated-games');
    }

    public function formatForView($games)
    {
        return collect($games)->map(function($game){
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb','cover_big', $game['cover']['url']),
                'rating' => isset($game['rating']) ? round($game['rating']) : null,
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->filter()->implode(', '),
            ]);
        })->toArray();
    }
}
