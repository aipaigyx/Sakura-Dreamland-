<?php
/**
 * 处理评论点赞的PHP文件
 */



// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查用户是否登录
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '请先登录']);
        exit;
    }
    
    // 获取评论ID
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    
    if (empty($comment_id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '无效的评论ID']);
        exit;
    }
    
    // 获取当前登录用户ID
    $user_id = $_SESSION['user_id'];
    
    // 检查是否已经点赞
    $check_sql = "SELECT * FROM comment_likes WHERE user_id = ? AND comment_id = ?";
    $check_result = db_query($check_sql, [$user_id, $comment_id]);
    
    if (!empty($check_result)) {
        // 已点赞，取消点赞
        $sql = "DELETE FROM comment_likes WHERE user_id = ? AND comment_id = ?";
        if (db_exec($sql, [$user_id, $comment_id])) {
            // 更新评论点赞数
            $update_sql = "UPDATE comments SET likes = (SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?) WHERE id = ?";
            db_exec($update_sql, [$comment_id, $comment_id]);
            
            // 获取更新后的点赞数
            $count_sql = "SELECT COUNT(*) as count FROM comment_likes WHERE comment_id = ?";
            $count_result = db_query_one($count_sql, [$comment_id]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'is_liked' => false, 'like_count' => $count_result['count']]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '取消点赞失败']);
            exit;
        }
    } else {
        // 未点赞，添加点赞
        $sql = "INSERT INTO comment_likes (user_id, comment_id) VALUES (?, ?)";
        if (db_exec($sql, [$user_id, $comment_id])) {
            // 更新评论点赞数
            $update_sql = "UPDATE comments SET likes = (SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?) WHERE id = ?";
            db_exec($update_sql, [$comment_id, $comment_id]);
            
            // 获取更新后的点赞数
            $count_sql = "SELECT COUNT(*) as count FROM comment_likes WHERE comment_id = ?";
            $count_result = db_query_one($count_sql, [$comment_id]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'is_liked' => true, 'like_count' => $count_result['count']]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '点赞失败']);
            exit;
        }
    }
} else {
    // 如果不是POST请求，返回错误
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => '无效的请求方法']);
    exit;
}
?>