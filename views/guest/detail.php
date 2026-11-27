<?php if (!$currentMeal): ?>
  <div class="empty-card"><span>🍽️</span><h2>Không tìm thấy món ăn</h2><a class="button button-primary" href="<?= h(route_url('explore')) ?>">Khám phá món ăn</a></div>
<?php else: ?>
  <div class="back-link"><a href="<?= h(route_url('explore')) ?>">← Quay lại khám phá</a></div>
  <section class="detail-layout">
    <?= render_meal_image($currentMeal, 'detail-image') ?>
    <div class="detail-copy"><span class="meal-category"><?= h(category_name($currentMeal, $state['categories'])) ?> · <?= h($currentMeal['location']) ?></span><h1><?= h($currentMeal['name']) ?></h1>
      <div class="rating-line">★ <?= !empty($currentMeal['rating']) ? number_format((float)$currentMeal['rating'], 1) : 'Mới' ?> <span>(<?= (int)$currentMeal['reviews'] ?> đánh giá)</span></div>
      <p><?= h($currentMeal['description']) ?></p>
      <div class="detail-facts"><span><small>GIÁ THAM KHẢO</small><strong><?= h($currentMeal['price']) ?></strong></span><span><small>THỜI GIAN CHUẨN BỊ</small><strong><?= h($currentMeal['time']) ?></strong></span></div>
      <div class="hero-actions">
        <?= post_form_open('toggle-favorite', 'detail') ?><input type="hidden" name="id" value="<?= h($currentMeal['id']) ?>"><button class="button button-primary" type="submit"><?= in_array($currentMeal['id'], $state['favorites'], true) ? '♥ Đã yêu thích' : '♡ Thêm yêu thích' ?></button></form>
        <a class="button button-light" href="#review-form">☆ Viết đánh giá</a>
      </div>
      <form class="review-form" id="review-form" method="post" action="<?= h(route_url('detail', ['id' => $currentMeal['id']])) ?>">
        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>"><input type="hidden" name="action" value="add-review"><input type="hidden" name="return_page" value="detail"><input type="hidden" name="id" value="<?= h($currentMeal['id']) ?>">
        <label>Đánh giá<select name="rating"><option value="5">★★★★★</option><option value="4">★★★★☆</option><option value="3">★★★☆☆</option><option value="2">★★☆☆☆</option><option value="1">★☆☆☆☆</option></select></label>
        <label>Nhận xét<textarea name="text" required maxlength="250" placeholder="Chia sẻ cảm nhận của bạn"></textarea></label><button class="button button-primary" type="submit">Gửi đánh giá</button>
      </form>
    </div>
  </section>
  <section class="content-section"><div class="section-title"><div><span class="section-kicker">CÓ THỂ BẠN CŨNG THÍCH</span><h2>Món ngon tương tự</h2></div></div>
    <?php $similar = array_values(array_filter($meals, static fn(array $item): bool => $item['category'] === $currentMeal['category'] && $item['id'] !== $currentMeal['id'])); ?>
    <?= render_meal_grid(array_slice($similar, 0, 4), $state) ?>
  </section>
<?php endif; ?>
