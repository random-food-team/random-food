<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

function rf_lower(string $value): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    $upper = preg_split('//u', 'ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚÝĂĐĨŨƠƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỂỄỆỈỊỌỎỐỒỔỖỘỚỞỠỢỤỦỨỪỬỮỰỲỶỸ', -1, PREG_SPLIT_NO_EMPTY);
    $lower = preg_split('//u', 'abcdefghijklmnopqrstuvwxyzàáâãèéêìíòóôõùúýăđĩũơưạảấầẩẫậắằẳẵặẹẻẽếềểễệỉịọỏốồổỗộớởỡợụủứừửữựỳỷỹ', -1, PREG_SPLIT_NO_EMPTY);
    return strtolower(strtr($value, array_combine($upper, $lower)));
}

function rf_contains(string $text, string $query): bool
{
    return strpos(rf_lower($text), rf_lower($query)) !== false;
}

function rf_substr(string $value, int $length): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $length, 'UTF-8');
    }
    $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    return implode('', array_slice($characters ?: [], 0, $length));
}

final class FoodModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
        if (!isset($_SESSION['randomfood']) || !is_array($_SESSION['randomfood'])) {
            $_SESSION['randomfood'] = [
                'guestFavorites' => [],
                'guestHistory' => [],
                'settings' => ['region' => 'Tất cả', 'vegetarianOnly' => false, 'notifications' => true],
            ];
        }
    }

    public function state(): array
    {
        $user = $this->currentUser();
        return [
            'categories' => $this->categories(),
            'favorites' => $user ? $this->favoritesForUser($user['id']) : $_SESSION['randomfood']['guestFavorites'],
            'history' => $user ? $this->historyForUser($user['id']) : $_SESSION['randomfood']['guestHistory'],
            'user' => $user,
            'users' => $this->users(),
            'reviews' => $this->reviews(),
            'settings' => $user ? $this->settingsForUser($user['id']) : $_SESSION['randomfood']['settings'],
        ];
    }

    public function categories(): array
    {
        $statement = $this->db->query(
            'SELECT c.id, c.name, c.emoji, COUNT(m.id) AS `count`
             FROM categories c LEFT JOIN meals m ON m.category_id = c.id
             GROUP BY c.id, c.name, c.emoji ORDER BY c.name'
        );
        return $statement->fetchAll();
    }

    public function meals(): array
    {
        $statement = $this->db->query(
            'SELECT m.id, m.name, m.category_id AS category, c.name AS categoryName,
                    m.emoji, m.rating, m.review_count AS reviews, m.price, m.location,
                    m.prep_time AS `time`, m.description, m.image, m.is_seed
             FROM meals m JOIN categories c ON c.id = m.category_id
             ORDER BY m.name'
        );
        return $statement->fetchAll();
    }

    public function meal(string $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT m.id, m.name, m.category_id AS category, c.name AS categoryName,
                    m.emoji, m.rating, m.review_count AS reviews, m.price, m.location,
                    m.prep_time AS `time`, m.description, m.image, m.is_seed
             FROM meals m JOIN categories c ON c.id = m.category_id WHERE m.id = :id'
        );
        $statement->execute(['id' => $id]);
        $meal = $statement->fetch();
        return $meal ?: null;
    }

    public function visibleMeals(string $query = '', string $category = 'all', ?string $region = null): array
    {
        $settings = $this->settings();
        $selectedRegion = $region ?? $settings['region'];
        $query = trim($query);

        return array_values(array_filter($this->meals(), static function (array $meal) use ($query, $category, $settings, $selectedRegion): bool {
            $matchesQuery = $query === '' || rf_contains($meal['name'], $query);
            $matchesCategory = $category === 'all' || $meal['category'] === $category;
            $matchesRegion = $selectedRegion === 'Tất cả' || $meal['location'] === $selectedRegion;
            $matchesDiet = !$settings['vegetarianOnly'] || $meal['category'] === 'vegetarian';
            return $matchesQuery && $matchesCategory && $matchesRegion && $matchesDiet;
        }));
    }

    public function pickRandomMeal(string $query = '', string $category = 'all', ?string $region = null): ?array
    {
        $meals = $this->visibleMeals($query, $category, $region);
        if ($meals === []) {
            return null;
        }

        $user = $this->currentUser();
        $history = $user ? $this->historyForUser($user['id']) : $_SESSION['randomfood']['guestHistory'];
        $recent = array_column(array_slice($history, 0, 3), 'mealId');
        $choices = array_values(array_filter($meals, static fn(array $meal): bool => !in_array($meal['id'], $recent, true)));
        $pool = $choices !== [] ? $choices : $meals;
        $meal = $pool[random_int(0, count($pool) - 1)];

        if ($user) {
            $statement = $this->db->prepare('INSERT INTO random_history (user_id, meal_id) VALUES (:user_id, :meal_id)');
            $statement->execute(['user_id' => $user['id'], 'meal_id' => $meal['id']]);
        } else {
            array_unshift($_SESSION['randomfood']['guestHistory'], ['mealId' => $meal['id'], 'date' => date(DATE_ATOM)]);
            $_SESSION['randomfood']['guestHistory'] = array_slice($_SESSION['randomfood']['guestHistory'], 0, 30);
        }

        return $meal;
    }

    public function toggleFavorite(string $id): bool
    {
        if (!$this->meal($id)) {
            throw new RuntimeException('Không tìm thấy món ăn.');
        }

        $user = $this->currentUser();
        if ($user) {
            $statement = $this->db->prepare('SELECT 1 FROM favorites WHERE user_id = :user_id AND meal_id = :meal_id');
            $statement->execute(['user_id' => $user['id'], 'meal_id' => $id]);
            if ($statement->fetchColumn()) {
                $delete = $this->db->prepare('DELETE FROM favorites WHERE user_id = :user_id AND meal_id = :meal_id');
                $delete->execute(['user_id' => $user['id'], 'meal_id' => $id]);
                return false;
            }
            $insert = $this->db->prepare('INSERT INTO favorites (user_id, meal_id) VALUES (:user_id, :meal_id)');
            $insert->execute(['user_id' => $user['id'], 'meal_id' => $id]);
            return true;
        }

        $favorites = $_SESSION['randomfood']['guestFavorites'];
        if (in_array($id, $favorites, true)) {
            $_SESSION['randomfood']['guestFavorites'] = array_values(array_diff($favorites, [$id]));
            return false;
        }
        $_SESSION['randomfood']['guestFavorites'][] = $id;
        return true;
    }

    public function addMeal(string $name, string $category): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new RuntimeException('Hãy nhập tên món ăn.');
        }
        $categoryInfo = $this->category($category);
        if (!$categoryInfo) {
            throw new RuntimeException('Danh mục món ăn không hợp lệ.');
        }
        $duplicate = $this->db->prepare('SELECT 1 FROM meals WHERE name = :name');
        $duplicate->execute(['name' => $name]);
        if ($duplicate->fetchColumn()) {
            throw new RuntimeException('Món ăn này đã có trong danh sách.');
        }

        $statement = $this->db->prepare(
            'INSERT INTO meals (id, category_id, name, emoji, description, image)
             VALUES (:id, :category_id, :name, :emoji, :description, :image)'
        );
        $statement->execute([
            'id' => 'custom-' . bin2hex(random_bytes(6)),
            'category_id' => $category,
            'name' => $name,
            'emoji' => $categoryInfo['emoji'],
            'description' => 'Món ' . rf_lower($categoryInfo['name']) . ' do bạn thêm vào danh sách.',
            'image' => '',
        ]);
    }

    public function removeMeal(string $id): void
    {
        $statement = $this->db->prepare('DELETE FROM meals WHERE id = :id AND is_seed = 0');
        $statement->execute(['id' => $id]);
        if ($statement->rowCount() === 0) {
            throw new RuntimeException('Không tìm thấy món tự thêm để xóa.');
        }
    }

    public function addCategory(string $name, string $emoji): void
    {
        $name = trim($name);
        if ($name === '') {
            throw new RuntimeException('Hãy nhập tên danh mục.');
        }
        $duplicate = $this->db->prepare('SELECT 1 FROM categories WHERE name = :name');
        $duplicate->execute(['name' => $name]);
        if ($duplicate->fetchColumn()) {
            throw new RuntimeException('Danh mục này đã tồn tại.');
        }

        $statement = $this->db->prepare('INSERT INTO categories (id, name, emoji) VALUES (:id, :name, :emoji)');
        $statement->execute([
            'id' => 'category-' . bin2hex(random_bytes(6)),
            'name' => $name,
            'emoji' => trim($emoji) !== '' ? rf_substr(trim($emoji), 4) : '🍽️',
        ]);
    }

    public function removeCategory(string $id): void
    {
        $statement = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        try {
            $statement->execute(['id' => $id]);
        } catch (PDOException $error) {
            if (($error->errorInfo[0] ?? null) === '23000') {
                throw new RuntimeException('Hãy xóa các món trong danh mục này trước.');
            }
            throw $error;
        }
        if ($statement->rowCount() === 0) {
            throw new RuntimeException('Không tìm thấy danh mục để xóa.');
        }
    }

    public function register(string $name, string $email, string $password): void
    {
        $name = trim($name);
        $email = strtolower(trim($email));
        if ($name === '') {
            throw new RuntimeException('Hãy nhập họ tên của bạn.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email chưa hợp lệ.');
        }
        if (strlen($password) < 6) {
            throw new RuntimeException('Mật khẩu cần có ít nhất 6 ký tự.');
        }
        $duplicate = $this->db->prepare('SELECT 1 FROM users WHERE email = :email');
        $duplicate->execute(['email' => $email]);
        if ($duplicate->fetchColumn()) {
            throw new RuntimeException('Email này đã được đăng ký.');
        }

        $id = 'user-' . bin2hex(random_bytes(12));
        $statement = $this->db->prepare(
            "INSERT INTO users (id, name, email, password_hash) VALUES (:id, :name, :email, :password_hash)"
        );
        $statement->execute([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);
        $this->startUserSession($id);
    }

    public function login(string $email, string $password): void
    {
        $email = strtolower(trim($email));
        $statement = $this->db->prepare(
            "SELECT id, password_hash FROM users WHERE email = :email AND status = 'Hoạt động'"
        );
        $statement->execute(['email' => $email]);
        $account = $statement->fetch();
        if (!$account || !$account['password_hash'] || !password_verify($password, $account['password_hash'])) {
            throw new RuntimeException('Email hoặc mật khẩu không chính xác.');
        }
        $this->startUserSession($account['id']);
    }

    public function logout(): void
    {
        unset($_SESSION['randomfood']['userId']);
        session_regenerate_id(true);
    }

    public function isAdmin(): bool
    {
        return ($this->currentUser()['role'] ?? null) === 'admin';
    }

    public function updateUser(string $name): void
    {
        $name = trim($name);
        $user = $this->currentUser();
        if (!$user) {
            throw new RuntimeException('Hãy đăng nhập để cập nhật hồ sơ.');
        }
        if ($name === '') {
            throw new RuntimeException('Tên hiển thị không được để trống.');
        }
        $statement = $this->db->prepare('UPDATE users SET name = :name WHERE id = :id');
        $statement->execute(['name' => $name, 'id' => $user['id']]);
    }

    public function updateSettings(string $region, bool $vegetarianOnly, bool $notifications): void
    {
        if (!in_array($region, ['Tất cả', 'Miền Bắc', 'Miền Trung', 'Miền Nam'], true)) {
            throw new RuntimeException('Vùng miền không hợp lệ.');
        }

        $user = $this->currentUser();
        $settings = [
            'region' => $region,
            'vegetarianOnly' => $vegetarianOnly,
            'notifications' => $notifications,
        ];
        if (!$user) {
            $_SESSION['randomfood']['settings'] = $settings;
            return;
        }

        $statement = $this->db->prepare(
            'INSERT INTO user_settings (user_id, region, vegetarian_only, notifications)
             VALUES (:user_id, :region, :vegetarian_only, :notifications)
             ON DUPLICATE KEY UPDATE region = VALUES(region),
                 vegetarian_only = VALUES(vegetarian_only), notifications = VALUES(notifications)'
        );
        $statement->execute([
            'user_id' => $user['id'],
            'region' => $region,
            'vegetarian_only' => (int)$vegetarianOnly,
            'notifications' => (int)$notifications,
        ]);
    }

    public function addReview(string $mealId, int $rating, string $text): void
    {
        if (!$this->meal($mealId)) {
            throw new RuntimeException('Không tìm thấy món ăn để đánh giá.');
        }
        $text = trim($text);
        if ($text === '' || $rating < 1 || $rating > 5) {
            throw new RuntimeException('Vui lòng nhập nhận xét và mức đánh giá hợp lệ.');
        }

        $user = $this->currentUser();
        $statement = $this->db->prepare(
            "INSERT INTO reviews (id, user_id, meal_id, reviewer_name, rating, review_text, status)
             VALUES (:id, :user_id, :meal_id, :reviewer_name, :rating, :review_text, 'pending')"
        );
        $statement->execute([
            'id' => 'review-' . bin2hex(random_bytes(6)),
            'user_id' => $user['id'] ?? null,
            'meal_id' => $mealId,
            'reviewer_name' => $user['name'] ?? 'Khách',
            'rating' => $rating,
            'review_text' => $text,
        ]);
    }

    public function approveReview(string $id): void
    {
        $statement = $this->db->prepare("UPDATE reviews SET status = 'approved' WHERE id = :id");
        $statement->execute(['id' => $id]);
        if ($statement->rowCount() === 0) {
            $exists = $this->db->prepare('SELECT 1 FROM reviews WHERE id = :id');
            $exists->execute(['id' => $id]);
            if (!$exists->fetchColumn()) {
                throw new RuntimeException('Không tìm thấy đánh giá cần cập nhật.');
            }
        }
    }

    private function currentUser(): ?array
    {
        $id = $_SESSION['randomfood']['userId'] ?? null;
        if (!is_string($id) || $id === '') {
            return null;
        }
        $statement = $this->db->prepare('SELECT id, name, email, role FROM users WHERE id = :id AND status = :status');
        $statement->execute(['id' => $id, 'status' => 'Hoạt động']);
        $user = $statement->fetch();
        if (!$user) {
            unset($_SESSION['randomfood']['userId']);
            return null;
        }
        return $user;
    }

    private function startUserSession(string $id): void
    {
        session_regenerate_id(true);
        $_SESSION['randomfood']['userId'] = $id;
    }

    private function users(): array
    {
        $statement = $this->db->query('SELECT id, name, email, role, status FROM users ORDER BY name');
        $users = $statement->fetchAll();
        foreach ($users as &$user) {
            $user['role'] = $user['role'] === 'admin' ? 'Quản trị' : 'Thành viên';
        }
        unset($user);
        return $users;
    }

    private function reviews(): array
    {
        $statement = $this->db->query(
            'SELECT id, reviewer_name AS name, meal_id AS mealId, rating, review_text AS `text`, status
             FROM reviews ORDER BY created_at DESC, id DESC'
        );
        $reviews = $statement->fetchAll();
        foreach ($reviews as &$review) {
            $review['status'] = $review['status'] === 'pending' ? 'Chờ duyệt' : 'Đã duyệt';
            $review['rating'] = (int)$review['rating'];
        }
        unset($review);
        return $reviews;
    }

    private function favoritesForUser(string $userId): array
    {
        $statement = $this->db->prepare('SELECT meal_id FROM favorites WHERE user_id = :user_id ORDER BY created_at DESC');
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    private function historyForUser(string $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT meal_id AS mealId, created_at AS `date` FROM random_history
             WHERE user_id = :user_id ORDER BY created_at DESC, id DESC LIMIT 30'
        );
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll();
    }

    private function settingsForUser(string $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT region, vegetarian_only, notifications FROM user_settings WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);
        $settings = $statement->fetch();
        if (!$settings) {
            return ['region' => 'Tất cả', 'vegetarianOnly' => false, 'notifications' => true];
        }
        return [
            'region' => $settings['region'],
            'vegetarianOnly' => (bool)$settings['vegetarian_only'],
            'notifications' => (bool)$settings['notifications'],
        ];
    }

    private function settings(): array
    {
        $user = $this->currentUser();
        return $user
            ? $this->settingsForUser($user['id'])
            : $_SESSION['randomfood']['settings'];
    }

    private function category(string $id): ?array
    {
        $statement = $this->db->prepare('SELECT id, name, emoji FROM categories WHERE id = :id');
        $statement->execute(['id' => $id]);
        $category = $statement->fetch();
        return $category ?: null;
    }
}
