@extends('layouts.app')

@section('title', 'Webmasters - Embed Live Streams on Your Site')

@section('content')
<div class="container">
    <div style="text-align: center; padding: 3rem 1rem 2rem;">
        <h1 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 0.8rem;">
            Embed <span style="color: #00d4ff;">Live Streams</span> on Your Site
        </h1>
        <p style="color: #888; max-width: 600px; margin: 0 auto; line-height: 1.6;">
            Add live sports streams to your website in seconds. Choose a match below, copy the embed code, and paste it into your HTML. The player includes a built-in server switcher for your visitors.
        </p>
    </div>

    {{-- API Info --}}
    <div style="background: #14142a; border: 1px solid #1e1e3a; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1rem; color: #00d4ff; margin-bottom: 0.8rem;"><i class="fas fa-plug me-2"></i>API Endpoint</h3>
        <p style="color: #888; font-size: 0.85rem; margin-bottom: 0.8rem;">
            Access all available matches and embed URLs programmatically:
        </p>
        <div style="background: #0a0a14; border: 1px solid #2a2a4a; border-radius: 8px; padding: 0.8rem 1rem; font-family: monospace; font-size: 0.85rem; color: #00d4ff; display: flex; align-items: center; justify-content: space-between;">
            <code>GET {{ url('/api/matches') }}</code>
            <button onclick="navigator.clipboard.writeText('{{ url('/api/matches') }}').then(()=>alert('Copied!'))" style="background: #00d4ff22; color: #00d4ff; border: 1px solid #00d4ff44; padding: 4px 12px; border-radius: 6px; cursor: pointer; font-size: 0.75rem;">Copy</button>
        </div>
    </div>

    {{-- Embed Code Generator --}}
    <h3 style="font-size: 1rem; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 1rem;">
        <i class="fas fa-code me-2"></i>Choose a Match to Embed
    </h3>

    @foreach($games as $game)
    <div style="background: #14142a; border: 1px solid #1e1e3a; border-radius: 10px; padding: 1rem 1.5rem; margin-bottom: 0.8rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <strong>{{ $game->home_team }}</strong>
                <span style="color: #555; margin: 0 0.5rem;">vs</span>
                <strong>{{ $game->away_team }}</strong>
                <span style="color: #555; font-size: 0.8rem; margin-left: 0.5rem;">{{ $game->league }}</span>
            </div>
            <button class="btn-watch" style="cursor: pointer; border: none;" onclick="showEmbed({{ $game->id }}, {{ $game->streams->first()?->id ?? 0 }}, '{{ $game->home_team }} vs {{ $game->away_team }}')">
                Get Embed Code
            </button>
        </div>
    </div>
    @endforeach

    {{-- Embed Modal --}}
    <div id="embedModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; display: none; align-items: center; justify-content: center;">
        <div style="background: #14142a; border: 1px solid #1e1e3a; border-radius: 12px; padding: 2rem; max-width: 600px; width: 90%; position: relative;">
            <button onclick="document.getElementById('embedModal').style.display='none'" style="position: absolute; top: 12px; right: 16px; background: none; border: none; color: #888; font-size: 1.5rem; cursor: pointer;">&times;</button>
            <h3 id="embedTitle" style="font-size: 1rem; margin-bottom: 1rem; color: #00d4ff;"></h3>
            <label style="font-size: 0.8rem; color: #888;">Copy this code and paste into your HTML:</label>
            <textarea id="embedCode" readonly rows="3" style="width: 100%; background: #0a0a14; border: 1px solid #2a2a4a; border-radius: 8px; padding: 0.8rem; font-family: monospace; font-size: 0.8rem; color: #00d4ff; margin-top: 0.5rem; resize: none;"></textarea>
            <button onclick="navigator.clipboard.writeText(document.getElementById('embedCode').value).then(()=>alert('Copied!'))" style="margin-top: 0.8rem; background: linear-gradient(135deg, #00d4ff, #0090ff); color: #000; font-weight: 700; padding: 8px 20px; border: none; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-copy me-1"></i> Copy Embed Code
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showEmbed(gameId, streamId, title) {
    const code = `<iframe src="{{ url('/embed') }}/${gameId}/${streamId}" width="100%" height="500" frameborder="0" allowfullscreen></iframe>`;
    document.getElementById('embedTitle').textContent = title;
    document.getElementById('embedCode').value = code;
    document.getElementById('embedModal').style.display = 'flex';
}
</script>
@endpush
