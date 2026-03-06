@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-futbol me-2"></i>Manage Matches</h2>
    <a href="{{ route('admin.import.form') }}" class="btn btn-cyber"><i class="fas fa-file-import me-1"></i> Import New</a>
</div>

<form class="mb-3" method="GET">
    <div class="input-group" style="max-width: 400px;">
        <input type="text" name="search" class="form-control bg-dark text-white border-secondary" placeholder="Search teams..." value="{{ request('search') }}">
        <button class="btn btn-outline-info" type="submit"><i class="fas fa-search"></i></button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-dark-custom">
        <thead>
            <tr>
                <th>#</th>
                <th>Match</th>
                <th>League</th>
                <th>Status</th>
                <th>Servers</th>
                <th>Important</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($games as $game)
            <tr>
                <td>{{ $game->id }}</td>
                <td>
                    @if($game->home_logo)<img src="{{ $game->home_logo }}" width="20" class="me-1">@endif
                    <strong>{{ $game->home_team }}</strong>
                    <span class="text-muted mx-1">vs</span>
                    <strong>{{ $game->away_team }}</strong>
                    @if($game->away_logo)<img src="{{ $game->away_logo }}" width="20" class="ms-1">@endif
                </td>
                <td>
                    @if($game->league_logo)<img src="{{ $game->league_logo }}" width="20" class="me-1">@endif
                    {{ $game->league }}
                </td>
                <td><span class="badge bg-secondary">{{ $game->status ?: 'N/A' }}</span></td>
                <td><span class="badge bg-info text-dark">{{ $game->streams_count }}</span></td>
                <td>
                    <form method="POST" action="{{ route('admin.games.toggle-important', $game) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $game->is_important ? 'btn-warning' : 'btn-outline-secondary' }}">
                            <i class="fas fa-star"></i>
                        </button>
                    </form>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-outline-info" title="Edit"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-outline-success" title="Copy Embed Link" onclick="copyEmbed({{ $game->id }}, {{ $game->streams->first()?->id ?? 0 }})">
                            <i class="fas fa-code"></i>
                        </button>
                        <button class="btn btn-outline-light" title="Copy Match Link" onclick="copyLink('{{ route('match', $game->slug) }}')">
                            <i class="fas fa-link"></i>
                        </button>
                        <form method="POST" action="{{ route('admin.games.destroy', $game) }}" class="d-inline" onsubmit="return confirm('Delete this match?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No matches found. <a href="{{ route('admin.import.form') }}">Import some!</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $games->links() }}

@endsection

@push('scripts')
<script>
function copyEmbed(gameId, streamId) {
    const code = `<iframe src="{{ url('/embed') }}/${gameId}/${streamId}" width="100%" height="500" frameborder="0" allowfullscreen></iframe>`;
    navigator.clipboard.writeText(code).then(() => alert('Embed code copied!'));
}
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => alert('Match link copied!'));
}
</script>
@endpush
