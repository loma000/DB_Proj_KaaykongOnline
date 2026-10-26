<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in - KaayKongKruaOnline</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#222; --muted:#777; --line:#e2e2e2; --red:#e11d2e; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:'Inter',system-ui,sans-serif; color:var(--ink); }
        .layout { display:grid; grid-template-columns:1fr 1fr; min-height:100vh; }

        /* left */
        .left { background:#f3f3f3; padding:28px 28px 24px 28px; display:flex; flex-direction:column; justify-content:space-between; }
        .brand { display:flex; align-items:center; gap:8px; font-weight:600; font-size:15px; }
        .logo { width:18px; height:18px; background:var(--red); border-radius:5px; display:grid; place-items:center; }
        .logo i { width:8px; height:5px; background:#fff; border-radius:0 0 4px 4px; display:block; }
        .hero { max-width:300px; }
        .hero h1 { font-size:24px; line-height:1.2; margin:0 0 12px; font-weight:600; }
        .hero p { font-size:12px; color:var(--muted); line-height:1.5; margin:0 0 14px; }
        .chips { display:flex; flex-wrap:wrap; gap:8px; }
        .chip { font-size:10px; color:#555; padding:4px 10px; border:1px solid var(--line); border-radius:999px; background:#fff; }
        .foot { font-size:10px; color:var(--muted); }

        /* right */
        .right { display:grid; place-items:center; padding:32px; background:#fff; }
        .form { width:100%; max-width:320px; }
        .form h2 { margin:0; font-size:18px; font-weight:600; }
        .sub { margin:4px 0 18px; font-size:11px; color:var(--muted); }
        .row { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
        label { font-size:11px; font-weight:500; }
        .link { font-size:10px; color:var(--ink); text-decoration: underline; font-weight:500; }
        .input { position:relative; margin-bottom:14px; }
        .input input {
            width:100%; height:34px; padding:0 12px; font:inherit; font-size:12px;
            border:1px solid var(--line); border-radius:6px; background:#fff;
        }
        .input input:focus { outline:none; border-color:var(--ink); }
        .eye { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:0; cursor:pointer; padding:0; width:auto; height:auto; color:#555; }
        .remember { display:flex; align-items:center; gap:8px; font-size:11px; margin:2px 0 14px; }
        .remember input { width:12px; height:12px; margin:0; }
        .btn { width:100%; height:34px; border:0; border-radius:6px; background:#222; color:#fff; font:inherit; font-size:12px; font-weight:500; cursor:pointer; }
        .btn:hover { background:#000; }
        .alt { text-align:center; font-size:11px; color:var(--muted); margin:16px 0 0; }
        .alt + .alt { margin-top:12px; }
        .alt a { color:var(--ink); font-weight:500; text-decoration:underline; }
        .error { color:#dc2626; font-size:11px; margin:-8px 0 12px; }

        @media (max-width:800px) { .layout{grid-template-columns:1fr;} .left{display:none;} }
    </style>
</head>
<body>
<div class="layout">
    <aside class="left">
        <div class="brand"><span class="logo"><i></i></span> KaayKongKruaOnline</div>

        <div class="hero">
            <h1>Tools worth keeping on the counter.</h1>
            <p>Cookware, knives and bakeware, picked for kitchens that get used every day.</p>
            <div class="chips">
                <span class="chip">Cookware</span><span class="chip">Knives</span>
                <span class="chip">Bakeware</span><span class="chip">Linens</span>
            </div>
        </div>

        <div class="foot">Free returns on every order for 60 years.</div>
    </aside>

    <main class="right">
        <form class="form" method="POST" action="{{ route('login') }}">
            @csrf
            <h2>Welcome back</h2>
            <p class="sub">Sign in to see your orders and saved items.</p>

            <div class="row"><label for="email">Email</label></div>
            <div class="input">
                <input id="email" name="email" type="email" placeholder="you@example.com"
                       value="{{ old('email') }}" autocomplete="username" required autofocus>
            </div>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <div class="row">
                <label for="password">Password</label>
                <a class="link" href="#">Forgot password?</a>
            </div>
            <div class="input">
                <input id="password" name="password" type="password" placeholder="Your password" required>
                <button type="button" class="eye" onclick="togglePw()" aria-label="Show password">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label class="remember"><input type="checkbox" name="remember"> Keep me signed in</label>

            <button class="btn" type="submit">Sign in</button>

            <p class="alt">New to KaayKongKruaOnline? <a href="{{ route('register') }}">Create an account</a></p>
            
        </form>
    </main>
</div>

<script>
    function togglePw() {
        const i = document.getElementById('password');
        i.type = i.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>