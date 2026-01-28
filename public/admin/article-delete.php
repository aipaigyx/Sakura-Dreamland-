<?php
/**
 * 删除文章
 */

// 包含认证中间件
require_once 'auth.php';

// 检查权限
require_permission('manage_articles');

// 包含功能函数
require_once '../functions.php';

// 只处理POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => '只允许POST请求']);
    exit;
}

// 获取POST数据
$article_id = $_POST['article_id'] ?? 0;

// 验证数据
if (empty($article_id)) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 删除文章
try {
    // 先删除文章的相关记录（评论、点赞、收藏等）
    db_exec("DELETE FROM comments WHERE article_id = ?", [$article_id]);
    db_exec("DELETE FROM article_likes WHERE article_id = ?", [$article_id]);
    db_exec("DELETE FROM favorites WHERE article_id = ?", [$article_id]);
    db_exec("DELETE FROM danmaku WHERE article_id = ?", [$article_id]);
    db_exec("DELETE FROM article_tags WHERE article_id = ?", [$article_id]);
    
    // 然后删除文章本身
    $sql = "DELETE FROM articles WHERE id = ?";
    $result = db_exec($sql, [$article_id]);
    
    if ($result > 0) {
        echo json_encode(['success' => true, 'message' => '文章已成功删除']);
    } else {
        echo json_encode(['success' => false, 'message' => '文章删除失败']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '删除过程中发生错误：' . $e->getMessage()]);
    exit;
}
