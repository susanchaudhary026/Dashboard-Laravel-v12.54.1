<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; background: #f4f4f4; display: flex; height: 100vh; }
        .sidebar { width: 220px; background: #333; color: white; padding: 20px; flex-shrink: 0; }
        .sidebar a { display: block; color: white; padding: 10px; text-decoration: none; border-radius: 4px; }
        .sidebar a:hover { background: #444; }
        .content { flex: 1; padding: 30px; overflow-y: auto; }
        .topbar { background: white; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .card { background: white; padding: 25px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border: none; }
        .btn-edit {
        padding: 5px 5px;
        border-radius: 10px;
        cursor: pointer;
        border: 0;
        background-color: white;
        box-shadow: rgb(0 0 0 / 5%) 0 0 8px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        font-size: 15px;
        transition: all 0.5s ease;
        }

        .btn-edit:hover {
        letter-spacing: 3px;
        background-color: hsl(261deg 80% 48%);
        color: hsl(0, 0%, 100%);
        box-shadow: rgb(93 24 220) 0px 7px 29px 0px;
        }

        .btn-edit:active {
        letter-spacing: 3px;
        background-color: hsl(261deg 80% 48%);
        color: hsl(0, 0%, 100%);
        box-shadow: rgb(93 24 220) 0px 0px 0px 0px;
        transform: translateY(10px);
        transition: 100ms;
        }

        .sidebar h2 { font-size: 1.5rem; margin-bottom: 20px; }
        
        
        td a { color:#333; text-decoration: none; }
        td a:hover { text-decoration: underline; }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Menu</h2>
        <a href="/dashboard">Dashboard</a>
        <a href="{{ route('articles.index') }}">Articles</a>
        <a href="{{ route('categories.index') }}">Categories</a>
        <a href="{{ route('files.index') }}">File Manager</a>
        <a href="{{ route('news.index') }}">News</a>
        <a href="{{ route('live.index') }}">Live Session</a>
        @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
        <a href="{{ route('live.admin') }}">Live Admin Panel</a>
        @endif
        
        @if(Auth::check() && Auth::user()->role === 'superadmin' || Auth::user()->role === 'admin')
        <li class="nav-item">
            <a class="nav-link {{request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="fas fa-user-shield"></i> <span> Manage Users </span>

            </a>
        </li>
        @endif
    </div>
    
    <div class="content">
        <div class="topbar">
    @auth
        <div> 
            <strong>Welcome {{ Auth::user()->name }} ({{ Auth::user()->role }}) ⚙️</strong>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-dark btn-sm">Logout</button>
        </form>
    @else
        <div><strong>Please Login to continue</strong></div>
       <div>
            <a href="/" class="btn btn-primary btn-sm text-white">Login</a>
        </ div>
    @endauth
</div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>