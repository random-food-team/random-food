<section class="panel admin-panel">
  <div class="section-title"><div><span class="section-kicker">DANH SÁCH MÓN</span><h2><?= count($meals) ?> món ăn</h2></div></div>
  <?= post_form_open('add-meal', 'meals', 'inline-form') ?><input name="name" maxlength="48" required placeholder="Tên món mới"><select name="category"><?php foreach ($state['categories'] as $category): ?><option value="<?= h($category['id']) ?>"><?= h($category['name']) ?></option><?php endforeach; ?></select><button class="button button-primary button-small" type="submit">＋ Thêm món</button></form>
  <div class="table-wrap"><table><thead><tr><th>Món ăn</th><th>Danh mục</th><th>Đánh giá</th><th>Giá</th><th></th></tr></thead><tbody>
    <?php foreach ($meals as $meal): ?><tr><td><span class="table-food"><?= h($meal['emoji']) ?> <strong><?= h($meal['name']) ?></strong></span></td><td><?= h(category_name($meal, $state['categories'])) ?></td><td>★ <?= !empty($meal['rating']) ? number_format((float)$meal['rating'], 1) : 'Mới' ?></td><td><?= h($meal['price']) ?></td><td>
      <?php if (str_starts_with($meal['id'], 'custom-')): ?><?= post_form_open('delete-meal', 'meals') ?><input type="hidden" name="id" value="<?= h($meal['id']) ?>"><button class="table-action danger" type="submit">Xóa</button></form><?php else: ?><span class="muted">Mẫu</span><?php endif; ?>
    </td></tr><?php endforeach; ?>
  </tbody></table></div>
</section>
