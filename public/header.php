<?php
// 启动会话
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 引入函数文件
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo aniblog_get_page_title(); ?></title>
    <!-- 网站图标优化 -->
    <link rel="shortcut icon" href="<?php echo ANIBLOG_ASSETS_URL; ?>/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo ANIBLOG_ASSETS_URL; ?>/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo ANIBLOG_ASSETS_URL; ?>/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo ANIBLOG_ASSETS_URL; ?>/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo ANIBLOG_ASSETS_URL; ?>/images/favicon-16x16.png">
    <link rel="manifest" href="<?php echo ANIBLOG_ASSETS_URL; ?>/images/site.webmanifest">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- GSAP 动画库 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- 自定义样式 -->
    <?php aniblog_enqueue_styles(); ?>
    <!-- 预加载字体 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- 引入统一CSS样式文件 -->
    <link rel="stylesheet" href="/assets/css/unified.css">
    <style>
        /* 加载背景设置 */
        <?php 
            $settings = get_settings();
            $custom_bg = $settings['custom_bg_image'] ?? '';
            $bg_blend = $settings['bg_blend_mode'] ?? 'normal';
            $bg_opacity = $settings['bg_opacity'] ?? 80;
        ?>
        
        /* 动态背景样式 */
        body {
            <?php if (!empty($custom_bg)): ?>
            background-image: url('<?php echo htmlspecialchars($custom_bg); ?>');
            background-size: cover;
            background-position: center;
            background-blend-mode: <?php echo htmlspecialchars($bg_blend); ?>;
            background-color: rgba(255, 255, 255, <?php echo $bg_opacity / 100; ?>);
            <?php endif; ?>
        }
        
        /* 动态背景装饰的透明度调整 */
        .bg-decoration::before {
            <?php if (!empty($custom_bg)): ?>
            opacity: 0.5;
            <?php endif; ?>
        }
        
        /* 自定义CSS */
        <?php 
            $settings = get_settings();
            if (!empty($settings['custom_css'])) {
                echo $settings['custom_css'];
            }
        ?>
    </style>
