<?php
/**
 * 测试密码重置表
 */

require_once __DIR__ . '/db.php';

// 测试数据库连接
$pdo = get_db_connection();
if ($pdo) {
    echo "数据库连接成功
";
    
    // 检查表是否已存在
    $sql = "SHOW TABLES LIKE 'password_resets'";
    $stmt = $pdo->query($sql);
    if ($stmt->rowCount() > 0) {
        echo "密码重置表已存在
";
    } else {
        // 尝试创建表
        $create_sql = "CREATE TABLE password_resets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            token VARCHAR(255) NOT NULL,
            expiry DATETIME NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        try {
            $pdo->exec($create_sql);
            echo "密码重置表创建成功
";
        } catch (PDOException $e) {
            echo "密码重置表创建失败: " . $e->getMessage() . "
";
        }
    }
} else {
    echo "数据库连接失败
";
}
