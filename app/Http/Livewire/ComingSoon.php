<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComingSoon extends Component
{
    public $comingSoon = [];
    public function loadComingSoon()
    {
        $current=Carbon::now()->timestamp;
        $comingSoonUnformatted = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => (env('IGDB_ACCESS_TOKEN')),
        ])
        ->withBody(
            "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, slug, summary;
            where platforms = (48,49,130,6)
            & (first_release_date > {$current});
            sort first_release_date asc;
            limit 11;",
            'text/plain'
        )->post('https://api.igdb.com/v4/games')->json();

        $this->comingSoon = $this->formatForView($comingSoonUnformatted);

    }
    public function render()
    {
        return view('livewire.coming-soon');
    }

     public function formatForView($games)
    {
        return collect($games)->map(function($game){
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb','cover_small', $game['cover']['url']),
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->filter()->implode(', '),
                'releaseDate'=> Carbon::parse($game['first_release_date'])->format('M d, Y'),
            ]);
        })->toArray();
    }
}
