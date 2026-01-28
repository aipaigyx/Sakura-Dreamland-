<?php
/**
 * API接口文件
 * 用于处理前后台AJAX请求
 */

// 设置响应头
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 处理OPTIONS请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 包含功能函数
require_once 'functions.php';

// 获取请求类型
$action = $_GET['action'] ?? '';

// 处理不同的API请求
switch ($action) {
    // 获取最新文章
    case 'get_latest_articles':
        $limit = $_GET['limit'] ?? 5;
        $articles = get_latest_articles($limit);
        echo json_encode(['success' => true, 'data' => $articles]);
        break;
    
    // 获取最新评论
    case 'get_latest_comments':
        $limit = $_GET['limit'] ?? 5;
        $comments = get_latest_comments($limit);
        echo json_encode(['success' => true, 'data' => $comments]);
        break;
    
    // 获取统计数据
    case 'get_statistics':
        $stats = get_statistics();
        echo json_encode(['success' => true, 'data' => $stats]);
        break;
    
    // 获取所有文章
    case 'get_all_articles':
        $articles = get_all_articles();
        echo json_encode(['success' => true, 'data' => $articles]);
        break;
    
    // 获取所有图片
    case 'get_all_images':
        $images = get_all_images();
        echo json_encode(['success' => true, 'data' => $images]);
        break;
    
    // 获取所有评论
    case 'get_all_comments':
        $comments = get_all_comments();
        echo json_encode(['success' => true, 'data' => $comments]);
        break;
    
    // 获取访问统计数据
    case 'get_visit_statistics':
        $period = $_GET['period'] ?? 'all';
        $stats = get_visit_statistics($period);
        echo json_encode(['success' => true, 'data' => $stats]);
        break;
    
    // 获取流量来源数据
    case 'get_traffic_sources':
        $sources = get_traffic_sources();
        echo json_encode(['success' => true, 'data' => $sources]);
        break;
    
    // 获取热门内容数据
    case 'get_popular_content':
        $type = $_GET['type'] ?? 'articles';
        $limit = $_GET['limit'] ?? 5;
        $popular = get_popular_content($type, $limit);
        echo json_encode(['success' => true, 'data' => $popular]);
        break;
    
    // 获取用户行为数据
    case 'get_user_behavior':
        $behavior = get_user_behavior();
        echo json_encode(['success' => true, 'data' => $behavior]);
        break;
    
    // 获取所有用户
    case 'get_all_users':
        $users = get_all_users();
        echo json_encode(['success' => true, 'data' => $users]);
        break;
    
    // 获取筛选后的图片
    case 'get_filtered_images':
        $category = $_GET['category'] ?? 'all';
        
        if ($category === 'all') {
            $sql = "SELECT i.*, c.name as category_name FROM images i LEFT JOIN categories c ON i.category_id = c.id ORDER BY i.created_at DESC";
            $images = db_query($sql);
        } else {
            $sql = "SELECT i.*, c.name as category_name FROM images i LEFT JOIN categories c ON i.category_id = c.id WHERE i.category_id = ? ORDER BY i.created_at DESC";
            $images = db_query($sql, [$category]);
        }
        
        echo json_encode(['success' => true, 'data' => $images]);
        break;
    
    // 更新用户角色
    case 'update_user_role':
        $user_id = $_POST['user_id'] ?? 0;
        $role = $_POST['role'] ?? '';
        if ($user_id && $role) {
            $result = update_user_role($user_id, $role);
            echo json_encode(['success' => $result, 'message' => $result ? '角色更新成功' : '角色更新失败']);
        } else {
            echo json_encode(['success' => false, 'message' => '参数错误']);
        }
        break;
    
    // 默认响应
    default:
        echo json_encode(['success' => false, 'message' => '无效的API请求']);
        break;
}