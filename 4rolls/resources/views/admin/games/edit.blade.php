@extends('layouts.admin')

@section('content')
<h2 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Match</h2>

<form method="POST" action="{{ route('admin.games.update', $game) }}" style="max-width: 600px;">
    @csrf @method('PUT')

    <div class="mb-3">
        <label class="form-label">Home Team</label>
        <input type="text" name="home_team" class="form-control bg-dark text-white border-secondary" value="{{ old('home_team', $game->home_team) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Away Team</label>
        <input type="text" name="away_team" class="form-control bg-dark text-white border-secondary" value="{{ old('away_team', $game->away_team) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">League</label>
        <input type="text" name="league" class="form-control bg-dark text-white border-secondary" value="{{ old('league', $game->league) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Status</label>
        <input type="text" name="status" class="form-control bg-dark text-white border-secondary" value="{{ old('status', $game->status) }}">
    </div>

    <div class="form-check mb-3">
        <input type="hidden" name="is_important" value="0">
        <input class="form-check-input" type="checkbox" name="is_important" value="1" id="isImportant" {{ $game->is_important ? 'checked' : '' }}>
        <label class="form-check-label" for="isImportant">Mark as Important Game</label>
    </div>

    <h5 class="mt-4 mb-3">Streams ({{ $game->streams->count() }})</h5>
    <div class="table-responsive mb-4">
        <table class="table table-dark-custom table-sm">
            <thead><tr><th>Server</th><th>URL</th></tr></thead>
            <tbody>
                @foreach($game->streams as $stream)
                <tr>
                    <td>{{ $stream->server_name }}</td>
                    <td class="text-truncate" style="max-width:300px;" title="{{ $stream->url }}">{{ $stream->url }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-cyber"><i class="fas fa-save me-1"></i> Save Changes</button>
    <a href="{{ route('admin.games.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>
@endsection
