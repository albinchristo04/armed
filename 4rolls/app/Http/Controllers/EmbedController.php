<?php

namespace App\Http\Controllers;

use App\Models\EmbedLog;
use App\Models\Game;
use App\Models\Stream;
use Illuminate\Http\Request;

class EmbedController extends Controller
{
    public function show(Game $game, Stream $stream, Request $request)
    {
        // Log the embed impression
        EmbedLog::create([
            'game_id' => $game->id,
            'stream_id' => $stream->id,
            'referrer' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $servers = $game->streams;

        return view('embed.player', compact('game', 'stream', 'servers'));
    }
}
