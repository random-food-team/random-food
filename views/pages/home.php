<?php
$featured = $meals[0] ?? null;
?>
<section class="hero">
  <div class="hero-copy">
    <span class="hero-badge">✦ Mỗi bữa ăn, một khám phá mới</span>
    <h1>Hôm nay ăn gì?<br>Để chúng mình<br><span>chọn nhé!</span></h1>
    <p>Tạm biệt câu hỏi khó nhất mỗi ngày. Tìm món hợp gu, khám phá vị mới và để một chút ngẫu nhiên làm bữa ăn thú vị hơn.</p>
    <div class="hero-actions"><a class="button button-primary" href="<?= h(route_url('random')) ?>"><span>⤨</span> Random Food</a><a class="button button-light" href="<?= h(route_url('explore')) ?>"><span>↗</span> Khám phá món ăn</a></div>
    <span class="hero-note">✧ Hương vị Việt · Chọn theo sở thích của bạn</span>
  </div>
  <?php if ($featured): ?>
    <a class="hero-food" href="<?= h(route_url('detail', ['id' => $featured['id']])) ?>"><?= render_meal_image($featured, 'hero-image') ?>
      <span class="hero-food-caption"><span><small>GỢI Ý CHO MỘT NGÀY THẬT NGON</small><strong><?= h($featured['name']) ?></strong></span><b>☆ <?= number_format((float)$featured['rating'], 1) ?></b></span>
    </a>
  <?php endif; ?>
</section>
<section class="quick-pick panel">
  <div class="quick-pick-heading"><div><h2>Một chạm, một món ngon.</h2><p>Chọn danh mục bạn thích. Phần còn lại cứ để Random Food lo.</p></div><a class="button button-primary button-small" href="<?= h(route_url('random')) ?>">⤨ &nbsp; Random Food</a></div>
  <div class="chip-row"><a class="filter-chip" href="<?= h(route_url('explore')) ?>">Tất cả</a>
    <?php foreach ($state['categories'] as $category): ?><a class="filter-chip" href="<?= h(route_url('explore', ['category' => $category['id']])) ?>"><?= h($category['name']) ?></a><?php endforeach; ?>
  </div>
</section>
<section class="content-section">
  <div class="section-title"><div><span class="section-kicker">DANH MỤC</span><h2>Bạn đang thèm gì?</h2></div><a href="<?= h(route_url('explore')) ?>">Khám phá tất cả →</a></div>
  <div class="category-grid"><?= render_category_cards($state['categories'], $meals) ?></div>
</section>
<section class="content-section">
  <div class="section-title"><div><span class="section-kicker">GỢI Ý HÔM NAY</span><h2>Món ngon được yêu thích</h2></div><a href="<?= h(route_url('explore')) ?>">Xem tất cả →</a></div>
  <?= render_meal_grid(array_slice($visibleMeals, 0, 4), $state) ?>
</section>
