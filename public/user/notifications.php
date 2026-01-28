<?php
/**
 * 用户通知页面
 */

// 引入函数文件，会自动处理会话启动
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../db.php';

// 检查是否已登录
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // 检查是否是管理员登录
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // 管理员自动登录到用户账户
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = 1; // 假设管理员的用户ID是1
        $_SESSION['username'] = $_SESSION['admin_username'];
        $_SESSION['user_role'] = 'admin';
    } else {
        header('Location: /login.php');
        exit;
    }
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$user = db_query_one($sql, [$user_id]);

$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取通知设置
    $comment_replies = isset($_POST['comment_replies']) ? 1 : 0;
    $article_comments = isset($_POST['article_comments']) ? 1 : 0;
    $comment_likes = isset($_POST['comment_likes']) ? 1 : 0;
    $article_likes = isset($_POST['article_likes']) ? 1 : 0;
    $article_favorites = isset($_POST['article_favorites']) ? 1 : 0;
    $site_updates = isset($_POST['site_updates']) ? 1 : 0;
    $events = isset($_POST['events']) ? 1 : 0;
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $in_site_notifications = isset($_POST['in_site_notifications']) ? 1 : 0;
    
    // 更新通知设置
    $update_sql = "UPDATE users SET 
                    comment_replies = ?, 
                    article_comments = ?, 
                    comment_likes = ?, 
                    article_likes = ?, 
                    article_favorites = ?, 
                    site_updates = ?, 
                    events = ?, 
                    email_notifications = ?, 
                    in_site_notifications = ? 
                    WHERE id = ?";
    $result = db_exec($update_sql, [
        $comment_replies, $article_comments, $comment_likes, $article_likes, 
        $article_favorites, $site_updates, $events, $email_notifications, 
        $in_site_notifications, $user_id
    ]);
    
    if ($result !== false) {
        $success = '通知设置已保存';
        // 刷新用户信息
        $user = db_query_one($sql, [$user_id]);
    } else {
        $error = '通知设置保存失败，请稍后重试';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通知设置 - 樱花梦境</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef2f2 0%, #f3e8ff 50%, #e0f2fe 100%);
            background-attachment: fixed;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        }
        .form-control {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid rgba(255, 107, 139, 0.2);
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: #FF6B8B;
            box-shadow: 0 0 0 3px rgba(255, 107, 139, 0.1);
        }
        /* 开关样式 */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e2e8f0;
            transition: .4s;
            border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background: linear-gradient(135deg, #FF6B8B, #8B5CF6);
        }
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- 包含头部 -->
    <?php require_once __DIR__ . '/../header.php'; ?>
    
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 页面标题 -->
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6">
            <i class="fas fa-bell mr-2"></i>通知设置
        </h1>
        
        <!-- 返回按钮 -->
        <a href="profile.php" class="inline-flex items-center text-pink-500 hover:text-pink-600 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>返回个人中心
        </a>
        
        <!-- 内容区域 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 侧边菜单 -->
            <div class="lg:col-span-1">
                <div class="glass-morphism rounded-2xl p-6">
                    <h3 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4">
                        设置菜单
                    </h3>
                    <nav class="space-y-2">
                        <a href="settings.php" class="block px-4 py-3 bg-white/50 text-gray-700 rounded-lg hover:bg-white hover:text-pink-500 transition-colors duration-200 font-medium">
                            <i class="fas fa-user-cog mr-2"></i>个人资料
                        </a>
                        <a href="password.php" class="block px-4 py-3 bg-white/50 text-gray-700 rounded-lg hover:bg-white hover:text-pink-500 transition-colors duration-200 font-medium">
                            <i class="fas fa-lock mr-2"></i>修改密码
                        </a>
                        <a href="privacy.php" class="block px-4 py-3 bg-white/50 text-gray-700 rounded-lg hover:bg-white hover:text-pink-500 transition-colors duration-200 font-medium">
                            <i class="fas fa-shield-alt mr-2"></i>隐私设置
                        </a>
                        <a href="notifications.php" class="block px-4 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg font-medium">
                            <i class="fas fa-bell mr-2"></i>通知设置
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- 主要内容 -->
            <div class="lg:col-span-2">
                <div class="glass-morphism rounded-2xl p-6">
                    <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6">
                        通知设置
                    </h2>
                    
                    <?php if ($error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- 通知设置表单 -->
                    <form method="POST" action="notifications.php" class="space-y-6">
                        <!-- 评论通知 -->
                        <div class="bg-pink-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-pink-600 mb-3">评论通知</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="cursor-pointer font-medium">有人回复我的评论</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="comment_replies" <?php echo ($user['comment_replies'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="cursor-pointer font-medium">我的文章收到新评论</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="article_comments" <?php echo ($user['article_comments'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 点赞通知 -->
                        <div class="bg-purple-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-purple-600 mb-3">点赞通知</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="cursor-pointer font-medium">我的评论被点赞</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="comment_likes" <?php echo ($user['comment_likes'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="cursor-pointer font-medium">我的文章被点赞</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="article_likes" <?php echo ($user['article_likes'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 收藏通知 -->
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-blue-600 mb-3">收藏通知</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="cursor-pointer font-medium">我的文章被收藏</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="article_favorites" <?php echo ($user['article_favorites'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 系统通知 -->
                        <div class="bg-green-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-green-600 mb-3">系统通知</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="cursor-pointer font-medium">网站更新通知</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="site_updates" <?php echo ($user['site_updates'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="cursor-pointer font-medium">活动通知</label>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="events" <?php echo ($user['events'] ?? 1) ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 通知方式 -->
                        <div class="bg-yellow-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-yellow-600 mb-3">通知方式</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="email_notifications" name="email_notifications" class="text-pink-500 focus:ring-pink-500 border-gray-300 rounded" <?php echo ($user['email_notifications'] ?? 1) ? 'checked' : ''; ?>>
                                    <label for="email_notifications" class="cursor-pointer font-medium">邮件通知</label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" id="in_site_notifications" name="in_site_notifications" class="text-pink-500 focus:ring-pink-500 border-gray-300 rounded" <?php echo ($user['in_site_notifications'] ?? 1) ? 'checked' : ''; ?>>
                                    <label for="in_site_notifications" class="cursor-pointer font-medium">站内通知</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 保存按钮 -->
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:from-pink-600 hover:to-purple-700 transition-colors duration-200 font-medium shadow-md">
                                保存设置
                            </button>
                            <button type="reset" class="px-6 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium border border-gray-300">
                                重置
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- 包含底部 -->
    <?php require_once __DIR__ . '/../footer.php'; ?>
</body>
</html>