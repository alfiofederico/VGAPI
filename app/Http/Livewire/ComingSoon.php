<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComingSoon extends Component
{
    public $comingSoon = [];
    public function loadComingSoon()
    {
        $current=Carbon::now()->timestamp;
        $this->comingSoon = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => (env('IGDB_ACCESS_TOKEN')),
        ])
        ->withBody(
            "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, slug, summary;
            where platforms = (48,49,130,6)
            & (first_release_date > {$current});
            sort first_release_date asc;
            limit 8;",
            'text/plain'
        )->post('https://api.igdb.com/v4/games')->json();

    }
    public function render()
    {
        return view('livewire.coming-soon');
    }
}
