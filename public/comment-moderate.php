<?php
/**
 * 处理评论审核的PHP文件
 */

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// 检查请求方法
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取评论ID和审核状态
    $comment_id = isset($_POST['comment_id']) ? (int)$_POST['comment_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    if (empty($comment_id) || !in_array($status, ['pending', 'approved', 'rejected'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '无效的参数']);
        exit;
    }
    
    // 更新评论状态
    $sql = "UPDATE comments SET status = ? WHERE id = ?";
    $result = db_exec($sql, [$status, $comment_id]);
    
    if ($result !== false) {
        // 即使影响行数为0，只要SQL执行成功，就返回成功
        // 影响行数为0的情况：1) 评论已处于目标状态 2) 评论ID不存在
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => '评论状态已更新', 'status' => $status]);
        exit;
    } else {
        // 只有当SQL执行失败时，才返回失败
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => '更新评论状态失败']);
        exit;
    }
} else {
    // 如果不是POST请求，返回错误
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => '无效的请求方法']);
    exit;
}
?>