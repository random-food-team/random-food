<?php
declare(strict_types=1);

$state = $this->model->state();
$isAdminUser = ($state['user']['role'] ?? null) === 'admin';
$isAdmin = in_array($page, ['admin', 'meals', 'categories', 'users', 'reviews'], true);
$isUser = in_array($page, ['profile', 'favorites', 'history', 'settings'], true);
$pageFolder = $isAdmin ? 'admin' : ($isUser ? 'user' : 'guest');
$pageView = __DIR__ . '/' . $pageFolder . '/' . $page . '.php';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#fff0e6">
  <meta name="description" content="RandomFood giúp bạn chọn món ăn hôm nay nhanh gọn và vui vẻ.">
  <title><?= h($pageTitle) ?> — Random Food</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E🍜%3C/text%3E%3C/svg%3E">
  <link rel="stylesheet" href="styles.css?v=<?= h((string)filemtime(__DIR__ . '/../styles.css')) ?>">
</head>
<body>
<header class="site-header">
  <a class="brand" href="<?= h(route_url('home')) ?>"><span class="brand-icon">✳</span><span>Random <b>Food</b></span></a>
  <nav class="main-nav" aria-label="Điều hướng chính">
    <?php foreach (['home' => 'Trang chủ', 'explore' => 'Khám phá', 'random' => 'Random Food'] as $route => $label): ?>
      <a class="nav-link <?= $page === $route ? 'is-active' : '' ?>" href="<?= h(route_url($route)) ?>"<?= $page === $route ? ' aria-current="page"' : '' ?>><?= h($label) ?></a>
    <?php endforeach; ?>
  </nav>
  <nav class="account-nav" aria-label="Tài khoản">
    <a class="nav-link optional-nav <?= $page === 'favorites' ? 'is-active' : '' ?>" href="<?= h(route_url('favorites')) ?>">♡ Yêu thích</a>
    <a class="nav-link optional-nav <?= $page === 'history' ? 'is-active' : '' ?>" href="<?= h(route_url('history')) ?>">Lịch sử</a>
    <?php if ($state['user']): ?>
      <a class="nav-link <?= $page === 'profile' ? 'is-active' : '' ?>" href="<?= h(route_url('profile')) ?>">Hồ sơ</a>
      <a class="button button-primary header-register" href="<?= h(route_url('settings')) ?>">Cài đặt</a>
    <?php else: ?>
      <a class="nav-link login-link <?= $page === 'login' ? 'is-active' : '' ?>" href="<?= h(route_url('login')) ?>">Đăng nhập</a>
      <a class="button button-primary header-register" href="<?= h(route_url('register')) ?>">Đăng ký</a>
    <?php endif; ?>
    <?php if ($isAdminUser): ?><a class="admin-shortcut" href="<?= h(route_url('admin')) ?>" aria-label="Khu vực quản trị">⚙</a><?php endif; ?>
  </nav>
</header>
<main class="app-main <?= $isAdmin ? 'is-admin' : '' ?>">
  <?php if ($notice !== ''): ?><div class="notice-banner" role="status"><?= h($notice) ?></div><?php endif; ?>
  <?php if ($isAdmin): ?>
    <section class="admin-page">
      <div class="admin-banner"><span class="admin-mark">RF</span><div><span class="section-kicker">RANDOM FOOD · ADMIN</span><h1><?= h(PAGE_META[$page][0]) ?></h1><p><?= h(PAGE_META[$page][1]) ?></p></div><span class="demo-pill">QUẢN TRỊ</span></div>
      <div class="admin-layout">
        <aside class="admin-sidebar panel"><span class="section-kicker">QUẢN TRỊ</span>
          <?php foreach (ADMIN_LINKS as [$route, $label, $icon]): ?>
            <a class="admin-link <?= $page === $route ? 'is-active' : '' ?>" href="<?= h(route_url($route)) ?>"><span><?= h($icon) ?></span><?= h($label) ?></a>
          <?php endforeach; ?>
          <a class="admin-link" href="<?= h(route_url('settings')) ?>"><span>⚙</span>Cài đặt</a>
        </aside>
        <div class="admin-content"><?php require $pageView; ?></div>
      </div>
      <p class="demo-note">Khu vực này chỉ dành cho tài khoản có vai trò quản trị.</p>
    </section>
  <?php else: ?>
    <?php require $pageView; ?>
  <?php endif; ?>
</main>
<footer class="site-footer">
  <a class="brand footer-brand" href="<?= h(route_url('home')) ?>"><span class="brand-icon">✳</span><span>Random <b>Food</b></span></a>
  <p>Mỗi bữa ăn, một khám phá mới. <span>♡</span></p>
  <div><a href="<?= h(route_url('settings')) ?>">Cài đặt</a><?php if ($isAdminUser): ?><a href="<?= h(route_url('admin')) ?>">Quản trị</a><?php endif; ?></div>
</footer>
</body>
</html>
