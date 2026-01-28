<?php
require_once __DIR__ . '/functions.php';

// 添加测试文章
$sql = "INSERT INTO articles (title, content, summary, cover_image, author_id, category_id, view_count, comment_count, created_at, updated_at) VALUES ('测试文章', '这是一篇测试文章的内容', '这是文章摘要', 'https://via.placeholder.com/800x400', 1, 1, 0, 0, NOW(), NOW())";
$result = db_exec($sql);

if ($result) {
    echo "文章添加成功！<br>";
    echo "文章ID：" . get_last_insert_id();
} else {
    echo "文章添加失败！";
}
?>