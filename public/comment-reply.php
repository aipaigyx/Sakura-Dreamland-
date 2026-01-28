<?php
/**
 * 处理评论回复的PHP文件
 */

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取评论ID和回复内容
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    $reply_content = isset($_POST['content']) ? trim($_POST['content']) : '';
    
    if (empty($comment_id) || empty($reply_content)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '无效的参数']);
        exit;
    }
    
    // 获取被回复评论的信息
    $sql = "SELECT article_id FROM comments WHERE id = ?";
    $parent_comment = db_query_one($sql, [$comment_id]);
    
    if (!$parent_comment) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '被回复的评论不存在']);
        exit;
    }
    
    $article_id = $parent_comment['article_id'];
    
    // 插入回复
        $sql = "INSERT INTO comments (content, user_id, article_id, parent_id, status) VALUES (?, ?, ?, ?, ?)";
        $user_id = 1; // 管理员用户ID
        $status = 'approved'; // 管理员回复自动通过审核
        
        // 获取PDO连接，用于获取最后插入的ID
        $pdo = get_db_connection();
        if (db_exec($sql, [$reply_content, $user_id, $article_id, $comment_id, $status], $pdo)) {
            // 更新文章的评论计数
            $sql = "UPDATE articles SET comment_count = comment_count + 1 WHERE id = ?";
            db_exec($sql, [$article_id], $pdo);
            
            // 获取最后插入的回复ID
            $reply_id = db_last_insert_id($pdo);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => '回复成功',
                'reply' => [
                    'id' => $reply_id,
                    'content' => $reply_content,
                    'user_id' => $user_id,
                    'parent_id' => $comment_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'status' => $status
                ]
            ]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '回复失败，请稍后重试']);
            exit;
        }
} else {
    // 如果不是POST请求，返回错误
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => '无效的请求方法']);
    exit;
}
