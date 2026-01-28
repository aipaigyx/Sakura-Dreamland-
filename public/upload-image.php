<?php
/**
 * 处理富文本编辑器的图片上传
 */

// 只允许POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => '只允许POST请求']);
    exit;
}

// 检查是否有文件上传
if (empty($_FILES['file'])) {
    echo json_encode(['error' => '没有文件上传']);
    exit;
}

$file = $_FILES['file'];

// 验证文件类型
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['error' => '只允许上传JPG、PNG、GIF格式的图片']);
    exit;
}

// 验证文件大小（最大2MB）
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['error' => '图片大小不能超过2MB']);
    exit;
}

// 创建上传目录
$upload_dir = __DIR__ . '/uploads/images/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// 生成唯一文件名
$file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$file_name = 'img_' . uniqid() . '.' . $file_ext;
$file_path = $upload_dir . $file_name;

// 保存文件
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // 返回图片URL
    $image_url = '/uploads/images/' . $file_name;
    echo json_encode([
        'location' => $image_url
    ]);
} else {
    echo json_encode(['error' => '文件上传失败']);
}
