@extends('layouts.admin')

@section('content')
<h2 class="mb-4"><i class="fas fa-file-import me-2"></i>Import Matches</h2>

<div class="card bg-dark border-secondary" style="max-width: 600px;">
    <div class="card-body">
        <p class="text-muted">Paste the URL to your <code>matches.json</code> file (e.g., from GitHub raw URL).</p>
        <form method="POST" action="{{ route('admin.import.fetch') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">JSON URL</label>
                <input type="url" name="json_url" class="form-control bg-dark text-white border-secondary"
                       placeholder="https://raw.githubusercontent.com/user/repo/main/matches.json" required>
            </div>
            <button type="submit" class="btn btn-cyber"><i class="fas fa-download me-1"></i> Fetch Matches</button>
        </form>
    </div>
</div>
@endsection
