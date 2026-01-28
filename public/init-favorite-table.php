<?php
/**
 * 初始化收藏表
 */

// 加载数据库连接
require_once 'db.php';

// 关闭默认的错误处理，以便捕获PDOException
$pdo = null;
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // 创建收藏表
    $sql = "CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        article_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_favorite (user_id, article_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "收藏表创建成功！\n";
    
} catch (PDOException $e) {
    echo "收藏表创建失败！错误信息：" . $e->getMessage() . "\n";
    echo "SQL语句：" . $sql . "\n";
    exit;
} finally {
    $pdo = null;
}
