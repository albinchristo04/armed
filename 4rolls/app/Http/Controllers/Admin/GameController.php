<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::withCount('streams')
            ->when($request->search, fn($q, $s) => $q->where('home_team', 'like', "%{$s}%")->orWhere('away_team', 'like', "%{$s}%"))
            ->latest()
            ->paginate(20);

        return view('admin.games.index', compact('games'));
    }

    public function edit(Game $game)
    {
        $game->load('streams');
        return view('admin.games.edit', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $game->update($request->validate([
            'home_team' => 'required|string|max:255',
            'away_team' => 'nullable|string|max:255',
            'league' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'is_important' => 'boolean',
        ]));

        return redirect()->route('admin.games.index')->with('success', 'Match updated.');
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->route('admin.games.index')->with('success', 'Match deleted.');
    }

    public function toggleImportant(Game $game)
    {
        $game->update(['is_important' => !$game->is_important]);
        return back()->with('success', ($game->is_important ? 'Marked' : 'Unmarked') . ' as important.');
    }

    // ── Import Flow ──

    public function importForm()
    {
        return view('admin.games.import');
    }

    public function importFetch(Request $request)
    {
        $request->validate(['json_url' => 'required|url']);

        $json = @file_get_contents($request->json_url);
        if (!$json) {
            return back()->withErrors(['json_url' => 'Could not fetch the JSON file.']);
        }

        $data = json_decode($json, true);
        if (!$data || !isset($data['matches'])) {
            return back()->withErrors(['json_url' => 'Invalid JSON structure.']);
        }

        $matches = $data['matches'];

        // Store temporarily in session
        session(['import_matches' => $matches]);

        return view('admin.games.import-select', compact('matches'));
    }

    public function importSave(Request $request)
    {
        $request->validate(['selected' => 'required|array']);
        $allMatches = session('import_matches', []);
        $selected = $request->selected;
        $imported = 0;

        foreach ($allMatches as $index => $match) {
            if (!in_array($index, $selected)) continue;

            $slug = Str::slug($match['home_team'] . '-vs-' . ($match['away_team'] ?? 'live'));

            $game = Game::updateOrCreate(
                ['slug' => $slug],
                [
                    'home_team' => $match['home_team'] ?? '',
                    'away_team' => $match['away_team'] ?? '',
                    'league' => $match['league'] ?? '',
                    'is_important' => strtolower($match['league'] ?? '') === 'important games',
                    'home_logo' => $match['home_logo'] ?? null,
                    'away_logo' => $match['away_logo'] ?? null,
                    'league_logo' => $match['league_logo'] ?? null,
                    'status' => $match['status'] ?? null,
                ]
            );

            // Sync streams
            $game->streams()->delete();
            foreach (($match['iframe_urls'] ?? []) as $i => $url) {
                $game->streams()->create([
                    'server_name' => 'Server ' . ($i + 1),
                    'url' => $url,
                ]);
            }

            $imported++;
        }

        session()->forget('import_matches');

        return redirect()->route('admin.games.index')
            ->with('success', "{$imported} matches imported successfully.");
    }
}
