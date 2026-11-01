<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account - KaayKongKruaOnline</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#222; --muted:#777; --line:#e2e2e2; --red:#e11d2e; }
        * { box-sizing: border-box; }
        body { margin:0; font-family:'Inter',system-ui,sans-serif; color:var(--ink); }
        .layout { display:grid; grid-template-columns:1fr 1fr; min-height:100vh; }

        .left { background:#f3f3f3; padding:28px; display:flex; flex-direction:column; justify-content:space-between; }
        .brand { display:flex; align-items:center; gap:8px; font-weight:600; font-size:15px; }
        .logo { width:18px; height:18px; background:var(--red); border-radius:5px; display:grid; place-items:center; }
        .logo i { width:8px; height:5px; background:#fff; border-radius:0 0 4px 4px; display:block; }
        .hero { max-width:300px; }
        .hero h1 { font-size:24px; line-height:1.2; margin:0 0 12px; font-weight:600; }
        .hero p { font-size:12px; color:var(--muted); line-height:1.5; margin:0 0 14px; }
        .chips { display:flex; flex-wrap:wrap; gap:8px; }
        .chip { font-size:10px; color:#555; padding:4px 10px; border:1px solid var(--line); border-radius:999px; background:#fff; }
        .foot { font-size:10px; color:var(--muted); }

        .right { display:grid; place-items:center; padding:32px; background:#fff; }
        .form { width:100%; max-width:320px; }
        .form h2 { margin:0; font-size:18px; font-weight:600; }
        .sub { margin:4px 0 18px; font-size:11px; color:var(--muted); }
        .row { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
        label { font-size:11px; font-weight:500; }
        .input { position:relative; margin-bottom:14px; }
        .input input {
            width:100%; height:34px; padding:0 12px; font:inherit; font-size:12px;
            border:1px solid var(--line); border-radius:6px; background:#fff;
        }
        .input input:focus { outline:none; border-color:var(--ink); }
        .eye { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:0; cursor:pointer; padding:0; color:#555; }
        .terms { display:flex; align-items:center; gap:8px; font-size:11px; margin:2px 0 14px; }
        .terms input { width:12px; height:12px; margin:0; }
        .terms a, .alt a { color:var(--ink); font-weight:500; text-decoration:underline; }
        .btn { width:100%; height:34px; border:0; border-radius:6px; background:#222; color:#fff; font:inherit; font-size:12px; font-weight:500; cursor:pointer; }
        .btn:hover { background:#000; }
        .alt { text-align:center; font-size:11px; color:var(--muted); margin:16px 0 0; }
        .error { color:#dc2626; font-size:11px; margin:-8px 0 12px; }

        @media (max-width:800px) { .layout{grid-template-columns:1fr;} .left{display:none;} }
    </style>
</head>
<body>
<div class="layout">
    <aside class="left">
        <div class="brand"><span class="logo"><i></i></span> KaayKongKruaOnline</div>

        <div class="hero">
            <h1>Set up your kitchen, your way.</h1>
            <p>Create an account to save your cart, track orders and share reviews.</p>
            <div class="chips">
                <span class="chip">Cookware</span><span class="chip">Knives</span>
                <span class="chip">Bakeware</span><span class="chip">Linens</span>
            </div>
        </div>

        <div class="foot">Free returns on every order for 60 years.</div>
    </aside>

    <main class="right">
        <form class="form" method="POST" action="{{ route('register.store') }}">
            @csrf
            <h2>Create your account</h2>
            <p class="sub">Join to save items, track orders and leave reviews.</p>

            <div class="row"><label for="name">Username</label></div>
            <div class="input">
                <input id="name" name="name" type="text" placeholder="somchai_j" value="{{ old('name') }}" autocomplete="username" required autofocus>
            </div>
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <div class="row"><label for="email">Email</label></div>
            <div class="input">
                <input id="email" name="email" type="email" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email" required>
            </div>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <div class="row"><label for="address">Address</label></div>
            <div class="input">
                <input id="address" name="address" type="text" placeholder="239 Huay Kaew Road, Suthep, Mueang Chiang Mai, Chiang Mai, Thailand, 50200" value="{{ old('address') }}" autocomplete="street-address">
            </div>
            @error('address') <div class="error">{{ $message }}</div> @enderror

            <div class="row"><label for="password">Password</label></div>
            <div class="input">
                <input id="password" name="password" type="password" placeholder="At least 8 characters" autocomplete="new-password" required>
                <button type="button" class="eye" onclick="togglePw()" aria-label="Show password">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label class="terms">
                <input type="checkbox" name="terms" value="1" required>
                I agree to the <a href="#">Terms</a> and <a href="#">Privacy Policy</a>
            </label>
            @error('terms') <div class="error">{{ $message }}</div> @enderror

            <button class="btn" type="submit" data-test="register-user-button">Create account</button>

            <p class="alt">Already a member? <a href="{{ route('login') }}">Sign in</a></p>
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