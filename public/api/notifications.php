<?php
/**
 * 获取通知列表API
 */

// 包含函数文件
require_once __DIR__ . '/../functions.php';

// 检查是否已登录
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['notifications' => [], 'error' => '未登录']);
    exit;
}

// 获取用户ID
$user_id = $_SESSION['user_id'];

// 获取请求参数
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$only_unread = isset($_GET['only_unread']) && $_GET['only_unread'] === 'true';

// 获取通知列表
$notifications = get_user_notifications($user_id, $limit, $offset, $only_unread);

// 返回JSON响应
echo json_encode(['notifications' => $notifications]);
