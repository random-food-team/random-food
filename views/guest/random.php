<?php
$randomMeal = null;
if (isset($_GET['id'])) {
    foreach ($meals as $candidate) {
        if ($candidate['id'] === (string)$_GET['id']) { $randomMeal = $candidate; break; }
    }
}
if (!$randomMeal && !empty($state['history'][0]['mealId'])) {
    foreach ($meals as $candidate) {
        if ($candidate['id'] === $state['history'][0]['mealId']) { $randomMeal = $candidate; break; }
    }
}
$randomMeal ??= $visibleMeals[0] ?? ($meals[0] ?? null);
?>
<section class="page-intro centered-intro"><span class="section-kicker">ĐỪNG LO, ĐÃ CÓ RANDOM FOOD</span><h1>Bữa này để may mắn chọn nhé!</h1><p>Ngẫu nhiên một chút để mỗi bữa ăn đều có điều thú vị.</p></section>
<?php if ($randomMeal): ?>
  <section class="random-stage panel"><div class="random-flower">✳</div><?= render_meal_image($randomMeal, 'random-image') ?>
    <span class="meal-category"><?= h(category_name($randomMeal, $state['categories'])) ?> · <?= h($randomMeal['location']) ?></span>
    <h2><?= h($randomMeal['name']) ?></h2><p><?= h($randomMeal['description']) ?></p>
    <span class="rating-line">★ <?= !empty($randomMeal['rating']) ? number_format((float)$randomMeal['rating'], 1) : 'Mới' ?> <span>(<?= (int)$randomMeal['reviews'] ?> đánh giá)</span></span>
    <div class="hero-actions centered-actions">
      <?= post_form_open('pick-random', 'random') ?><button class="button button-primary" type="submit">⤨ &nbsp; Thử món khác</button></form>
      <a class="button button-light" href="<?= h(route_url('detail', ['id' => $randomMeal['id']])) ?>">Xem chi tiết ↗</a>
    </div>
  </section>
<?php else: ?><div class="empty-card"><p>Chưa có món phù hợp để chọn.</p><a class="button button-primary" href="<?= h(route_url('explore')) ?>">Khám phá món ăn</a></div><?php endif; ?>
