<?php
/**
 * 处理评论删除的PHP文件
 */

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取评论ID
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    
    if (empty($comment_id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '无效的评论ID']);
        exit;
    }
    
    // 获取评论关联的文章ID（用于更新评论计数）
    $sql = "SELECT article_id FROM comments WHERE id = ?";
    $comment = db_query_one($sql, [$comment_id]);
    
    if (!$comment) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '评论不存在']);
        exit;
    }
    
    $article_id = $comment['article_id'];
    
    // 删除评论
    $sql = "DELETE FROM comments WHERE id = ? OR parent_id = ?";
    $result = db_exec($sql, [$comment_id, $comment_id]);
    
    if ($result !== false) {
        // 更新文章的评论计数
        $sql = "UPDATE articles SET comment_count = comment_count - 1 WHERE id = ?";
        db_exec($sql, [$article_id]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => '评论已删除']);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '删除评论失败']);
        exit;
    }
} else {
    // 如果不是POST请求，返回错误
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => '无效的请求方法']);
    exit;
}
