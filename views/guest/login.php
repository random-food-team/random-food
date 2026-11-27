<section class="auth-page">
  <div class="auth-art"><span>🍜</span><p>Thêm một chút ngẫu nhiên,<br>thêm nhiều niềm vui.</p><small>RANDOM FOOD · MÓN NGON MỖI NGÀY</small></div>
  <form class="auth-card" method="post" action="<?= h(route_url('login')) ?>">
    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="login"><input type="hidden" name="return_page" value="login">
    <span class="section-kicker">RANDOM FOOD</span><h1>Chào mừng trở lại</h1><p>Đăng nhập để lưu lại những món bạn yêu thích.</p>
    <label>Email<input name="email" type="email" autocomplete="email" required placeholder="you@example.com"></label>
    <label>Mật khẩu<input name="password" type="password" minlength="6" autocomplete="current-password" required placeholder="Ít nhất 6 ký tự"></label>
    <button class="button button-primary auth-submit" type="submit">Đăng nhập <span>→</span></button>
    <p class="auth-switch">Chưa có tài khoản? <a href="<?= h(route_url('register')) ?>">Đăng ký</a></p>
  </form>
</section>