</head>
<body>
    <!-- 背景装饰 -->
    <div class="bg-decoration"></div>
    
    <!-- 阅读进度条 -->
    <div id="reading-progress" class="fixed top-0 left-0 right-0 h-1 bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 z-50 transform origin-left scale-x-0 transition-transform duration-300 ease-out"></div>
    
    <!-- 导航栏 -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass-morphism">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-600">
                        樱花梦境
                    </a>
                </div>
                
                <!-- 搜索框 -->
                <div class="hidden md:flex items-center w-1/3 max-w-md">
                    <form method="GET" action="/articles.php" class="w-full">
                        <div class="relative">
                            <input type="text" name="search" placeholder="搜索文章、图片或角色..." class="form-control">
                            <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-pink-500 hover:text-pink-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- 导航链接 -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="/index.php" class="nav-link active">首页</a>
                    <a href="/articles.php" class="nav-link">文章</a>
                    <a href="/gallery.php" class="nav-link">画廊</a>
                    <a href="/character.php" class="nav-link">角色生成器</a>
                    <a href="/submit.php" class="nav-link">投稿</a>
                </div>
                
                <!-- 用户菜单 -->
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                        <!-- 已登录状态 -->
                        <!-- 简化的通知图标 - 仅保留点击跳转功能 -->
                        <a href="/admin/notifications.php" class="relative p-2 text-gray-700 hover:text-pink-500 transition-colors duration-200">
                            <i class="fas fa-bell text-lg"></i>
                            <?php $unread_count = (isset($_SESSION['user_logged_in']) && isset($_SESSION['user_id'])) ? get_unread_notification_count($_SESSION['user_id']) : 0; ?>
                            <?php if ($unread_count > 0): ?>
                                <span class="notification-badge"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <!-- 用户头像 -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 p-2 rounded-full hover:bg-white/20 transition-colors duration-200">
                                <?php 
                                    // 获取用户头像
                                    $avatar = '';
                                    
                                    // 从会话中获取头像
                                    if (isset($_SESSION['user_avatar']) && !empty($_SESSION['user_avatar'])) {
                                        $avatar = $_SESSION['user_avatar'];
                                    } elseif (isset($_SESSION['admin_avatar']) && !empty($_SESSION['admin_avatar'])) {
                                        $avatar = $_SESSION['admin_avatar'];
                                    }
                                    
                                    // 只有在有用户头像时才显示头像元素
                                    if (!empty($avatar)) {
                                ?>  
                                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="用户头像" class="w-8 h-8 rounded-full object-cover border-2 border-pink-500">
                                <?php } ?>
                                <span class="text-sm font-medium hidden md:inline-block"><?php echo htmlspecialchars($_SESSION['username'] ?? $_SESSION['admin_username'] ?? ''); ?></span>
                                <i class="fas fa-chevron-down text-xs text-gray-500 hidden md:inline-block"></i>
                            </button>
                            
                            <!-- 下拉菜单 -->
                            <div class="absolute right-0 mt-2 w-48 glass-morphism rounded-xl shadow-lg hidden group-hover:block transition-all duration-300 z-50">
                                <div class="py-2">
                                    <a href="/user/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/30 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-user mr-2"></i>个人中心
                                    </a>
                                    <a href="/user/settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/30 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-cog mr-2"></i>设置
                                    </a>
                                    <a href="/user/favorites.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/30 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-heart mr-2"></i>我的收藏
                                    </a>
                                    <a href="/user/comments.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/30 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-comment mr-2"></i>我的评论
                                    </a>
                                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                    <a href="/admin/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-white/30 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-cog mr-2"></i>后台管理
                                    </a>
                                    <?php endif; ?>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <a href="/logout.php" class="block px-4 py-2 text-sm text-red-500 hover:bg-white/30 rounded-lg transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt mr-2"></i>退出登录
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- 未登录状态 -->
                        <a href="/login.php" class="nav-link">登录</a>
                        <a href="/register.php" class="modern-btn text-white">注册</a>
                    <?php endif; ?>
                    
                    <!-- 移动端菜单按钮 -->
                    <div class="md:hidden">
                        <button type="button" id="mobile-menu-button" class="text-gray-700 hover:text-pink-500">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 移动端导航菜单 -->
        <div id="mobile-menu" class="md:hidden hidden glass-morphism border-t border-white/20">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <!-- 移动端搜索框 -->
                <form method="GET" action="/articles.php" class="relative mb-4">
                    <input type="text" name="search" placeholder="搜索文章、图片或角色..." class="form-control">
                    <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-pink-500 hover:text-pink-600">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <a href="/index.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">首页</a>
                <a href="/articles.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">文章</a>
                <a href="/gallery.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">画廊</a>
                <a href="/character.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">角色生成器</a>
                <a href="/submit.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">投稿</a>
                
                <div class="border-t border-white/20 pt-4 mt-2">
                    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                        <!-- 已登录状态 -->
                        <a href="/admin/notifications.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-bell mr-2"></i>通知中心
                        </a>
                        <a href="/user/profile.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-user mr-2"></i>个人中心
                        </a>
                        <a href="/user/settings.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-cog mr-2"></i>设置
                        </a>
                        <a href="/user/favorites.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-heart mr-2"></i>我的收藏
                        </a>
                        <a href="/user/comments.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-comment mr-2"></i>我的评论
                        </a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/admin/dashboard.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-cog mr-2"></i>后台管理
                        </a>
                        <?php endif; ?>
                        <a href="/logout.php" class="block py-2 px-4 text-red-500 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-sign-out-alt mr-2"></i>退出登录
                        </a>
                    <?php else: ?>
                        <!-- 未登录状态 -->
                        <a href="/login.php" class="block py-2 px-4 text-gray-700 hover:text-pink-500 transition-colors duration-200 font-medium rounded-lg hover:bg-white/20">
                            <i class="fas fa-sign-in-alt mr-2"></i>登录
                        </a>
                        <a href="/register.php" class="block py-2 px-4 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-lg hover:bg-gradient-to-r hover:from-pink-600 hover:to-purple-700 transition-colors duration-200">
                            <i class="fas fa-user-plus mr-2"></i>注册
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- 通知相关JavaScript -->
    <script>
        // 标记通知为已读
        function markAsRead(notificationId) {
            fetch('/api/mark-notification-read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 更新通知项样式
                    const notificationItem = document.querySelector('[onclick="markAsRead(' + notificationId + ')"').closest('div');
                    if (notificationItem) {
                        notificationItem.classList.remove('bg-pink-50/70');
                        notificationItem.classList.add('bg-white/50');
                        notificationItem.querySelector('button').textContent = '';
                    }
                    
                    // 更新通知计数
                    updateNotificationCount();
                }
            })
            .catch(error => console.error('Error marking notification as read:', error));
        }
        
        // 标记全部通知为已读
        function markAllAsRead() {
            fetch('/api/mark-notification-read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'all=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 更新所有通知项样式
                    const notificationItems = document.querySelectorAll('#notification-list > div');
                    notificationItems.forEach(item => {
                        item.classList.remove('bg-pink-50/70');
                        item.classList.add('bg-white/50');
                        const button = item.querySelector('button');
                        if (button) {
                            button.textContent = '';
                        }
                    });
                    
                    // 更新通知计数
                    updateNotificationCount();
                }
            })
            .catch(error => console.error('Error marking all notifications as read:', error));
        }
        
        // 更新通知计数
        function updateNotificationCount() {
            fetch('/api/notification-count.php')
            .then(response => response.json())
            .then(data => {
                const countElement = document.getElementById('notification-count');
                if (countElement) {
                    if (data.count > 0) {
                        countElement.textContent = data.count;
                        countElement.classList.remove('hidden');
                    } else {
                        countElement.classList.add('hidden');
                    }
                }
            })
            .catch(error => console.error('Error updating notification count:', error));
        }
        
        // 实时更新通知
        function realTimeUpdateNotifications() {
            fetch('/api/notifications.php?limit=5')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 这里可以实现通知的实时更新逻辑
                    // 例如，检查是否有新通知并显示提示
                }
            })
            .catch(error => console.error('Error updating notifications:', error));
        }
        
        // 页面加载时更新通知计数
        document.addEventListener('DOMContentLoaded', function() {
            updateNotificationCount();
        });
        
        // 每分钟自动更新通知
        setInterval(realTimeUpdateNotifications, 60000);
        
        // 移动端菜单切换
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });
    </script>
    
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">