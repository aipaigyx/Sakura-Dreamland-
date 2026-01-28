<?php
/**
 * 编辑图片信息
 */

// 包含认证中间件
require_once 'auth.php';

// 检查权限
require_permission('manage_images');

// 包含功能函数
require_once '../functions.php';

// 只处理POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => '只允许POST请求']);
    exit;
}

// 获取POST数据
$image_id = $_POST['image_id'] ?? 0;
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$category_id = $_POST['category_id'] ?? null;

// 验证数据
if (empty($image_id) || empty($title)) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 更新图片信息
try {
    $sql = "UPDATE images SET title = ?, description = ?, category_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $result = db_exec($sql, [$title, $description, $category_id, $image_id]);
    
    if ($result > 0) {
        echo json_encode(['success' => true, 'message' => '图片信息已成功更新']);
    } else {
        echo json_encode(['success' => false, 'message' => '图片信息更新失败，可能是因为没有任何更改']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '更新过程中发生错误：' . $e->getMessage()]);
    exit;
}
