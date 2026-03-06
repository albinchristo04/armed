<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - 4rolls.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f0f1a; color: #e0e0e0; font-family: 'Segoe UI', sans-serif; }
        .sidebar { width: 250px; min-height: 100vh; background: #1a1a2e; border-right: 1px solid #2a2a4a; position: fixed; }
        .sidebar .brand { padding: 1.5rem; font-size: 1.4rem; font-weight: 700; color: #00d4ff; border-bottom: 1px solid #2a2a4a; }
        .sidebar .nav-link { color: #b0b0c0; padding: 0.8rem 1.5rem; transition: all 0.2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #16213e; color: #00d4ff; border-left: 3px solid #00d4ff; }
        .sidebar .nav-link i { width: 24px; margin-right: 10px; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .stat-card { background: linear-gradient(135deg, #1a1a2e, #16213e); border: 1px solid #2a2a4a; border-radius: 12px; padding: 1.5rem; }
        .stat-card h3 { color: #00d4ff; font-size: 2rem; }
        .table-dark-custom { background: #1a1a2e; }
        .table-dark-custom th { color: #00d4ff; border-bottom: 2px solid #2a2a4a; }
        .table-dark-custom td { border-bottom: 1px solid #2a2a4a; vertical-align: middle; }
        .btn-cyber { background: linear-gradient(135deg, #00d4ff, #0090ff); border: none; color: #000; font-weight: 600; }
        .btn-cyber:hover { background: linear-gradient(135deg, #0090ff, #00d4ff); color: #000; }
        .badge-important { background: #ff4757; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand">⚡ 4rolls Admin</div>
        <nav class="nav flex-column mt-3">
            <a class="nav-link {{ request()->routeIs('admin.games.*') ? 'active' : '' }}" href="{{ route('admin.games.index') }}">
                <i class="fas fa-futbol"></i> Matches
            </a>
            <a class="nav-link {{ request()->routeIs('admin.import.*') ? 'active' : '' }}" href="{{ route('admin.import.form') }}">
                <i class="fas fa-file-import"></i> Import Matches
            </a>
            <a class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                <i class="fas fa-chart-line"></i> Analytics
            </a>
            <hr class="border-secondary mx-3">
            <a class="nav-link" href="{{ route('home') }}" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </nav>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
