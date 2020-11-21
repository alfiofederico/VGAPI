                <div wire:init="loadComingSoon" class="most-anticipated-container space-y-10 mt-8">
                    @forelse ($comingSoon as $game)
                    <div class="game flex">
                        <a href="{{ route('games.show', $game['slug'] )}}"><img src="{{ isset($game['cover']) ? Str::replaceFirst('thumb','cover_small', $game['cover']['url']) : '#' }}" class="w-16 hover:opacity-75 transition ease-in-out duration-150"></a>
                        <div class="ml-4">
                            <a href="{{ route('games.show', $game['slug'] )}}" class="hover:text-gray-300">{{$game['name']}}</a>
                            <div class="text-gray-400 mt-1 italic">
                                @foreach($game['platforms'] as $platform)
                                    {{$platform['abbreviation']}},
                                @endforeach
                            </div>
                            <div class="text-gray-400 text-sm mt-1">
                                {{ Carbon\Carbon::parse($game['first_release_date'])->format('M d, Y') }}
                            </div>
                        </div>
                        
                    </div>
                    @empty
                    <div class="spinner mt-8"></div>
                    @endforelse
                </div>