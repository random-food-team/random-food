<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/FoodModel.php';
require_once __DIR__ . '/../views/shared.php';

final class AppController
{
    private FoodModel $model;
    private array $pages = [
        'home', 'explore', 'random', 'detail', 'login', 'register', 'profile',
        'favorites', 'history', 'admin', 'meals', 'categories', 'users', 'reviews', 'settings',
    ];

    public function __construct()
    {
        $this->model = new FoodModel();
    }

    public function run(): void
    {
        $page = (string)($_GET['page'] ?? 'home');
        if (!in_array($page, $this->pages, true)) {
            $page = 'home';
        }

        if (in_array($page, ['admin', 'meals', 'categories', 'users', 'reviews'], true) && !$this->model->isAdmin()) {
            $_SESSION['randomfood_flash'] = 'Vui lòng đăng nhập bằng tài khoản quản trị.';
            $this->redirect('login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost($page);
        }

        $state = $this->model->state();
        $query = trim((string)($_GET['q'] ?? ''));
        $category = (string)($_GET['category'] ?? 'all');
        $selectedRegion = (string)($_GET['region'] ?? $state['settings']['region']);
        if (!in_array($selectedRegion, ['Tất cả', 'Miền Bắc', 'Miền Trung', 'Miền Nam'], true)) {
            $selectedRegion = $state['settings']['region'];
        }
        if ($category !== 'all' && !in_array($category, array_column($state['categories'], 'id'), true)) {
            $category = 'all';
        }
        $meals = $this->model->meals();
        $visibleMeals = $this->model->visibleMeals($query, $category, $selectedRegion);
        $currentMeal = $page === 'detail' ? $this->model->meal((string)($_GET['id'] ?? '')) : null;
        if ($page === 'detail' && !$currentMeal) {
            http_response_code(404);
        }

        $notice = (string)($_SESSION['randomfood_flash'] ?? '');
        unset($_SESSION['randomfood_flash']);
        $pageTitle = page_title($page);
        require __DIR__ . '/../views/layout.php';
    }

    private function handlePost(string $page): never
    {
        $postedToken = (string)($_POST['csrf_token'] ?? '');
        if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), $postedToken)) {
            $_SESSION['randomfood_flash'] = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
            $this->redirect($page);
        }

        $action = (string)($_POST['action'] ?? '');
        if (in_array($action, ['add-meal', 'delete-meal', 'add-category', 'delete-category', 'approve-review'], true)
            && !$this->model->isAdmin()) {
            $_SESSION['randomfood_flash'] = 'Bạn không có quyền thực hiện thao tác này.';
            $this->redirect('login');
        }

        $redirectPage = (string)($_POST['return_page'] ?? $page);
        if (!in_array($redirectPage, $this->pages, true)) {
            $redirectPage = 'home';
        }

        try {
            switch ($action) {
                case 'pick-random':
                    $meal = $this->model->pickRandomMeal(
                        (string)($_POST['q'] ?? ''),
                        (string)($_POST['category'] ?? 'all'),
                        (string)($_POST['region'] ?? 'Tất cả')
                    );
                    if (!$meal) {
                        throw new RuntimeException('Không có món phù hợp. Hãy đổi bộ lọc ở trang Khám phá.');
                    }
                    $this->redirect('random', ['id' => $meal['id']]);
                case 'toggle-favorite':
                    $added = $this->model->toggleFavorite((string)($_POST['id'] ?? ''));
                    $_SESSION['randomfood_flash'] = $added ? 'Đã lưu vào món yêu thích.' : 'Đã bỏ khỏi món yêu thích.';
                    break;
                case 'login':
                    $this->model->login(
                        (string)($_POST['email'] ?? ''),
                        (string)($_POST['password'] ?? '')
                    );
                    $_SESSION['randomfood_flash'] = 'Đăng nhập thành công.';
                    $this->redirect('profile');
                case 'register':
                    $this->model->register(
                        (string)($_POST['name'] ?? ''),
                        (string)($_POST['email'] ?? ''),
                        (string)($_POST['password'] ?? '')
                    );
                    $_SESSION['randomfood_flash'] = 'Tạo tài khoản thành công.';
                    $this->redirect('profile');
                case 'logout':
                    $this->model->logout();
                    $_SESSION['randomfood_flash'] = 'Bạn đã đăng xuất.';
                    $this->redirect('home');
                case 'update-profile':
                    $this->model->updateUser((string)($_POST['name'] ?? ''));
                    $_SESSION['randomfood_flash'] = 'Đã cập nhật hồ sơ.';
                    break;
                case 'add-meal':
                    $this->model->addMeal((string)($_POST['name'] ?? ''), (string)($_POST['category'] ?? ''));
                    $_SESSION['randomfood_flash'] = 'Đã thêm món ăn.';
                    break;
                case 'delete-meal':
                    $this->model->removeMeal((string)($_POST['id'] ?? ''));
                    $_SESSION['randomfood_flash'] = 'Đã xóa món ăn.';
                    break;
                case 'add-category':
                    $this->model->addCategory((string)($_POST['name'] ?? ''), (string)($_POST['emoji'] ?? ''));
                    $_SESSION['randomfood_flash'] = 'Đã thêm danh mục.';
                    break;
                case 'delete-category':
                    $this->model->removeCategory((string)($_POST['id'] ?? ''));
                    $_SESSION['randomfood_flash'] = 'Đã xóa danh mục.';
                    break;
                case 'approve-review':
                    $this->model->approveReview((string)($_POST['id'] ?? ''));
                    $_SESSION['randomfood_flash'] = 'Đánh giá đã được duyệt.';
                    break;
                case 'add-review':
                    $mealId = (string)($_POST['id'] ?? '');
                    $this->model->addReview($mealId, (int)($_POST['rating'] ?? 0), (string)($_POST['text'] ?? ''));
                    $_SESSION['randomfood_flash'] = 'Đánh giá đã lưu ở trạng thái chờ duyệt.';
                    $this->redirect('detail', ['id' => $mealId]);
                case 'update-settings':
                    $this->model->updateSettings(
                        (string)($_POST['region'] ?? 'Tất cả'),
                        isset($_POST['vegetarianOnly']),
                        isset($_POST['notifications'])
                    );
                    $_SESSION['randomfood_flash'] = 'Đã lưu cài đặt.';
                    break;
                default:
                    throw new RuntimeException('Thao tác không hợp lệ.');
            }
        } catch (PDOException $error) {
            error_log($error->getMessage());
            $_SESSION['randomfood_flash'] = 'Không thể lưu dữ liệu. Vui lòng kiểm tra kết nối database và thử lại.';
        } catch (RuntimeException $error) {
            $_SESSION['randomfood_flash'] = $error->getMessage();
        }

        $params = [];
        if ($redirectPage === 'detail' && isset($_POST['id'])) {
            $params['id'] = (string)$_POST['id'];
        }
        $this->redirect($redirectPage, $params);
    }

    private function redirect(string $page, array $params = []): never
    {
        header('Location: ' . route_url($page, $params), true, 303);
        exit;
    }
}
