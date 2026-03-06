@extends('layouts.app')

@section('title', '4rolls - Live Sports Streaming')

@section('content')
<div class="container">

    {{-- Important Games --}}
    @if($importantGames->count())
    <div class="important-banner"><i class="fas fa-fire me-2"></i>Important Games</div>
    <div style="background: #1a0a0e; border: 1px solid #ff475733; border-top: none; border-radius: 0 0 10px 10px; padding: 0.5rem; margin-bottom: 1.5rem;">
        @foreach($importantGames as $game)
        <a href="{{ route('match', $game->slug) }}" class="match-card" style="border-color: #ff475733;">
            <div class="team">
                @if($game->home_logo)<img src="{{ $game->home_logo }}" alt="">@endif
                {{ $game->home_team }}
            </div>
            <div class="vs">
                @if(str_contains(strtolower($game->status ?? ''), 'started'))
                    <span class="live-badge"><i class="fas fa-circle me-1" style="font-size:6px;"></i>LIVE</span>
                @else
                    {{ $game->status }}
                @endif
                <span class="status">{{ $game->streams->count() }} streams</span>
            </div>
            <div class="team right">
                {{ $game->away_team }}
                @if($game->away_logo)<img src="{{ $game->away_logo }}" alt="">@endif
            </div>
            <span class="btn-watch d-none d-md-inline-block">Watch</span>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Regular Games by League --}}
    @foreach($regularGames as $league => $games)
    <div class="league-header">
        @php $firstGame = $games->first(); @endphp
        @if($firstGame?->league_logo)<img src="{{ $firstGame->league_logo }}" alt="">@endif
        <h3>{{ $league ?: 'Other' }}</h3>
    </div>
    @foreach($games as $game)
    <a href="{{ route('match', $game->slug) }}" class="match-card">
        <div class="team">
            @if($game->home_logo)<img src="{{ $game->home_logo }}" alt="">@endif
            {{ $game->home_team }}
        </div>
        <div class="vs">
            @if(str_contains(strtolower($game->status ?? ''), 'started'))
                <span class="live-badge"><i class="fas fa-circle me-1" style="font-size:6px;"></i>LIVE</span>
            @else
                {{ $game->status }}
            @endif
            <span class="status">{{ $game->streams->count() }} streams</span>
        </div>
        <div class="team right">
            {{ $game->away_team }}
            @if($game->away_logo)<img src="{{ $game->away_logo }}" alt="">@endif
        </div>
        <span class="btn-watch d-none d-md-inline-block">Watch</span>
    </a>
    @endforeach
    @endforeach

</div>
@endsection
