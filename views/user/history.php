<section class="page-intro"><span class="section-kicker">NHỮNG LẦN CHỌN GẦN ĐÂY</span><h1>Lịch sử random</h1><p>Món đã được chọn trên phiên trình duyệt này.</p></section>
<?php if ($state['history']): ?>
  <div class="history-list">
    <?php foreach ($state['history'] as $entry): $historyMeal = null; foreach ($meals as $candidate) { if ($candidate['id'] === $entry['mealId']) { $historyMeal = $candidate; break; } } if (!$historyMeal) continue; ?>
      <a class="history-item panel" href="<?= h(route_url('detail', ['id' => $historyMeal['id']])) ?>"><?= render_meal_image($historyMeal) ?>
        <span><small><?= h(date('d/m/Y H:i', strtotime($entry['date']))) ?></small><strong><?= h($historyMeal['name']) ?></strong><small><?= h(category_name($historyMeal, $state['categories'])) ?></small></span><b>↗</b>
      </a>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <div class="empty-card"><span>⤨</span><p>Bạn chưa random món nào. Thử một lượt nhé!</p><?= post_form_open('pick-random', 'random') ?><button class="button button-primary" type="submit">Random Food</button></form></div>
<?php endif; ?>
