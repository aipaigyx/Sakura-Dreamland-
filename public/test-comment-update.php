<?php
/**
 * 测试评论更新功能的脚本
 */

// 加载数据库连接和函数文件
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

echo "正在测试评论更新功能...\n\n";

// 测试1: 检查数据库连接
$pdo = get_db_connection();
if ($pdo) {
    echo "✅ 数据库连接成功\n";
} else {
    echo "❌ 数据库连接失败\n";
    exit;
}

// 测试2: 检查comments表结构
echo "\n检查comments表结构...\n";
$check_sql = "SHOW COLUMNS FROM comments";
$columns = db_query($check_sql);
if (!empty($columns)) {
    echo "comments表字段列表：\n";
    foreach ($columns as $column) {
        if ($column['Field'] === 'status') {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ") - 默认值: " . $column['Default'] . "\n";
        }
    }
} else {
    echo "❌ 无法获取comments表结构\n";
    exit;
}

// 测试3: 检查是否有评论可以测试
echo "\n检查评论数据...\n";
$comment_sql = "SELECT * FROM comments LIMIT 1";
$comments = db_query($comment_sql);

if (empty($comments)) {
    echo "❌ 没有评论数据可以测试\n";
    exit;
}

$test_comment = $comments[0];
$test_comment_id = $test_comment['id'];
$current_status = $test_comment['status'];

echo "测试评论ID: $test_comment_id\n";
echo "当前状态: $current_status\n";

// 测试4: 执行更新操作
echo "\n测试更新评论状态...\n";
$new_status = $current_status === 'approved' ? 'pending' : 'approved';
$update_sql = "UPDATE comments SET status = ? WHERE id = ?";
$result = db_exec($update_sql, [$new_status, $test_comment_id]);

if ($result !== false && $result > 0) {
    echo "✅ 评论状态更新成功\n";
    echo "从 $current_status 更新为 $new_status\n";
    
    // 测试5: 验证更新结果
    $verify_sql = "SELECT status FROM comments WHERE id = ?";
    $updated_comment = db_query_one($verify_sql, [$test_comment_id]);
    if ($updated_comment && $updated_comment['status'] === $new_status) {
        echo "✅ 验证成功，状态已更新为 $new_status\n";
        
        // 恢复原状态
        db_exec($update_sql, [$current_status, $test_comment_id]);
        echo "✅ 已恢复原状态 $current_status\n";
    } else {
        echo "❌ 验证失败，状态未更新\n";
    }
} else {
    echo "❌ 评论状态更新失败\n";
    echo "错误信息: " . (isset($pdo->errorInfo()[2]) ? $pdo->errorInfo()[2] : '未知错误') . "\n";
}

// 测试6: 测试comment-moderate.php文件
echo "\n测试comment-moderate.php文件...\n";

// 模拟POST请求
$test_data = [
    'comment_id' => $test_comment_id,
    'status' => $new_status
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($test_data)
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents('http://localhost:8860/comment-moderate.php', false, $context);

if ($result) {
    $data = json_decode($result, true);
    if ($data['success']) {
        echo "✅ comment-moderate.php 调用成功\n";
        echo "返回结果: " . json_encode($data) . "\n";
        
        // 恢复原状态
        $test_data['status'] = $current_status;
        $options['http']['content'] = http_build_query($test_data);
        $context = stream_context_create($options);
        file_get_contents('http://localhost:8860/comment-moderate.php', false, $context);
    } else {
        echo "❌ comment-moderate.php 调用失败\n";
        echo "返回结果: " . json_encode($data) . "\n";
    }
} else {
    echo "❌ 无法连接到 comment-moderate.php\n";
}

echo "\n测试完成！\n";
