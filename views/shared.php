<?php
declare(strict_types=1);

const PAGE_META = [
    'home' => ['Trang chủ', 'Chào mừng bạn đến với Random Food'],
    'explore' => ['Khám phá món ăn', 'Tìm món ngon đúng gu'],
    'random' => ['Random Food', 'Để chúng mình chọn món cho bạn'],
    'detail' => ['Chi tiết món ăn', 'Khám phá hương vị'],
    'login' => ['Đăng nhập', 'Chào mừng bạn quay lại'],
    'register' => ['Đăng ký', 'Tạo tài khoản Random Food'],
    'profile' => ['Hồ sơ cá nhân', 'Thông tin tài khoản'],
    'favorites' => ['Món yêu thích', 'Những món bạn muốn thử'],
    'history' => ['Lịch sử random', 'Những lựa chọn gần đây'],
    'admin' => ['Dashboard Admin', 'Tổng quan hoạt động'],
    'meals' => ['Quản lý món ăn', 'Danh sách món trong hệ thống'],
    'categories' => ['Quản lý danh mục', 'Tổ chức danh sách món'],
    'users' => ['Quản lý người dùng', 'Tài khoản trên hệ thống'],
    'reviews' => ['Quản lý đánh giá', 'Kiểm duyệt phản hồi'],
    'settings' => ['Cài đặt', 'Tùy chỉnh trải nghiệm của bạn'],
];

const ADMIN_LINKS = [
    ['admin', 'Tổng quan', '▦'],
    ['meals', 'Món ăn', '♧'],
    ['categories', 'Danh mục', '◫'],
    ['users', 'Người dùng', '♙'],
    ['reviews', 'Đánh giá', '☆'],
];

function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function route_url(string $page, array $params = []): string
{
    return 'index.php?' . http_build_query(['page' => $page] + $params, '', '&', PHP_QUERY_RFC3986);
}

function page_title(string $page): string
{
    return PAGE_META[$page][0] ?? PAGE_META['home'][0];
}

function post_form_open(string $action, string $page, string $class = ''): string
{
    return '<form method="post" action="' . h(route_url($page)) . '" class="' . h($class) . '">'
        . '<input type="hidden" name="csrf_token" value="' . h($_SESSION['csrf_token'] ?? '') . '">'
        . '<input type="hidden" name="action" value="' . h($action) . '">'
        . '<input type="hidden" name="return_page" value="' . h($page) . '">';
}

function category_name(array $meal, array $categories): string
{
    foreach ($categories as $category) {
        if ($category['id'] === $meal['category']) {
            return $category['name'];
        }
    }
    return $meal['categoryName'] ?? 'Món mới';
}

function render_meal_image(array $meal, string $className = ''): string
{
    $color = ($meal['category'] ?? '') === 'vegetarian' ? '#dce9cf' : '#f8dfca';
    $image = !empty($meal['image'])
        ? '<img src="' . h($meal['image']) . '" alt="' . h($meal['name']) . '" loading="lazy">'
        : '';
    return '<div class="food-image ' . h($className) . '" style="--meal-color:' . $color . '">'
        . '<span class="food-image-fallback" aria-hidden="true">' . h($meal['emoji'] ?? '🍽️') . '</span>' . $image . '</div>';
}

function render_meal_card(array $meal, array $state): string
{
    $favorite = in_array($meal['id'], $state['favorites'], true);
    $html = '<article class="meal-card"><a class="meal-card-link" href="' . h(route_url('detail', ['id' => $meal['id']])) . '" aria-label="Xem ' . h($meal['name']) . '">'
        . render_meal_image($meal)
        . '<span class="meal-card-info"><span class="meal-category">' . h(category_name($meal, $state['categories'])) . ' · ' . h($meal['location']) . '</span>'
        . '<strong>' . h($meal['name']) . '</strong><span class="meal-card-meta"><span>★ ' . (!empty($meal['rating']) ? number_format((float)$meal['rating'], 1) : 'Mới')
        . '</span><span>' . h($meal['price']) . '</span></span></span></a>'
        . post_form_open('toggle-favorite', (string)($_GET['page'] ?? 'home'), 'favorite-form')
        . '<input type="hidden" name="id" value="' . h($meal['id']) . '"><button class="favorite-button ' . ($favorite ? 'is-favorite' : '') . '" type="submit" aria-label="' . ($favorite ? 'Bỏ yêu thích' : 'Thêm vào yêu thích') . '" aria-pressed="' . ($favorite ? 'true' : 'false') . '">' . ($favorite ? '♥' : '♡') . '</button></form></article>';
    return $html;
}

function render_meal_grid(array $meals, array $state, string $emptyText = 'Chưa có món nào ở đây.'): string
{
    if ($meals === []) {
        return '<div class="empty-card"><span>🍽️</span><p>' . h($emptyText) . '</p></div>';
    }
    return '<div class="meal-grid">' . implode('', array_map(static fn(array $meal): string => render_meal_card($meal, $state), $meals)) . '</div>';
}

function render_category_cards(array $categories, array $meals): string
{
    $html = '';
    foreach ($categories as $category) {
        $count = count(array_filter($meals, static fn(array $meal): bool => $meal['category'] === $category['id']));
        $html .= '<a class="category-card" href="' . h(route_url('explore', ['category' => $category['id']])) . '"><span class="category-icon">'
            . h($category['emoji']) . '</span><strong>' . h($category['name']) . '</strong><span>' . $count . ' món</span></a>';
    }
    return $html;
}

function render_review_table(array $reviews, array $meals, bool $controls = true): string
{
    $html = '<div class="table-wrap"><table><thead><tr><th>Người dùng</th><th>Món ăn</th><th>Đánh giá</th><th>Nội dung</th><th>Trạng thái</th>'
        . ($controls ? '<th></th>' : '') . '</tr></thead><tbody>';
    foreach ($reviews as $review) {
        $mealName = 'Món đã xóa';
        foreach ($meals as $meal) {
            if ($meal['id'] === $review['mealId']) {
                $mealName = $meal['name'];
                break;
            }
        }
        $html .= '<tr><td><strong>' . h($review['name']) . '</strong></td><td>' . h($mealName) . '</td><td>★ ' . (int)$review['rating']
            . '</td><td class="review-cell">' . h($review['text']) . '</td><td><span class="status-pill '
            . ($review['status'] === 'Chờ duyệt' ? 'is-pending' : '') . '">' . h($review['status']) . '</span></td>';
        if ($controls) {
            $html .= '<td>' . post_form_open('approve-review', 'reviews')
                . '<input type="hidden" name="id" value="' . h($review['id']) . '"><button class="table-action" type="submit">Duyệt</button></form></td>';
        }
        $html .= '</tr>';
    }
    return $html . '</tbody></table></div>';
}
