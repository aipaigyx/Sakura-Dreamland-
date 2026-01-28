<?php
/**
 * 更新评论表结构和创建评论点赞表
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 更新评论表，添加点赞数和状态字段
try {
    $sql = "ALTER TABLE comments 
            ADD COLUMN likes INT DEFAULT 0,
            ADD COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved'";
    db_exec($sql);
    echo "评论表已添加点赞数和状态字段\n";
} catch (Exception $e) {
    echo "更新评论表失败: " . $e->getMessage() . "\n";
}

// 创建评论点赞表
try {
    $sql = "CREATE TABLE IF NOT EXISTS comment_likes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT 1,
            comment_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE
          )";
    db_exec($sql);
    echo "评论点赞表已创建\n";
} catch (Exception $e) {
    echo "创建评论点赞表失败: " . $e->getMessage() . "\n";
}

echo "操作完成！";
?>