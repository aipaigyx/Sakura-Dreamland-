<?php
// 包含功能文件
include 'public/functions.php';

// 测试创建通知
echo 'Testing notification functions...\n';

// 假设用户ID为1（根据实际情况调整）
$user_id = 1;
$title = '测试通知';
$content = '这是一条测试通知内容';
$type = 'test';

// 创建通知
$result = create_notification($user_id, $type, $title, $content);

if ($result) {
    echo '✓ Notification created successfully!\n';
    
    // 测试获取通知
    $notifications = get_user_notifications($user_id);
    echo '✓ Retrieved ' . count($notifications) . ' notifications!\n';
    
    // 测试获取未读通知数量
    $unread_count = get_unread_notification_count($user_id);
    echo '✓ Unread notification count: ' . $unread_count . '\n';
    
    echo '\nAll notification functions tested successfully!\n';
} else {
    echo '✗ Failed to create notification!\n';
}