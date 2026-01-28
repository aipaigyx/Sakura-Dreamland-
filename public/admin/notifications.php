<?php
// 包含功能文件
include '../functions.php';

// 检查用户是否已登录
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    header('Location: /login.php');
    exit;
}

// 检查用户权限（如果需要）
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /');
    exit;
}

// 处理标记全部已读
if (isset($_POST['mark_all_read'])) {
    mark_all_notifications_as_read($_SESSION['user_id']);
    header('Location: notifications.php');
    exit;
}

// 处理删除通知
if (isset($_POST['delete_notification'])) {
    $notification_id = $_POST['id'];
    delete_notification($notification_id, $_SESSION['user_id']);
    header('Location: notifications.php');
    exit;
}

// 处理删除全部通知
if (isset($_POST['delete_all_notifications'])) {
    // 这里可以添加删除全部通知的功能
    // delete_all_user_notifications($_SESSION['user_id']);
    header('Location: notifications.php');
    exit;
}

// 获取所有通知
$notifications = get_user_notifications($_SESSION['user_id'], 50);

// 获取未读通知数量
$unread_count = get_unread_notification_count($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通知中心 - 后台管理</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- 自定义配置 -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#EC4899',
                        secondary: '#8B5CF6',
                        dark: '#1F2937',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                },
            }
        }
    </script>
    <!-- 自定义工具类 -->
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
            .glass-morphism {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.18);
            }
            .hover-lift {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- 顶部导航 -->
    <?php include '../header.php'; ?>

    <!-- 主要内容 -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- 页面标题 -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">通知中心</h1>
                <p class="text-gray-600">查看和管理所有通知</p>
            </div>
            
            <!-- 通知统计和操作 -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-8 hover-lift">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <p class="text-gray-600">共有 <span class="font-bold text-primary"><?php echo count($notifications); ?></span> 条通知</p>
                        <?php if ($unread_count > 0): ?>
                            <p class="text-gray-600">其中 <span class="font-bold text-primary"><?php echo $unread_count; ?></span> 条未读</p>
                        <?php endif; ?>
                    </div>
                    <div class="flex space-x-3">
                        <?php if ($unread_count > 0): ?>
                            <form method="POST" class="inline">
                                <button type="submit" name="mark_all_read" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors duration-200 flex items-center space-x-2">
                                    <i class="fas fa-check-double"></i>
                                    <span>标记全部已读</span>
                                </button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" class="inline">
                            <button type="submit" name="delete_all_notifications" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center space-x-2">
                                <i class="fas fa-trash"></i>
                                <span>清空通知</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- 通知列表 -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover-lift">
                <?php if (empty($notifications)): ?>
                    <!-- 暂无通知 -->
                    <div class="p-12 text-center">
                        <i class="fas fa-bell-slash text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-800 mb-2">暂无通知</h3>
                        <p class="text-gray-600">当有新通知时，会显示在这里</p>
                    </div>
                <?php else: ?>
                    <!-- 通知列表 -->
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-start space-x-4">
                                            <!-- 通知图标 -->
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                                    <i class="fas fa-bell text-primary"></i>
                                                </div>
                                            </div>
                                            
                                            <!-- 通知内容 -->
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start">
                                                    <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($notification['title']); ?></h3>
                                                    <span class="text-xs text-gray-500">
                                                        <?php echo date('Y-m-d H:i', strtotime($notification['created_at'])); ?>
                                                    </span>
                                                </div>
                                                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($notification['content']); ?></p>
                                                
                                                <!-- 通知状态 -->
                                                <div class="mt-2 flex items-center space-x-2">
                                                    <?php if (!$notification['is_read']): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                                            未读
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            已读
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <!-- 通知类型 -->
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        <?php echo htmlspecialchars($notification['type']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 操作按钮 -->
                                    <div class="ml-4 flex flex-col space-y-2">
                                        <?php if (!$notification['is_read']): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="id" value="<?php echo $notification['id']; ?>">
                                                <button type="submit" name="mark_read" class="text-primary hover:text-primary/80 transition-colors duration-200">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="id" value="<?php echo $notification['id']; ?>">
                                            <button type="submit" name="delete_notification" class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- 页脚 -->
    <?php include '../footer.php'; ?>
</body>
</html>