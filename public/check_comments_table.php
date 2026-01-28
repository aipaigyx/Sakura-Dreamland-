<?php
/**
 * 检查评论表结构的临时脚本
 */

require_once __DIR__ . '/db.php';

// 检查comments表的结构
echo "正在检查comments表结构...\n";

// 获取表结构
$check_sql = "SHOW COLUMNS FROM comments";
$columns = db_query($check_sql);

if (!empty($columns)) {
    echo "comments表的字段列表：\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ") - 默认值: " . $column['Default'] . " - " . ($column['Null'] === 'YES' ? '允许NULL' : '不允许NULL') . "\n";
    }
} else {
    echo "无法获取comments表结构！\n";
}

// 检查评论数据
echo "\n正在检查评论数据...\n";
$comment_sql = "SELECT * FROM comments LIMIT 5";
$comments = db_query($comment_sql);

if (!empty($comments)) {
    echo "最新5条评论：\n";
    foreach ($comments as $comment) {
        echo "ID: " . $comment['id'] . " - 内容: " . substr($comment['content'], 0, 50) . "... - 用户ID: " . $comment['user_id'] . "\n";
        echo "状态字段: " . (isset($comment['status']) ? $comment['status'] : "不存在") . "\n";
    }
} else {
    echo "暂无评论数据！\n";
}
