@extends('layouts.app')

@section('title', $game->home_team . ' vs ' . $game->away_team . ' - 4rolls')

@section('content')
<div class="container">
    <div style="background: #14142a; border: 1px solid #1e1e3a; border-radius: 12px; padding: 2rem; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 2rem; flex-wrap: wrap;">
            <div style="text-align: center;">
                @if($game->home_logo)<img src="{{ $game->home_logo }}" width="60" style="margin-bottom:8px;"><br>@endif
                <strong style="font-size: 1.2rem;">{{ $game->home_team }}</strong>
            </div>
            <div style="text-align: center; color: #555;">
                <div style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px;">
                    @if($game->league_logo)<img src="{{ $game->league_logo }}" width="24" style="vertical-align:middle; margin-right:6px;">@endif
                    {{ $game->league }}
                </div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #00d4ff;">VS</div>
                <div style="font-size: 0.8rem; color: #888;">{{ $game->status }}</div>
            </div>
            <div style="text-align: center;">
                @if($game->away_logo)<img src="{{ $game->away_logo }}" width="60" style="margin-bottom:8px;"><br>@endif
                <strong style="font-size: 1.2rem;">{{ $game->away_team }}</strong>
            </div>
        </div>
    </div>

    <h3 style="margin-bottom: 1rem; font-size: 1rem; color: #888; text-transform: uppercase; letter-spacing: 1px;">
        <i class="fas fa-server me-2"></i>Available Streams ({{ $game->streams->count() }})
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.8rem;">
        @foreach($game->streams as $stream)
        <a href="{{ route('watch', [$game->slug, $stream->id]) }}" class="match-card" style="justify-content: center; flex-direction: column; text-align: center; padding: 1.2rem;">
            <i class="fas fa-play-circle" style="font-size: 2rem; color: #00d4ff; margin-bottom: 0.5rem;"></i>
            <strong>{{ $stream->server_name }}</strong>
        </a>
        @endforeach
    </div>
</div>
@endsection
