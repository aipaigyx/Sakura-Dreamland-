<?php
/**
 * 后台公共头部
 */

// 包含认证中间件
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? '后台管理'; ?> - 樱花梦境</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- 引入统一CSS样式文件 -->
    <link rel="stylesheet" href="/assets/css/unified.css">
</head>
<body class="min-h-screen">
    <div class="flex h-screen overflow-hidden">
        <!-- 侧边栏 -->
        <aside class="sidebar w-64 flex flex-col overflow-y-auto fixed h-full left-0 top-0 z-40">
            <!-- 侧边栏头部 -->
            <div class="sidebar-header">
                <h1 class="sidebar-title">樱花梦境</h1>
                <p class="sidebar-subtitle">后台管理系统</p>
            </div>
            
            <!-- 侧边栏菜单 -->
            <nav class="flex-1 p-4 space-y-1">
                <a href="dashboard.php" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>仪表盘</span>
                </a>
                <a href="articles.php" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'articles.php' ? 'active' : ''; ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>文章管理</span>
                </a>
                <a href="gallery.php" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'gallery.php' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i>
                    <span>图片管理</span>
                </a>
                <a href="comments.php" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'comments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-comments"></i>
                    <span>评论管理</span>
                </a>
                <a href="danmaku.php" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'danmaku.php' ? 'active' : ''; ?>">
                    <i class="fas fa-comment-dots"></i>
                    <span>弹幕管理</span>
                </a>
                <a href="users.php" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' || basename($_SERVER['PHP_SELF']) === 'user-edit.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>用户管理</span>
                </a>
                <a href="settings.php" class="nav-item flex items-center gap-3 px-4 py-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span>系统设置</span>
                </a>
            </nav>
            
            <!-- 侧边栏底部 -->
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php echo isset($_SESSION['admin_username']) ? substr($_SESSION['admin_username'], 0, 1) : 'A'; ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : '管理员'; ?></div>
                        <div class="user-role">管理员</div>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </aside>
        
        <!-- 主内容区 -->
        <main class="flex-1 ml-64 p-6 overflow-y-auto">
            <!-- 顶部导航 -->
            <div class="admin-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title"><?php echo $page_title ?? '后台管理'; ?></h2>
                        <p class="page-description"><?php echo $page_description ?? ''; ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- 搜索框 -->
                        <div class="admin-search">
                            <input type="text" placeholder="搜索...">
                            <i class="fas fa-search"></i>
                        </div>
                        <!-- 通知图标 -->
                        <button class="notification-btn">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- 页面内容 -->
            <div class="space-y-6">