<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $game->home_team }} vs {{ $game->away_team }} - 4rolls</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 100%; height: 100%; overflow: hidden; background: #000; font-family: 'Segoe UI', sans-serif; }

        .player-wrap { position: relative; width: 100%; height: 100%; }
        .player-wrap iframe { width: 100%; height: 100%; border: none; }

        /* Server switcher overlay */
        .server-bar {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.9));
            padding: 12px 16px 8px;
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
            opacity: 0; transition: opacity 0.3s;
        }
        .player-wrap:hover .server-bar { opacity: 1; }

        .server-bar .label { color: #888; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-right: 4px; }
        .server-btn {
            background: rgba(255,255,255,0.1); color: #ccc; border: 1px solid rgba(255,255,255,0.15);
            padding: 4px 12px; border-radius: 4px; font-size: 11px; cursor: pointer; transition: all 0.2s;
            text-decoration: none;
        }
        .server-btn:hover { background: rgba(0,212,255,0.2); border-color: #00d4ff; color: #00d4ff; }
        .server-btn.active { background: #00d4ff; color: #000; border-color: #00d4ff; font-weight: 700; }

        .branding {
            position: absolute; top: 8px; right: 12px;
            font-size: 10px; color: rgba(255,255,255,0.3); text-decoration: none;
            opacity: 0; transition: opacity 0.3s;
        }
        .player-wrap:hover .branding { opacity: 1; }
    </style>
</head>
<body>
    <div class="player-wrap">
        <iframe src="{{ $stream->url }}" allowfullscreen></iframe>

        <div class="server-bar">
            <span class="label">Server:</span>
            @foreach($servers as $s)
            <a href="{{ route('embed.show', [$game->id, $s->id]) }}" class="server-btn {{ $s->id === $stream->id ? 'active' : '' }}">
                {{ $s->server_name }}
            </a>
            @endforeach
        </div>

        <a href="{{ url('/') }}" target="_blank" class="branding">Powered by 4rolls.com</a>
    </div>

    {{-- Popup Ad Placeholder - Replace with your ad network script --}}
    <script>
        // Popup ad trigger: fires on first user interaction
        let adFired = false;
        document.addEventListener('click', function() {
            if (adFired) return;
            adFired = true;
            // INSERT YOUR POPUP AD SCRIPT HERE
            // Example: window.open('https://your-ad-url', '_blank');
        }, { once: true });
    </script>
</body>
</html>
