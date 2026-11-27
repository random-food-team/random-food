<section class="page-intro"><span class="section-kicker">TÌM MÓN HỢP GU</span><h1>Khám phá món ăn</h1><p>Từ món quen thuộc đến hương vị mới — có món ngon cho mọi tâm trạng.</p></section>
<form class="explore-toolbar panel" method="get" action="index.php">
  <input type="hidden" name="page" value="explore">
  <label class="search-field"><span>⌕</span><span class="visually-hidden">Tìm món ăn</span><input name="q" type="search" value="<?= h($query) ?>" placeholder="Tìm tên món ăn..." autocomplete="off"></label>
  <label class="select-field"><span class="visually-hidden">Lọc theo vùng miền</span><select name="region"><option value="Tất cả">Tất cả vùng miền</option><?php foreach (['Miền Bắc', 'Miền Trung', 'Miền Nam'] as $region): ?><option value="<?= h($region) ?>"<?= ($selectedRegion ?? $state['settings']['region']) === $region ? ' selected' : '' ?>><?= h($region) ?></option><?php endforeach; ?></select></label>
  <label class="select-field"><span class="visually-hidden">Danh mục</span><select name="category"><option value="all">Tất cả món</option><?php foreach ($state['categories'] as $categoryItem): ?><option value="<?= h($categoryItem['id']) ?>"<?= $category === $categoryItem['id'] ? ' selected' : '' ?>><?= h($categoryItem['name']) ?></option><?php endforeach; ?></select></label>
  <button class="button button-primary button-small" type="submit">Tìm món</button>
</form>
<div class="section-title results-title"><h2><?= count($visibleMeals) ?> món ăn</h2><a class="button button-primary button-small" href="<?= h(route_url('random')) ?>">⤨ Random Food</a></div>
<?= render_meal_grid($visibleMeals, $state, 'Không tìm thấy món phù hợp. Hãy thử từ khóa khác.') ?>
