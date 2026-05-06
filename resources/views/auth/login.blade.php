<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #333; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; font-size: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Login</h2>
        <form action="/login" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        @if($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif
        <p>Don't have account? <a href="/register">Register</a></p>
        <div class="mt-2 text-center">
    <a href="{{ route('password.request') }}" class="small text-muted">Forgot Password?</a>
</div>
    </div>
</body>
</html>