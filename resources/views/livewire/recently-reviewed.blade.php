                <div wire:init="loadRecentlyReviewed" class="recently-reviewed-container space-y-12 mt-8">
                    @forelse ($recentlyReviewed as $game)
                    <div class="game bg-gray-800 rounded-lg shadow-md flex px-6 py-6">
                        <div class="relative flex-none">
                            <a href="{{ route('games.show', $game['slug'] )}}">
                                <img src="{{ $game['coverImageUrl'] }}" alt="game cover" class="w-48 hover:opacity-75 transition ease-in-out duration-150">
                            </a>
                             @if (isset($game['rating']))
                        <div id="review_{{$game['slug']}}" class="absolute bottom-0 right-0 w-16 h-16 bg-gray-900 rounded-full text-xs" style="right: -20px; bottom: -20px">
                                
                            </div>
                            @endif
                        </div>
                        <div class="ml-6 lg:ml-12">
                            <a href="{{ route('games.show', $game['slug'] )}}" class="block text-lg font-semibold leading-tight hover:text-gray-400 mt-4">{{$game['name']}}</a>
                            <div class="text-gray-400 mt-1">
                             
                                     {{ $game['platforms'] }}
                             
                            </div>
                            <p class="mt-6 text-gray-400 hidden lg:block">
                               {{$game['summary']}}
                            </p>
                        </div>
                    </div> <!-- end game -->
                    @empty
                    <div class="spinner mt-8">      
                    </div>
                    @endforelse
                </div>

        @push('scripts')
            @include('_rating',[
                'event'=> 'reviewGameWithRatingAdded'
            ])
        @endpush