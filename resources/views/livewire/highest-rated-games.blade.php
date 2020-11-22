        <div wire:init="loadhighestRatedGames" class="popular-games text-sm grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 xl:grid-cols-6 gap-12 border-b border-gray-800 pb-16">
        @forelse ($highestRatedGames as $game)
            <x-Game-Card :game="$game"/>
        @empty  
            <div class="spinner mt-8"></div>        
        @endforelse 
        </div> <!-- end popular-games -->

        @push('scripts')
            @include('_rating',[
                'event'=> 'gameWithRatingAdded'
            ])
        @endpush