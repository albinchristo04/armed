@extends('layouts.admin')

@section('content')
<h2 class="mb-4"><i class="fas fa-list-check me-2"></i>Select Matches to Import</h2>

<form method="POST" action="{{ route('admin.import.save') }}">
    @csrf

    <div class="mb-3">
        <button type="button" class="btn btn-sm btn-outline-info" onclick="toggleAll()"><i class="fas fa-check-double me-1"></i>Select All</button>
        <button type="submit" class="btn btn-sm btn-cyber ms-2"><i class="fas fa-file-import me-1"></i> Import Selected</button>
        <span class="text-muted ms-3">{{ count($matches) }} matches found</span>
    </div>

    <div class="table-responsive">
        <table class="table table-dark-custom">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllCb" onclick="toggleAll()"></th>
                    <th>Home</th>
                    <th>Away</th>
                    <th>League</th>
                    <th>Status</th>
                    <th>Streams</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matches as $i => $match)
                <tr>
                    <td><input type="checkbox" name="selected[]" value="{{ $i }}" class="match-cb"></td>
                    <td>
                        @if(!empty($match['home_logo']))<img src="{{ $match['home_logo'] }}" width="20" class="me-1">@endif
                        {{ $match['home_team'] ?? '' }}
                    </td>
                    <td>
                        @if(!empty($match['away_logo']))<img src="{{ $match['away_logo'] }}" width="20" class="me-1">@endif
                        {{ $match['away_team'] ?? '' }}
                    </td>
                    <td>{{ $match['league'] ?? '' }}</td>
                    <td><span class="badge bg-secondary">{{ $match['status'] ?? 'N/A' }}</span></td>
                    <td><span class="badge bg-info text-dark">{{ count($match['iframe_urls'] ?? []) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <button type="submit" class="btn btn-cyber mt-3"><i class="fas fa-file-import me-1"></i> Import Selected</button>
</form>
@endsection

@push('scripts')
<script>
function toggleAll() {
    const cbs = document.querySelectorAll('.match-cb');
    const allChecked = [...cbs].every(cb => cb.checked);
    cbs.forEach(cb => cb.checked = !allChecked);
    document.getElementById('selectAllCb').checked = !allChecked;
}
</script>
@endpush
