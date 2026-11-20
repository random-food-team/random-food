<?php
declare(strict_types=1);

function rf_lower(string $value): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }

    $upper = preg_split('//u', 'ABCDEFGHIJKLMNOPQRSTUVWXYZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚÝĂĐĨŨƠƯẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼẾỀỂỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪỬỮỰỲỶỸ', -1, PREG_SPLIT_NO_EMPTY);
    $lower = preg_split('//u', 'abcdefghijklmnopqrstuvwxyzàáâãèéêìíòóôõùúýăđĩũơưạảấầẩẫậắằẳẵặẹẻẽếềểễệỉịọỏốồổỗộớờởỡợụủứừửữựỳỷỹ', -1, PREG_SPLIT_NO_EMPTY);
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
    private array $data;

    public function __construct()
    {
        $json = file_get_contents(__DIR__ . '/food-data.json');
        if ($json === false) {
            throw new RuntimeException('Không thể đọc dữ liệu món ăn.');
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($data) || !isset($data['meals'], $data['categories'], $data['users'], $data['reviews'])) {
            throw new RuntimeException('Dữ liệu món ăn không đúng định dạng.');
        }
        $this->data = $data;

        if (!isset($_SESSION['randomfood']) || !is_array($_SESSION['randomfood'])) {
            $_SESSION['randomfood'] = [
                'customMeals' => [],
                'categories' => $data['categories'],
                'favorites' => [],
                'history' => [],
                'user' => null,
                'users' => $data['users'],
                'reviews' => $data['reviews'],
                'settings' => ['region' => 'Tất cả', 'vegetarianOnly' => false, 'notifications' => true],
            ];
        }
    }

    public function state(): array
    {
        return $_SESSION['randomfood'];
    }

    public function categories(): array
    {
        return $this->state()['categories'];
    }

    public function meals(): array
    {
        $state = $this->state();
        return array_merge($this->data['meals'], $state['customMeals']);
    }

    public function meal(string $id): ?array
    {
        foreach ($this->meals() as $meal) {
            if ($meal['id'] === $id) {
                return $meal;
            }
        }
        return null;
    }

    public function visibleMeals(string $query = '', string $category = 'all', ?string $region = null): array
    {
        $state = $this->state();
        $settings = $state['settings'];
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

        $state = $this->state();
        $recent = array_column(array_slice($state['history'], 0, 3), 'mealId');
        $choices = array_values(array_filter($meals, static fn(array $meal): bool => !in_array($meal['id'], $recent, true)));
        $pool = $choices !== [] ? $choices : $meals;
        $meal = $pool[random_int(0, count($pool) - 1)];
        array_unshift($state['history'], ['mealId' => $meal['id'], 'date' => date(DATE_ATOM)]);
        $state['history'] = array_slice($state['history'], 0, 30);
        $_SESSION['randomfood'] = $state;
        return $meal;
    }

    public function toggleFavorite(string $id): bool
    {
        if (!$this->meal($id)) {
            throw new RuntimeException('Không tìm thấy món ăn.');
        }
        $state = $this->state();
        if (in_array($id, $state['favorites'], true)) {
            $state['favorites'] = array_values(array_diff($state['favorites'], [$id]));
            $added = false;
        } else {
            $state['favorites'][] = $id;
            $added = true;
        }
        $_SESSION['randomfood'] = $state;
        return $added;
    }

    public function addMeal(string $name, string $category): void
    {
        $name = trim($name);
        $state = $this->state();
        if ($name === '') {
            throw new RuntimeException('Hãy nhập tên món ăn.');
        }
        $categoryInfo = $this->category($category);
        if (!$categoryInfo) {
            throw new RuntimeException('Danh mục món ăn không hợp lệ.');
        }
        foreach ($this->meals() as $meal) {
            if (rf_lower($meal['name']) === rf_lower($name)) {
                throw new RuntimeException('Món ăn này đã có trong danh sách.');
            }
        }
        array_unshift($state['customMeals'], [
            'id' => 'custom-' . bin2hex(random_bytes(6)),
            'name' => $name,
            'category' => $category,
            'categoryName' => $categoryInfo['name'],
            'emoji' => $categoryInfo['emoji'],
            'rating' => 0,
            'reviews' => 0,
            'price' => 'Chưa cập nhật',
            'location' => 'Chưa phân vùng',
            'time' => 'Chưa cập nhật',
            'description' => 'Món ' . rf_lower($categoryInfo['name']) . ' do bạn thêm vào danh sách.',
            'image' => '',
        ]);
        $_SESSION['randomfood'] = $state;
    }

    public function removeMeal(string $id): void
    {
        $state = $this->state();
        $before = count($state['customMeals']);
        $state['customMeals'] = array_values(array_filter($state['customMeals'], static fn(array $meal): bool => $meal['id'] !== $id));
        if (count($state['customMeals']) === $before) {
            throw new RuntimeException('Không tìm thấy món tự thêm để xóa.');
        }
        $state['favorites'] = array_values(array_diff($state['favorites'], [$id]));
        $_SESSION['randomfood'] = $state;
    }

    public function addCategory(string $name, string $emoji): void
    {
        $name = trim($name);
        $state = $this->state();
        if ($name === '') {
            throw new RuntimeException('Hãy nhập tên danh mục.');
        }
        foreach ($state['categories'] as $category) {
            if (rf_lower($category['name']) === rf_lower($name)) {
                throw new RuntimeException('Danh mục này đã tồn tại.');
            }
        }
        $state['categories'][] = [
            'id' => 'category-' . bin2hex(random_bytes(6)),
            'name' => $name,
            'emoji' => trim($emoji) !== '' ? rf_substr(trim($emoji), 4) : '🍽️',
            'count' => 0,
        ];
        $_SESSION['randomfood'] = $state;
    }

    public function removeCategory(string $id): void
    {
        $state = $this->state();
        foreach ($state['customMeals'] as $meal) {
            if ($meal['category'] === $id) {
                throw new RuntimeException('Hãy xóa các món trong danh mục này trước.');
            }
        }
        $before = count($state['categories']);
        $state['categories'] = array_values(array_filter($state['categories'], static fn(array $category): bool => $category['id'] !== $id));
        if (count($state['categories']) === $before) {
            throw new RuntimeException('Không tìm thấy danh mục để xóa.');
        }
        $_SESSION['randomfood'] = $state;
    }

    public function login(string $name, string $email): void
    {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email chưa hợp lệ.');
        }
        $name = trim($name);
        $_SESSION['randomfood']['user'] = [
            'name' => $name !== '' ? $name : explode('@', $email)[0],
            'email' => $email,
        ];
        session_regenerate_id(true);
    }

    public function logout(): void
    {
        $_SESSION['randomfood']['user'] = null;
    }

    public function updateUser(string $name): void
    {
        $name = trim($name);
        if (!isset($_SESSION['randomfood']['user'])) {
            throw new RuntimeException('Hãy đăng nhập để cập nhật hồ sơ.');
        }
        if ($name === '') {
            throw new RuntimeException('Tên hiển thị không được để trống.');
        }
        $_SESSION['randomfood']['user']['name'] = $name;
    }

    public function updateSettings(string $region, bool $vegetarianOnly, bool $notifications): void
    {
        if (!in_array($region, ['Tất cả', 'Miền Bắc', 'Miền Trung', 'Miền Nam'], true)) {
            throw new RuntimeException('Vùng miền không hợp lệ.');
        }
        $_SESSION['randomfood']['settings'] = [
            'region' => $region,
            'vegetarianOnly' => $vegetarianOnly,
            'notifications' => $notifications,
        ];
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
        $state = $this->state();
        array_unshift($state['reviews'], [
            'id' => 'review-' . bin2hex(random_bytes(6)),
            'name' => $state['user']['name'] ?? 'Khách',
            'mealId' => $mealId,
            'rating' => $rating,
            'text' => $text,
            'status' => 'Chờ duyệt',
        ]);
        $_SESSION['randomfood'] = $state;
    }

    public function approveReview(string $id): void
    {
        $state = $this->state();
        foreach ($state['reviews'] as &$review) {
            if ($review['id'] === $id) {
                $review['status'] = 'Đã duyệt';
                unset($review);
                $_SESSION['randomfood'] = $state;
                return;
            }
        }
        unset($review);
        throw new RuntimeException('Không tìm thấy đánh giá cần cập nhật.');
    }

    private function category(string $id): ?array
    {
        foreach ($this->state()['categories'] as $category) {
            if ($category['id'] === $id) {
                return $category;
            }
        }
        return null;
    }
}
