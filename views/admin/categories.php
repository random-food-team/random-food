<?php
$categoryRows = [];
foreach ($state['categories'] as $categoryItem) {
    $categoryItem['count'] = count(array_filter($meals, static fn(array $meal): bool => $meal['category'] === $categoryItem['id']));
    $categoryRows[] = $categoryItem;
}
?>
<section class="panel admin-panel">
  <div class="section-title"><div><span class="section-kicker">PHÂN LOẠI</span><h2><?= count($categoryRows) ?> danh mục</h2></div></div>
  <?= post_form_open('add-category', 'categories', 'inline-form') ?><input name="name" maxlength="32" required placeholder="Tên danh mục"><input name="emoji" maxlength="4" placeholder="Emoji" aria-label="Emoji danh mục"><button class="button button-primary button-small" type="submit">＋ Thêm danh mục</button></form>
  <div class="table-wrap"><table><thead><tr><th>Danh mục</th><th>Số món</th><th></th></tr></thead><tbody>
    <?php foreach ($categoryRows as $categoryItem): ?><tr><td><span class="table-food"><?= h($categoryItem['emoji']) ?> <strong><?= h($categoryItem['name']) ?></strong></span></td><td><?= $categoryItem['count'] ?> món</td><td>
      <?php if (str_starts_with($categoryItem['id'], 'category-')): ?><?= post_form_open('delete-category', 'categories') ?><input type="hidden" name="id" value="<?= h($categoryItem['id']) ?>"><button class="table-action danger" type="submit">Xóa</button></form><?php else: ?><span class="muted">Mặc định</span><?php endif; ?>
    </td></tr><?php endforeach; ?>
  </tbody></table></div>
</section>
