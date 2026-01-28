<?php
/**
 * 标记通知为已读API
 */

// 包含函数文件
require_once __DIR__ . '/../functions.php';

// 检查是否已登录
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => '未登录']);
    exit;
}

// 获取用户ID
$user_id = $_SESSION['user_id'];

// 处理请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取通知ID
    $notification_id = isset($_POST['notification_id']) ? intval($_POST['notification_id']) : 0;
    
    if ($notification_id > 0) {
        // 标记单个通知为已读
        $success = mark_notification_as_read($notification_id, $user_id);
        echo json_encode(['success' => $success]);
    } else {
        // 标记所有通知为已读
        $success = mark_all_notifications_as_read($user_id);
        echo json_encode(['success' => $success]);
    }
} else {
    echo json_encode(['success' => false, 'error' => '方法不允许']);
}
