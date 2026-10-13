<div class="login-wrap">
    <div class="login-card">
        <h1>PharmaDEM</h1>
        <p class="muted">Sign in to continue</p>

        <form method="post" action="index.php?r=login.submit">
            <input type="hidden" name="csrf" value="<?= e(Auth::csrfToken()) ?>">

            <label>Email</label>
            <input type="email" name="email" required autofocus>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Sign in</button>
        </form>

        <div class="demo-creds">
            Demo accounts, password is <code>password123</code><br>
            manager@pharmadem.in<br>
            arjun@pharmadem.in
        </div>
    </div>
</div>
