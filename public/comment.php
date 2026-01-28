<?php
/**
 * 处理评论提交的PHP文件
 */

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取评论数据
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $parent_id = isset($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 1; // 使用从表单传递的用户ID，否则默认为游客用户
    
    // 验证数据
    if (empty($article_id) || empty($content)) {
        // 数据验证失败，重定向回文章页面
        header('Location: /article.php?id=' . $article_id . '&error=1');
        exit;
    }
    
    // 只有游客评论才需要验证name和email
    if ($user_id == 1) {
        if (empty($name) || empty($email)) {
            header('Location: /article.php?id=' . $article_id . '&error=1');
            exit;
        }
        
        // 简单的邮箱验证
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: /article.php?id=' . $article_id . '&error=2');
            exit;
        }
    }
    
    // 插入评论到数据库
    $sql = "INSERT INTO comments (content, user_id, name, email, article_id, parent_id) VALUES (?, ?, ?, ?, ?, ?)";
    
    // 执行插入操作
    $result = db_exec($sql, [$content, $user_id, $name, $email, $article_id, $parent_id]);
    
    if ($result !== false && $result > 0) {
        // 更新文章的评论计数
        $sql = "UPDATE articles SET comment_count = comment_count + 1 WHERE id = ?";
        db_exec($sql, [$article_id]);
        
        // 评论提交成功，重定向回文章页面
        header('Location: /article.php?id=' . $article_id . '&success=1');
        exit;
    } else {
        // 评论提交失败
        header('Location: /article.php?id=' . $article_id . '&error=3');
        exit;
    }
} else {
    // 如果不是POST请求，重定向到首页
    header('Location: /');
    exit;
}
