<?php $user = $state['user'] ?? ['name' => 'Khách trải nghiệm', 'email' => 'Chưa đăng nhập']; ?>
<section class="page-intro"><span class="section-kicker">TÀI KHOẢN CỦA BẠN</span><h1>Hồ sơ cá nhân</h1><p>Quản lý thông tin và những lựa chọn của bạn.</p></section>
<div class="profile-layout">
  <aside class="profile-nav panel"><div class="avatar"><?= h(rf_substr($user['name'], 1)) ?></div><strong><?= h($user['name']) ?></strong><span><?= h($user['email']) ?></span>
    <a class="nav-link is-active" href="<?= h(route_url('profile')) ?>">Thông tin cá nhân</a><a class="nav-link" href="<?= h(route_url('favorites')) ?>">Món yêu thích</a><a class="nav-link" href="<?= h(route_url('history')) ?>">Lịch sử random</a><a class="nav-link" href="<?= h(route_url('settings')) ?>">Cài đặt</a>
  </aside>
  <section class="panel profile-form"><h2>Thông tin cá nhân</h2><p>Thông tin chỉ được lưu trên phiên trình duyệt này trong bản demo.</p>
    <?php if ($state['user']): ?>
      <?= post_form_open('update-profile', 'profile') ?><label>Tên hiển thị<input name="name" value="<?= h($user['name']) ?>" required></label><label>Email<input value="<?= h($user['email']) ?>" disabled></label><button class="button button-primary" type="submit">Lưu thay đổi</button></form>
      <?= post_form_open('logout', 'profile') ?><button class="text-button" type="submit">Đăng xuất</button></form>
    <?php else: ?><a class="button button-light" href="<?= h(route_url('login')) ?>">Đăng nhập</a><?php endif; ?>
  </section>
</div>
