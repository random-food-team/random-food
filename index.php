<?php
declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/controllers/AppController.php';

try {
    (new AppController())->run();
} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    echo '<h1>Không thể truy cập database</h1><p>Hãy kiểm tra cấu hình MySQL và thử lại.</p>';
} catch (RuntimeException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    echo '<h1>Ứng dụng chưa sẵn sàng</h1><p>' . htmlspecialchars($error->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</p>';
}
