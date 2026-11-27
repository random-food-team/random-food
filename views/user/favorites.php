<?php
$favoriteMeals = array_values(array_filter($meals, static fn(array $meal): bool => in_array($meal['id'], $state['favorites'], true)));
?>
<section class="page-intro"><span class="section-kicker">BỘ SƯU TẬP CỦA BẠN</span><h1>Món yêu thích</h1><p>Lưu lại món ngon để lần sau tìm nhanh hơn.</p></section>
<?= render_meal_grid($favoriteMeals, $state, 'Bạn chưa lưu món nào. Hãy bấm ♡ trên một món để lưu.') ?>
