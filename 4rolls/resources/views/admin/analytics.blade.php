@extends('layouts.admin')

@section('content')
<h2 class="mb-4"><i class="fas fa-chart-line me-2"></i>Analytics Dashboard</h2>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="text-muted mb-1">Total Embed Views</div>
            <h3>{{ number_format($totalViews) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="text-muted mb-1">Today's Views</div>
            <h3>{{ number_format($todayViews) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="text-muted mb-1">Active Matches</div>
            <h3>{{ number_format($totalGames) }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Top Referrers --}}
    <div class="col-md-6">
        <div class="card bg-dark border-secondary">
            <div class="card-header border-secondary"><i class="fas fa-globe me-2"></i>Top Referrers</div>
            <div class="card-body p-0">
                <table class="table table-dark-custom mb-0">
                    <thead><tr><th>Domain</th><th>Views</th></tr></thead>
                    <tbody>
                        @forelse($topReferrers as $ref)
                        <tr>
                            <td>{{ $ref->referrer ?: 'Direct' }}</td>
                            <td><span class="badge bg-info text-dark">{{ number_format($ref->views) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-muted text-center py-3">No data yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Matches --}}
    <div class="col-md-6">
        <div class="card bg-dark border-secondary">
            <div class="card-header border-secondary"><i class="fas fa-trophy me-2"></i>Top Matches by Views</div>
            <div class="card-body p-0">
                <table class="table table-dark-custom mb-0">
                    <thead><tr><th>Match</th><th>Views</th></tr></thead>
                    <tbody>
                        @forelse($topGames as $g)
                        <tr>
                            <td>{{ $g->home_team }} vs {{ $g->away_team }}</td>
                            <td><span class="badge bg-info text-dark">{{ number_format($g->views) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-muted text-center py-3">No data yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Daily Views Chart --}}
<div class="card bg-dark border-secondary mt-4">
    <div class="card-header border-secondary"><i class="fas fa-chart-bar me-2"></i>Daily Views (Last 30 Days)</div>
    <div class="card-body">
        <canvas id="dailyChart" height="100"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('dailyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($dailyViews->pluck('date')) !!},
        datasets: [{
            label: 'Views',
            data: {!! json_encode($dailyViews->pluck('views')) !!},
            backgroundColor: 'rgba(0, 212, 255, 0.6)',
            borderColor: '#00d4ff',
            borderWidth: 1,
        }]
    },
    options: {
        scales: { y: { beginAtZero: true, ticks: { color: '#b0b0c0' } }, x: { ticks: { color: '#b0b0c0' } } },
        plugins: { legend: { labels: { color: '#e0e0e0' } } },
    }
});
</script>
@endpush
