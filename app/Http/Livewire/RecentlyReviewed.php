<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RecentlyReviewed extends Component
{
    public $recentlyReviewed = [];
    public function loadRecentlyReviewed()
    {
        $before=Carbon::now()->subMonths(2)->timestamp;
        $current=Carbon::now()->timestamp;
        $recentlyReviewedUnformatted = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => (env('IGDB_ACCESS_TOKEN')),
        ])
            ->withBody(
                "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, rating_count, slug, summary;
                where platforms = (48,49,130,6)
                & (first_release_date >= {$before}
                & first_release_date <  {$current}
                & rating_count > 5);
                sort total_rating_count desc;
                limit 4;",
                'text/plain'
            )
            ->post('https://api.igdb.com/v4/games')->json();

                 $this->recentlyReviewed = $this->formatForView($recentlyReviewedUnformatted);
    }
    public function render()
    {
        return view('livewire.recently-reviewed');
    }

    public function formatForView($games)
    {
        return collect($games)->map(function($game){
            return collect($game)->merge([
                'coverImageUrl' => Str::replaceFirst('thumb','cover_big', $game['cover']['url']),
                'rating' => isset($game['rating']) ? round($game['rating']).'%' : null,
                'platforms' => collect($game['platforms'])->pluck('abbreviation')->filter()->implode(', '),
            ]);
        })->toArray();
    }
}
