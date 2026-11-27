<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../models/Database.php';

$json = file_get_contents(__DIR__ . '/../models/food-data.json');
if ($json === false) {
    throw new RuntimeException('Không thể đọc models/food-data.json.');
}

$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
if (!is_array($data) || !isset($data['categories'], $data['meals'], $data['users'], $data['reviews'])) {
    throw new RuntimeException('Dữ liệu mẫu không đúng định dạng.');
}

$db = Database::connect();
$db->beginTransaction();

try {
    $categoryInsert = $db->prepare('INSERT IGNORE INTO categories (id, name, emoji) VALUES (:id, :name, :emoji)');
    foreach ($data['categories'] as $category) {
        $categoryInsert->execute([
            'id' => $category['id'],
            'name' => $category['name'],
            'emoji' => $category['emoji'],
        ]);
    }

    $mealInsert = $db->prepare(
        'INSERT IGNORE INTO meals
            (id, category_id, name, emoji, rating, review_count, price, location, prep_time, description, image, is_seed)
         VALUES
            (:id, :category_id, :name, :emoji, :rating, :review_count, :price, :location, :prep_time, :description, :image, 1)'
    );
    foreach ($data['meals'] as $meal) {
        $mealInsert->execute([
            'id' => $meal['id'],
            'category_id' => $meal['category'],
            'name' => $meal['name'],
            'emoji' => $meal['emoji'],
            'rating' => $meal['rating'],
            'review_count' => $meal['reviews'],
            'price' => $meal['price'],
            'location' => $meal['location'],
            'prep_time' => $meal['time'],
            'description' => $meal['description'],
            'image' => $meal['image'],
        ]);
    }

    $userInsert = $db->prepare(
        'INSERT IGNORE INTO users (id, name, email, role, status)
         VALUES (:id, :name, :email, :role, :status)'
    );
    foreach ($data['users'] as $user) {
        $userInsert->execute([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'] === 'Quản trị' ? 'admin' : 'user',
            'status' => $user['status'],
        ]);
    }

    $reviewInsert = $db->prepare(
        'INSERT IGNORE INTO reviews
            (id, meal_id, reviewer_name, rating, review_text, status)
         VALUES
            (:id, :meal_id, :reviewer_name, :rating, :review_text, :status)'
    );
    foreach ($data['reviews'] as $review) {
        $reviewInsert->execute([
            'id' => $review['id'],
            'meal_id' => $review['mealId'],
            'reviewer_name' => $review['name'],
            'rating' => $review['rating'],
            'review_text' => $review['text'],
            'status' => $review['status'] === 'Đã duyệt' ? 'approved' : 'pending',
        ]);
    }

    $db->commit();
    echo "Đã nạp dữ liệu mẫu vào MySQL.\n";
} catch (Throwable $error) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    throw $error;
}
