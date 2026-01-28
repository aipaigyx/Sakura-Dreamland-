<?php
/**
 * 用户隐私设置页面
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
    $profile_visibility = $_POST['profile_visibility'] ?? 'public';
    $share_data = isset($_POST['share_data']) ? 1 : 0;
    $privacy_notifications = isset($_POST['privacy_notifications']) ? 1 : 0;
    
    // 更新隐私设置
    $update_sql = "UPDATE users SET 
                    profile_visibility = ?, 
                    share_data = ?, 
                    privacy_notifications = ? 
                    WHERE id = ?";
    $result = db_exec($update_sql, [$profile_visibility, $share_data, $privacy_notifications, $user_id]);
    
    if ($result !== false) {
        $success = '隐私设置已保存';
        // 刷新用户信息
        $user = db_query_one($sql, [$user_id]);
    } else {
        $error = '隐私设置保存失败，请稍后重试';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>隐私设置 - 樱花梦境</title>
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
    </style>
</head>
<body class="min-h-screen">
    <!-- 包含头部 -->
    <?php require_once __DIR__ . '/../header.php'; ?>
    
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 页面标题 -->
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6">
            <i class="fas fa-shield-alt mr-2"></i>隐私设置
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
                        <a href="privacy.php" class="block px-4 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg font-medium">
                            <i class="fas fa-shield-alt mr-2"></i>隐私设置
                        </a>
                        <a href="notifications.php" class="block px-4 py-3 bg-white/50 text-gray-700 rounded-lg hover:bg-white hover:text-pink-500 transition-colors duration-200 font-medium">
                            <i class="fas fa-bell mr-2"></i>通知设置
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- 主要内容 -->
            <div class="lg:col-span-2">
                <div class="glass-morphism rounded-2xl p-6">
                    <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6">
                        隐私设置
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
                    
                    <!-- 隐私设置表单 -->
                    <form method="POST" action="privacy.php" class="space-y-6">
                        <!-- 个人资料可见性 -->
                        <div class="bg-pink-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-pink-600 mb-3">个人资料可见性</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <input type="radio" id="public" name="profile_visibility" value="public" class="text-pink-500 focus:ring-pink-500 border-gray-300" <?php echo (($user['profile_visibility'] ?? 'public') === 'public') ? 'checked' : ''; ?>>
                                    <label for="public" class="cursor-pointer">
                                        <div class="font-medium">公开</div>
                                        <div class="text-sm text-gray-500">所有人都可以查看您的个人资料</div>
                                    </label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="radio" id="friends" name="profile_visibility" value="friends" class="text-pink-500 focus:ring-pink-500 border-gray-300" <?php echo (($user['profile_visibility'] ?? 'public') === 'friends') ? 'checked' : ''; ?>>
                                    <label for="friends" class="cursor-pointer">
                                        <div class="font-medium">仅关注者可见</div>
                                        <div class="text-sm text-gray-500">只有关注您的用户可以查看您的个人资料</div>
                                    </label>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="radio" id="private" name="profile_visibility" value="private" class="text-pink-500 focus:ring-pink-500 border-gray-300" <?php echo (($user['profile_visibility'] ?? 'public') === 'private') ? 'checked' : ''; ?>>
                                    <label for="private" class="cursor-pointer">
                                        <div class="font-medium">仅自己可见</div>
                                        <div class="text-sm text-gray-500">只有您自己可以查看您的个人资料</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 数据共享设置 -->
                        <div class="bg-purple-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-purple-600 mb-3">数据共享设置</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label for="share_data" class="cursor-pointer font-medium">允许数据共享</label>
                                    <div class="relative inline-block w-12 h-6 transition duration-200 ease-in-out rounded-full">
                                        <input type="checkbox" id="share_data" name="share_data" class="sr-only" <?php echo ($user['share_data'] ?? 0) ? 'checked' : ''; ?>>
                                        <div class="absolute inset-0 bg-gray-300 rounded-full transition duration-200 ease-in-out"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition duration-200 ease-in-out transform"></div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">允许我们使用您的匿名数据来改进网站体验</p>
                            </div>
                        </div>
                        
                        <!-- 通知设置 -->
                        <div class="bg-blue-50 p-4 rounded-xl">
                            <h3 class="text-lg font-medium text-blue-600 mb-3">隐私通知</h3>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label for="privacy_notifications" class="cursor-pointer font-medium">接收隐私相关通知</label>
                                    <div class="relative inline-block w-12 h-6 transition duration-200 ease-in-out rounded-full">
                                        <input type="checkbox" id="privacy_notifications" name="privacy_notifications" class="sr-only" <?php echo ($user['privacy_notifications'] ?? 0) ? 'checked' : ''; ?>>
                                        <div class="absolute inset-0 bg-gray-300 rounded-full transition duration-200 ease-in-out"></div>
                                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition duration-200 ease-in-out transform"></div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500">当我们的隐私政策更新时通知您</p>
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