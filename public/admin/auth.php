<?php
/**
 * 后台认证中间件
 */

// 加载函数文件
require_once __DIR__ . '/../functions.php';

// 检查是否已登录
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 获取当前用户角色
$current_role = $_SESSION['admin_role'] ?? 'user';

/**
 * 检查当前用户是否有指定权限
 * @param string $permission 需要检查的权限
 * @return bool 是否有权限
 */
function can($permission) {
    global $current_role;
    return check_permission($current_role, $permission);
}

/**
 * 如果没有权限则重定向或显示错误
 * @param string $permission 需要检查的权限
 */
function require_permission($permission) {
    if (!can($permission)) {
        header('Location: dashboard.php');
        exit;
    }
}
