<section class="auth-page">
  <div class="auth-art"><span>🍜</span><p>Thêm một chút ngẫu nhiên,<br>thêm nhiều niềm vui.</p><small>RANDOM FOOD · MÓN NGON MỖI NGÀY</small></div>
  <form class="auth-card" method="post" action="<?= h(route_url('register')) ?>">
    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="register"><input type="hidden" name="return_page" value="register">
    <span class="section-kicker">THAM GIA CÙNG CHÚNG MÌNH</span><h1>Tạo tài khoản</h1><p>Lưu món yêu thích và khám phá thêm nhiều món ngon.</p>
    <label>Họ và tên<input name="name" type="text" autocomplete="name" required placeholder="Tên của bạn"></label>
    <label>Email<input name="email" type="email" autocomplete="email" required placeholder="you@example.com"></label>
    <label>Mật khẩu<input name="password" type="password" minlength="6" autocomplete="new-password" required placeholder="Ít nhất 6 ký tự"></label>
    <button class="button button-primary auth-submit" type="submit">Đăng ký <span>→</span></button>
    <p class="demo-note">Bản giao diện thử nghiệm: chưa kết nối xác thực hoặc máy chủ.</p><p class="auth-switch">Đã có tài khoản? <a href="<?= h(route_url('login')) ?>">Đăng nhập</a></p>
  </form>
</section>
