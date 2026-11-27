<?php
declare(strict_types=1);

final class Database
{
    public static function connect(): PDO
    {
        if (!extension_loaded('pdo_mysql')) {
            throw new RuntimeException('PHP chưa bật extension pdo_mysql. Hãy bật extension này rồi thử lại.');
        }

        $configPath = __DIR__ . '/database.local.php';
        $config = is_file($configPath) ? require $configPath : [];
        if (!is_array($config)) {
            throw new RuntimeException('Cấu hình database.local.php không hợp lệ.');
        }

        $host = $config['host'] ?? (getenv('DB_HOST') ?: '127.0.0.1');
        $port = $config['port'] ?? (getenv('DB_PORT') ?: '3306');
        $name = $config['name'] ?? (getenv('DB_NAME') ?: 'if0_43452788_radom_food');
        $user = $config['user'] ?? (getenv('DB_USER') ?: 'root');
        $password = $config['password'] ?? getenv('DB_PASSWORD');
        $password = is_string($password) ? $password : '';
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $error) {
            throw new RuntimeException('Không thể kết nối MySQL. Hãy kiểm tra cấu hình DB_HOST, DB_NAME, DB_USER và DB_PASSWORD.', 0, $error);
        }
    }
}
