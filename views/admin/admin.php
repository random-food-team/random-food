<?php
$pendingReviews = count(array_filter($state['reviews'], static fn(array $review): bool => $review['status'] === 'Chờ duyệt'));
?>
<div class="stat-grid">
  <article class="stat-card"><span>Tổng món ăn</span><strong><?= count($meals) ?></strong><small>Đã lưu trong database</small></article>
  <article class="stat-card"><span>Người dùng</span><strong><?= count($state['users']) ?></strong><small>Tài khoản trong database</small></article>
  <article class="stat-card"><span>Đánh giá</span><strong><?= count($state['reviews']) ?></strong><small><?= $pendingReviews ?> chờ kiểm duyệt</small></article>
  <article class="stat-card"><span>Lượt yêu thích</span><strong><?= count($state['favorites']) ?></strong><small>Tài khoản hiện tại</small></article>
</div>
<section class="panel admin-panel"><div class="section-title"><div><span class="section-kicker">HOẠT ĐỘNG GẦN ĐÂY</span><h2>Đánh giá mới</h2></div><a href="<?= h(route_url('reviews')) ?>">Quản lý đánh giá →</a></div><?= render_review_table(array_slice($state['reviews'], 0, 3), $meals, false) ?></section>
