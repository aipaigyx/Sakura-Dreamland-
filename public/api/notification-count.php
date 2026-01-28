<?php
/**
 * 获取未读通知数量API
 */

// 包含函数文件
require_once __DIR__ . '/../functions.php';

// 检查是否已登录
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['count' => 0]);
    exit;
}

// 获取用户ID
$user_id = $_SESSION['user_id'];

// 获取未读通知数量
$count = get_unread_notification_count($user_id);

// 返回JSON响应
echo json_encode(['count' => $count]);
