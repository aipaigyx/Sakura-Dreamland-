<?php
/**
 * 处理文章收藏的PHP文件
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
    
    // 获取文章ID
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : 'toggle';
    
    if (empty($article_id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '无效的文章ID']);
        exit;
    }
    
    // 获取当前登录用户ID
    $user_id = $_SESSION['user_id'];
    
    // 获取收藏数
    $count_sql = "SELECT COUNT(*) as count FROM favorites WHERE article_id = ?";
    $count_result = db_query_one($count_sql, [$article_id]);
    $favorite_count = $count_result['count'];
    
    // 检查是否已经收藏
    $check_sql = "SELECT * FROM favorites WHERE user_id = ? AND article_id = ?";
    $check_result = db_query($check_sql, [$user_id, $article_id]);
    $is_favorited = !empty($check_result);
    
    if ($action === 'check') {
        // 仅检查收藏状态
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'is_favorited' => $is_favorited, 'favorite_count' => $favorite_count]);
        exit;
    }
    
    // 切换收藏状态
    if ($is_favorited) {
        // 已收藏，取消收藏
        $sql = "DELETE FROM favorites WHERE user_id = ? AND article_id = ?";
        if (db_exec($sql, [$user_id, $article_id])) {
            // 获取更新后的收藏数
            $count_sql = "SELECT COUNT(*) as count FROM favorites WHERE article_id = ?";
            $count_result = db_query_one($count_sql, [$article_id]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'is_favorited' => false, 'favorite_count' => $count_result['count']]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '取消收藏失败']);
            exit;
        }
    } else {
        // 未收藏，添加收藏
        $sql = "INSERT INTO favorites (user_id, article_id) VALUES (?, ?)";
        if (db_exec($sql, [$user_id, $article_id])) {
            // 获取更新后的收藏数
            $count_sql = "SELECT COUNT(*) as count FROM favorites WHERE article_id = ?";
            $count_result = db_query_one($count_sql, [$article_id]);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'is_favorited' => true, 'favorite_count' => $count_result['count']]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '收藏失败']);
            exit;
        }
    }
} else {
    // 如果不是POST请求，返回错误
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => '无效的请求方法']);
    exit;
}