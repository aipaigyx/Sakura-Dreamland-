<?php
/**
 * 用户评论页面
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

// 获取用户评论
$comments_sql = "SELECT c.*, a.title as article_title, a.id as article_id FROM comments c 
                LEFT JOIN articles a ON c.article_id = a.id 
                WHERE c.user_id = ? 
                ORDER BY c.created_at DESC";
$comments = db_query($comments_sql, [$user_id]);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的评论 - 樱花梦境</title>
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
    </style>
</head>
<body class="min-h-screen">
    <!-- 包含头部 -->
    <?php require_once __DIR__ . '/../header.php'; ?>
    
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 页面标题 -->
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6">
            <i class="fas fa-comment-dots mr-2"></i>我的评论
        </h1>
        
        <!-- 返回按钮 -->
        <a href="profile.php" class="inline-flex items-center text-pink-500 hover:text-pink-600 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>返回个人中心
        </a>
        
        <!-- 评论列表 -->
        <div class="glass-morphism rounded-2xl p-6">
            <?php if (count($comments) > 0): ?>
                <div class="space-y-6">
                    <?php foreach ($comments as $comment): ?>
                        <div class="border-b border-gray-100 pb-4 last:border-b-0 last:pb-0">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-800">
                                        <?php if (!empty($comment['article_title'])): ?>
                                            <a href="/article.php?id=<?php echo $comment['article_id']; ?>" class="hover:text-pink-500 transition-colors duration-200">
                                                <?php echo $comment['article_title']; ?>
                                            </a>
                                        <?php else: ?>
                                            无标题文章
                                        <?php endif; ?>
                                    </h3>
                                    <div class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-calendar-alt mr-1"></i><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?>
                                    </div>
                                </div>
                                <button class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <div class="text-gray-700 pl-2 border-l-2 border-pink-200">
                                <?php echo $comment['content']; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-comment-slash text-4xl mb-3"></i>
                    <p class="text-lg">您还没有发表过任何评论</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- 包含底部 -->
    <?php require_once __DIR__ . '/../footer.php'; ?>
</body>
</html>