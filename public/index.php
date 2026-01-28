<?php
/**
 * AniBlog - 二次元风格博客网站
 * 类似WordPress主题的入口文件
 */

// 加载核心功能文件
require_once __DIR__ . '/functions.php';

// 获取当前页面
$page = aniblog_get_current_page();

// 加载页面模板
include __DIR__ . '/header.php';

switch ($page) {
    case 'home':
        include __DIR__ . '/home.php';
        break;
    case 'article':
        include __DIR__ . '/article.php';
        break;
    case 'articles':
        include __DIR__ . '/articles.php';
        break;
    case 'gallery':
        include __DIR__ . '/gallery.php';
        break;
    case 'character':
        include __DIR__ . '/character.php';
        break;
    case 'submit':
        include __DIR__ . '/submit.php';
        break;
    case 'login':
        include __DIR__ . '/login.php';
        break;
    case 'register':
        include __DIR__ . '/register.php';
        break;
    default:
        // 直接根据文件名加载对应页面
        $uri = $_SERVER['REQUEST_URI'];
        $uri = trim($uri, '/');
        
        // 处理URL参数，只获取文件名
        $file_parts = explode('?', $uri);
        $file_name = $file_parts[0];
        
        // 检查文件是否存在
        $file_path = __DIR__ . '/' . $file_name;
        if (file_exists($file_path) && is_file($file_path)) {
            include $file_path;
        } elseif (strpos($uri, 'submit') !== false) {
            include __DIR__ . '/submit.php';
        } else {
            include __DIR__ . '/home.php';
        }
        break;
}

// 加载页脚
include __DIR__ . '/footer.php';
