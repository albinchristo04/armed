<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmbedLog;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $totalViews = EmbedLog::count();
        $todayViews = EmbedLog::whereDate('created_at', today())->count();
        $totalGames = Game::count();

        $topReferrers = EmbedLog::select('referrer', DB::raw('COUNT(*) as views'))
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        $topGames = Game::withCount('streams')
            ->withCount(['streams as embed_views' => function ($q) {
                // This won't work directly; we use a subquery instead
            }])
            ->get();

        // Better approach: get top games by embed log count
        $topGames = DB::table('games')
            ->leftJoin('embed_logs', 'games.id', '=', 'embed_logs.game_id')
            ->select('games.id', 'games.home_team', 'games.away_team', 'games.league', DB::raw('COUNT(embed_logs.id) as views'))
            ->groupBy('games.id', 'games.home_team', 'games.away_team', 'games.league')
            ->orderByDesc('views')
            ->limit(20)
            ->get();

        $dailyViews = EmbedLog::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as views'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.analytics', compact('totalViews', 'todayViews', 'totalGames', 'topReferrers', 'topGames', 'dailyViews'));
    }
}
