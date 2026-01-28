<?php
/**
 * 创建管理员用户的脚本
 */

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 创建管理员用户
$username = 'admin';
$email = 'admin@example.com';
$password = 'admin123';
$role = 'admin';

// 生成密码哈希
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// 检查用户是否已存在
$check_sql = "SELECT * FROM users WHERE username = ? OR email = ?";
$check_result = db_query($check_sql, [$username, $email]);

if (empty($check_result)) {
    // 创建用户
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $result = db_exec($sql, [$username, $email, $hashed_password, $role]);
    
    if ($result) {
        echo "管理员用户创建成功！\n";
        echo "账号：$username\n";
        echo "密码：$password\n";
        echo "邮箱：$email\n";
    } else {
        echo "创建失败，请检查数据库配置\n";
    }
} else {
    echo "用户已存在！\n";
    echo "请使用其他用户名或邮箱\n";
}
?>