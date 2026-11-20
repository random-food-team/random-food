<section class="panel admin-panel"><div class="section-title"><div><span class="section-kicker">TÀI KHOẢN</span><h2><?= count($state['users']) ?> người dùng mẫu</h2></div></div>
  <div class="table-wrap"><table><thead><tr><th>Người dùng</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th></tr></thead><tbody>
    <?php foreach ($state['users'] as $user): ?><tr><td><span class="table-food"><span class="mini-avatar"><?= h(rf_substr($user['name'], 1)) ?></span><strong><?= h($user['name']) ?></strong></span></td><td><?= h($user['email']) ?></td><td><?= h($user['role']) ?></td><td><span class="status-pill"><?= h($user['status']) ?></span></td></tr><?php endforeach; ?>
  </tbody></table></div>
</section>
