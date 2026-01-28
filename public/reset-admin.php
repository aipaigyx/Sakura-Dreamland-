<?php
/**
 * 重置管理员密码或创建新管理员账号的脚本
 */

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 重置现有管理员密码
$username = 'admin';
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// 检查用户是否已存在
$check_sql = "SELECT * FROM users WHERE username = ?";
$check_result = db_query($check_sql, [$username]);

if (!empty($check_result)) {
    // 重置密码
    $update_sql = "UPDATE users SET password = ?, role = ? WHERE username = ?";
    $result = db_exec($update_sql, [$hashed_password, 'admin', $username]);
    
    if ($result) {
        echo "管理员密码重置成功！\n";
        echo "账号：$username\n";
        echo "新密码：$new_password\n";
        echo "角色：admin\n";
    } else {
        echo "密码重置失败，请检查数据库配置\n";
    }
} else {
    // 创建新用户
    $email = 'admin@example.com';
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $result = db_exec($sql, [$username, $email, $hashed_password, 'admin']);
    
    if ($result) {
        echo "管理员用户创建成功！\n";
        echo "账号：$username\n";
        echo "密码：$new_password\n";
        echo "邮箱：$email\n";
    } else {
        echo "创建失败，请检查数据库配置\n";
    }
}
?>