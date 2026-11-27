CREATE TABLE IF NOT EXISTS categories (
    id VARCHAR(64) NOT NULL,
    name VARCHAR(100) NOT NULL,
    emoji VARCHAR(16) NOT NULL DEFAULT '🍽️',
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(64) NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) DEFAULT NULL,
    role VARCHAR(16) NOT NULL DEFAULT 'user',
    status VARCHAR(32) NOT NULL DEFAULT 'Hoạt động',
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS meals (
    id VARCHAR(64) NOT NULL,
    category_id VARCHAR(64) NOT NULL,
    name VARCHAR(160) NOT NULL,
    emoji VARCHAR(16) NOT NULL DEFAULT '🍽️',
    rating DECIMAL(3, 2) NOT NULL DEFAULT 0,
    review_count INT UNSIGNED NOT NULL DEFAULT 0,
    price VARCHAR(100) NOT NULL DEFAULT 'Chưa cập nhật',
    location VARCHAR(64) NOT NULL DEFAULT 'Chưa phân vùng',
    prep_time VARCHAR(64) NOT NULL DEFAULT 'Chưa cập nhật',
    description TEXT NOT NULL,
    image TEXT NOT NULL,
    is_seed TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_meals_name (name),
    KEY idx_meals_category (category_id),
    CONSTRAINT fk_meals_category FOREIGN KEY (category_id)
        REFERENCES categories (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS favorites (
    user_id VARCHAR(64) NOT NULL,
    meal_id VARCHAR(64) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, meal_id),
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_favorites_meal FOREIGN KEY (meal_id)
        REFERENCES meals (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS random_history (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id VARCHAR(64) NOT NULL,
    meal_id VARCHAR(64) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_history_user_created (user_id, created_at),
    CONSTRAINT fk_history_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_history_meal FOREIGN KEY (meal_id)
        REFERENCES meals (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_settings (
    user_id VARCHAR(64) NOT NULL,
    region VARCHAR(32) NOT NULL DEFAULT 'Tất cả',
    vegetarian_only TINYINT(1) NOT NULL DEFAULT 0,
    notifications TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_settings_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS reviews (
    id VARCHAR(64) NOT NULL,
    user_id VARCHAR(64) DEFAULT NULL,
    meal_id VARCHAR(64) NOT NULL,
    reviewer_name VARCHAR(120) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review_text VARCHAR(250) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reviews_meal (meal_id),
    KEY idx_reviews_status (status),
    CONSTRAINT fk_reviews_user FOREIGN KEY (user_id)
        REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_reviews_meal FOREIGN KEY (meal_id)
        REFERENCES meals (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
