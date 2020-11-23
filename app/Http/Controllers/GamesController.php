<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function index()
    {
        $before=Carbon::now()->subMonths(2)->timestamp;
        $after=Carbon::now()->addMonths(2)->timestamp;
        $afterFourMonths=Carbon::now()->addMonths(4)->timestamp;
        $current=Carbon::now()->timestamp;


       

        

       /*  dump($recentlyReviewed); */


/*         $mostAnticipated = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => (env('IGDB_ACCESS_TOKEN')),
        ])
            ->withBody(
                "fields name, cover.url, first_release_date;
                where platforms = (48,49,130,6)
                & (first_release_date >= {$current}
                & first_release_date <  {$afterFourMonths}
                & rating_count > 5);
                sort total_rating_count desc;
                limit 4;",
                'text/plain'
            )
            ->post('https://api.igdb.com/v4/games')->json();

        dump($mostAnticipated); */

        $comingSoon = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => (env('IGDB_ACCESS_TOKEN')),
        ])
        ->withBody(
            "fields name, cover.url, first_release_date, total_rating_count, platforms.abbreviation, rating, slug, summary;
            where platforms = (48,49,130,6)
            & (first_release_date > {$current});
            sort first_release_date asc;
            limit 4;",
            'text/plain'
        )->post('https://api.igdb.com/v4/games')->json();

        return view('index', [
            /* 'highestRatedGames' => $highestRatedGames, */
           /*  'recentlyReviewed' => $recentlyReviewed, */
            /* 'mostAnticipated' => $mostAnticipated, */
            /* 'comingSoon' => $comingSoon, */
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {

        $game = Http::withHeaders([
            'Client-ID' => env('IGDB_CLIENT_ID'),
            'Authorization' => (env('IGDB_ACCESS_TOKEN')),
        ])
            ->withBody(
                "fields *, cover.url,irst_release_date, total_rating_count, platforms.abbreviation, url, websites, rating,aggregated_rating_count, aggregate_rating, slug, involved_companies.company.name, genres.name, websites.*,videos.*,screenshots.*,similar_games.slug,similar_games.rating,similar_games.platforms.abbreviation,similar_games.cover.url,similar_games.name,similar_games.platforms;
                 where slug = \"{$slug}\";
                
              
                ",
                'text/plain'
            )
            ->post('https://api.igdb.com/v4/games')->json();
         /* dump($game); */
         abort_if(!$game, 404);

        return view('show', [
            'game'=> $this->formatGameForView( $game[0]),
        ]);

      
    }

     public function formatGameForView($game)
     {
        return collect($game)->merge([
            'coverImageUrl' => isset($game['cover'])
                ? Str::replaceFirst('thumb', 'cover_big', $game['cover']['url'])
                : 'https://via.placeholder.com/264x352',
            'genres' => isset($game['genres']) 
                ? collect($game['genres'])->pluck('name')->implode(', ')
                : 'N/A',
            'involved_companies' => isset($game['involved_companies'])
                ? collect($game['involved_companies'])->pluck('company')->pluck('name')->implode(', ')
                : "N/A",
            'platforms' => isset($game['platforms']) 
                ? collect($game['platforms'])->pluck('abbreviation')->implode(', ') 
                : 'N/A',
            'memberRating' => isset($game['rating']) 
                ? round($game['rating'])
                : '0',
            'criticRating' => isset($game['aggregated_rating']) 
                ? round($game['aggregated_rating']) 
                : '0',
            'trailer' => isset($game['videos'])
                ? "https://youtube.com/embed/" . $game['videos'][0]['video_id']
                : "",
            'screenshots' => isset($game['screenshots']) 
                ? collect($game['screenshots'])->map(function ($screenshot) {
                    return [
                        'big' => Str::replaceFirst('thumb', 'screenshot_big', $screenshot['url']),
                        'huge' => Str::replaceFirst('thumb', 'screenshot_huge', $screenshot['url']),
                    ];
                })->take(9)
                : '',
            'similar_games' => isset($game['similar_games'])
                ? collect($game['similar_games'])->map(function ($game){
                    return collect($game)->merge([
                        'coverImageUrl' => isset($game['cover']) 
                            ? Str::replaceFirst('thumb', 'cover_big', $game['cover']['url']) 
                            : 'https://via.placeholder.com/264x352',
                        'rating' => isset($game['rating']) 
                            ? round($game['rating']) 
                            : '0',
                        'platforms' => isset($game['platforms']) 
                            ? collect($game['platforms'])->pluck('abbreviation')->implode(', ') 
                            : null,
                        ]);
                    })->take(6)
                : [],
            'social'=> [
                    'website'=>array_key_exists('websites',$game)?collect($game['websites'])->first():null,
                    'facebook'=>array_key_exists('websites',$game)? collect($game['websites'])->filter(function ($website){
                        return Str::contains($website['url'],'facebook');
                    })->first():null,
                    'instagram'=>array_key_exists('websites',$game)? collect($game['websites'])->filter(function ($website){
                        return Str::contains($website['url'],'instagram');
                    })->first():null,
                    'twitter'=>array_key_exists('websites',$game)? collect($game['websites'])->filter(function ($website){
                        return Str::contains($website['url'],'twitter');
                    })->first():null,
                  
                ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
