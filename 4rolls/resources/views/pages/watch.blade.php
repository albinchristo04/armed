@extends('layouts.app')

@section('title', $game->home_team . ' vs ' . $game->away_team . ' - Watch Live')

@section('content')
<div class="container">
    <div style="margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <a href="{{ route('match', $game->slug) }}" style="color: #00d4ff; text-decoration: none; font-size: 0.85rem;">
                <i class="fas fa-arrow-left me-1"></i> Back to match
            </a>
            <h2 style="font-size: 1.1rem; margin-top: 0.3rem;">{{ $game->home_team }} vs {{ $game->away_team }}</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <label style="font-size: 0.8rem; color: #888;">Switch Server:</label>
            <select id="serverSelect" onchange="switchServer(this.value)" style="background: #14142a; color: #e0e0e0; border: 1px solid #2a2a4a; border-radius: 6px; padding: 6px 12px; font-size: 0.85rem;">
                @foreach($game->streams as $s)
                <option value="{{ route('watch', [$game->slug, $s->id]) }}" {{ $s->id == $activeStream->id ? 'selected' : '' }}>
                    {{ $s->server_name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div style="position: relative; padding-top: 56.25%; background: #000; border-radius: 12px; overflow: hidden; border: 1px solid #1e1e3a;">
        <iframe src="{{ $activeStream->url }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none;" allowfullscreen></iframe>
    </div>

    <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
        @foreach($game->streams as $s)
        <a href="{{ route('watch', [$game->slug, $s->id]) }}"
           style="padding: 8px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; text-decoration: none;
                  {{ $s->id == $activeStream->id ? 'background: linear-gradient(135deg, #00d4ff, #0090ff); color: #000;' : 'background: #14142a; color: #aaa; border: 1px solid #2a2a4a;' }}">
            {{ $s->server_name }}
        </a>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchServer(url) { window.location.href = url; }
</script>
@endpush
