<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $importantGames = Game::where('is_important', true)->with('streams')->get();
        $regularGames = Game::where('is_important', false)->with('streams')->orderBy('league')->get()->groupBy('league');

        return view('pages.home', compact('importantGames', 'regularGames'));
    }

    public function match($slug)
    {
        $game = Game::where('slug', $slug)->with('streams')->firstOrFail();
        return view('pages.match', compact('game'));
    }

    public function watch($slug, $streamId)
    {
        $game = Game::where('slug', $slug)->with('streams')->firstOrFail();
        $activeStream = $game->streams()->findOrFail($streamId);

        return view('pages.watch', compact('game', 'activeStream'));
    }

    public function webmasters()
    {
        $games = Game::has('streams')->with('streams')->latest()->get();
        return view('pages.webmasters', compact('games'));
    }

    public function apiMatches()
    {
        $games = Game::has('streams')->with('streams')->get();

        $output = $games->map(function ($game) {
            return [
                'id' => $game->id,
                'home_team' => $game->home_team,
                'away_team' => $game->away_team,
                'league' => $game->league,
                'is_important' => $game->is_important,
                'status' => $game->status,
                'home_logo' => $game->home_logo,
                'away_logo' => $game->away_logo,
                'league_logo' => $game->league_logo,
                'embed_url' => url("/embed/{$game->id}/{$game->streams->first()?->id}"),
                'servers' => $game->streams->map(fn($s) => [
                    'name' => $s->server_name,
                    'embed_url' => url("/embed/{$game->id}/{$s->id}"),
                ]),
            ];
        });

        return response()->json([
            'total' => $output->count(),
            'matches' => $output,
        ]);
    }
}
