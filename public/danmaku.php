<?php
/**
 * 处理弹幕提交的PHP文件
 */



// 加载函数文件
require_once __DIR__ . '/functions.php';

// 加载数据库连接文件
require_once __DIR__ . '/db.php';

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取弹幕数据
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    $color = isset($_POST['color']) ? trim($_POST['color']) : '#ffffff';
    $size = isset($_POST['size']) ? (int)$_POST['size'] : 24;
    $mode = isset($_POST['mode']) ? trim($_POST['mode']) : 'scroll';
    
    // 获取用户ID（默认为1，未登录用户）
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
    
    // 验证数据
    if (empty($content) || $article_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '无效的弹幕数据']);
        exit;
    }
    
    // 插入弹幕到数据库
    $sql = "INSERT INTO danmaku (content, color, size, mode, user_id, article_id) VALUES (?, ?, ?, ?, ?, ?)";
    $result = db_exec($sql, [$content, $color, $size, $mode, $user_id, $article_id]);
    
    if ($result) {
        // 返回成功响应
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => '弹幕发送成功']);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '弹幕发送失败']);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 获取文章的弹幕列表
    $article_id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
    
    if ($article_id > 0) {
        $sql = "SELECT * FROM danmaku WHERE article_id = ? AND status = 'approved' ORDER BY created_at ASC";
        $danmakus = db_query($sql, [$article_id]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $danmakus]);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '无效的文章ID']);
        exit;
    }
}

// 默认响应
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => '无效的请求方法']);
exit;
