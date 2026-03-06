<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '4rolls - Live Sports Streaming')</title>
    <meta name="description" content="@yield('description', 'Watch live sports streams for free. Football, NBA, F1, Boxing, and more.')">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a14; color: #e0e0e0; font-family: 'Inter', sans-serif; min-height: 100vh; }

        /* Header */
        .header { background: linear-gradient(180deg, #111128, #0a0a14); padding: 1rem 2rem; border-bottom: 1px solid #1e1e3a; display: flex; justify-content: space-between; align-items: center; }
        .header .logo { font-size: 1.6rem; font-weight: 800; color: #00d4ff; text-decoration: none; }
        .header .logo span { color: #ff4757; }
        .header nav a { color: #8888aa; text-decoration: none; margin-left: 1.5rem; font-weight: 500; transition: color 0.2s; }
        .header nav a:hover { color: #00d4ff; }

        /* Container */
        .container { max-width: 1100px; margin: 0 auto; padding: 1.5rem; }

        /* League Section */
        .league-header { display: flex; align-items: center; gap: 10px; margin: 1.5rem 0 0.8rem; }
        .league-header img { width: 32px; height: 32px; border-radius: 6px; }
        .league-header h3 { font-size: 1rem; font-weight: 600; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }

        /* Important Games */
        .important-banner { background: linear-gradient(135deg, #ff4757, #c0392b); color: #fff; padding: 0.5rem 1rem; border-radius: 10px 10px 0 0; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

        /* Match Card */
        .match-card { display: flex; align-items: center; justify-content: space-between; background: #14142a; border: 1px solid #1e1e3a; border-radius: 10px; padding: 1rem 1.5rem; margin-bottom: 0.5rem; transition: all 0.25s; text-decoration: none; color: #e0e0e0; }
        .match-card:hover { background: #1a1a3a; border-color: #00d4ff44; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,212,255,0.08); }
        .match-card .team { display: flex; align-items: center; gap: 10px; font-weight: 600; flex: 1; }
        .match-card .team img { width: 28px; height: 28px; border-radius: 50%; }
        .match-card .team.right { justify-content: flex-end; text-align: right; }
        .match-card .vs { color: #555; font-size: 0.85rem; padding: 0 1rem; white-space: nowrap; text-align: center; min-width: 130px; }
        .match-card .vs .status { display: block; font-size: 0.75rem; color: #888; }
        .match-card .live-badge { background: #ff4757; color: #fff; font-size: 0.65rem; padding: 2px 8px; border-radius: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .match-card .btn-watch { background: linear-gradient(135deg, #00d4ff, #0090ff); color: #000; font-weight: 700; font-size: 0.75rem; padding: 6px 16px; border-radius: 20px; text-transform: uppercase; white-space: nowrap; }

        /* Footer */
        .footer { text-align: center; padding: 2rem; color: #555; font-size: 0.8rem; border-top: 1px solid #1e1e3a; margin-top: 3rem; }
        .footer a { color: #00d4ff; text-decoration: none; }

        @media (max-width: 768px) {
            .match-card { flex-wrap: wrap; gap: 0.5rem; padding: 0.8rem; }
            .match-card .team { font-size: 0.85rem; }
            .match-card .vs { min-width: auto; padding: 0 0.5rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="header">
        <a href="{{ route('home') }}" class="logo">4<span>rolls</span></a>
        <nav>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('webmasters') }}">Webmasters</a>
        </nav>
    </div>

    @yield('content')

    <div class="footer">
        &copy; {{ date('Y') }} 4rolls.com &mdash; <a href="{{ route('webmasters') }}">Embed our streams</a>
    </div>

    @stack('scripts')
</body>
</html>
