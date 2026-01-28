<?php
/**
 * 创建密码重置表
 */

require_once __DIR__ . '/db.php';

// 创建密码重置表
$sql = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expiry DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);";

if (db_exec($sql)) {
    echo "密码重置表创建成功
";
} else {
    echo "密码重置表创建失败
";
}
