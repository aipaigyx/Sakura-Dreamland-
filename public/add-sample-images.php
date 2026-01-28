<?php
/**
 * 添加测试图片数据
 */

// 加载核心功能文件
require_once __DIR__ . '/functions.php';

// 示例图片数据
$sample_images = [
    [
        'title' => '樱花飘落',
        'file_path' => 'https://picsum.photos/id/1005/800/600',
        'description' => '美丽的樱花飘落场景',
        'user_id' => 1
    ],
    [
        'title' => '梦幻星空',
        'file_path' => 'https://picsum.photos/id/1015/800/600',
        'description' => '璀璨的星空夜景',
        'user_id' => 1
    ],
    [
        'title' => '森林精灵',
        'file_path' => 'https://picsum.photos/id/1025/800/600',
        'description' => '森林中的精灵角色',
        'user_id' => 1
    ],
    [
        'title' => '秋日私语',
        'file_path' => 'https://picsum.photos/id/1035/800/600',
        'description' => '秋天的森林景色',
        'user_id' => 1
    ],
    [
        'title' => '海边日落',
        'file_path' => 'https://picsum.photos/id/1045/800/600',
        'description' => '海边的日落美景',
        'user_id' => 1
    ],
    [
        'title' => '动漫角色',
        'file_path' => 'https://picsum.photos/id/1055/800/600',
        'description' => '可爱的动漫角色',
        'user_id' => 1
    ],
    [
        'title' => '城市夜景',
        'file_path' => 'https://picsum.photos/id/1065/800/600',
        'description' => '繁华的城市夜景',
        'user_id' => 1
    ],
    [
        'title' => '山水风景',
        'file_path' => 'https://picsum.photos/id/1075/800/600',
        'description' => '壮丽的山水风景',
        'user_id' => 1
    ]
];

// 插入图片数据
$success_count = 0;
$error_count = 0;

foreach ($sample_images as $image) {
    $sql = "INSERT INTO images (title, file_path, description, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    $result = db_exec($sql, [
        $image['title'],
        $image['file_path'],
        $image['description'],
        $image['user_id']
    ]);
    
    if ($result) {
        $success_count++;
    } else {
        $error_count++;
    }
}

// 输出结果
echo "<h2>图片数据添加结果</h2>";
echo "<p style='color: green;'>成功添加：{$success_count} 张图片</p>";
if ($error_count > 0) {
    echo "<p style='color: red;'>添加失败：{$error_count} 张图片</p>";
}

echo "<h3>添加的图片列表：</h3>";
echo "<ul>";
foreach ($sample_images as $image) {
    echo "<li>";
    echo "<strong>{$image['title']}</strong><br>";
    echo "<img src='{$image['file_path']}' width='200' height='150' style='margin: 10px; border-radius: 8px;'><br>";
    echo "描述：{$image['description']}";
    echo "</li>";
}
echo "</ul>";

echo "<a href='/index.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #ec4899; color: white; text-decoration: none; border-radius: 5px;'>返回首页</a>";
?>