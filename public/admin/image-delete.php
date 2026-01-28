<?php
/**
 * 删除图片
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

// 验证数据
if (empty($image_id)) {
    echo json_encode(['success' => false, 'message' => '缺少必要参数']);
    exit;
}

// 删除图片
try {
    // 先获取图片信息，用于删除实际文件
    $sql = "SELECT file_path FROM images WHERE id = ?";
    $image = db_query_one($sql, [$image_id]);
    
    if (!$image) {
        echo json_encode(['success' => false, 'message' => '图片不存在']);
        exit;
    }
    
    // 删除数据库中的记录
    $sql = "DELETE FROM images WHERE id = ?";
    $result = db_exec($sql, [$image_id]);
    
    if ($result > 0) {
        // 尝试删除实际的图片文件
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $image['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        echo json_encode(['success' => true, 'message' => '图片已成功删除']);
    } else {
        echo json_encode(['success' => false, 'message' => '图片删除失败']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '删除过程中发生错误：' . $e->getMessage()]);
    exit;
}
