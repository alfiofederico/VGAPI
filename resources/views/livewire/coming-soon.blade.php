<div wire:init="loadComingSoon" class="most-anticipated-container space-y-10 mt-8">
    @foreach ($comingSoon as $game)
        <x-game-card-small :game="$game" />
    @endforeach
</div>