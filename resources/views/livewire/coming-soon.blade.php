                <div wire:init="loadComingSoon" class="most-anticipated-container space-y-10 mt-8">
                    @forelse ($comingSoon as $game)
                    <div class="game flex">
                        <a href="{{ route('games.show', $game['slug'] )}}"><img src="{{ $game['coverImageUrl'] }}" class="w-16 hover:opacity-75 transition ease-in-out duration-150"></a>
                        <div class="ml-4">
                            <a href="{{ route('games.show', $game['slug'] )}}" class="hover:text-gray-300">{{$game['name']}}</a>
                            <div class="text-gray-400 mt-1 italic">
                               {{ $game['platforms'] }}
                            </div>
                            <div class="text-gray-400 text-sm mt-1">
                               {{ $game['releaseDate'] }}
                            </div>
                        </div>          
                    </div>
                    @empty
                    <div class="spinner mt-8"></div>
                    @endforelse
                </div>