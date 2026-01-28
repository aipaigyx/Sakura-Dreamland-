<?php
/**
 * 用户收藏页面
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

// 获取用户收藏的文章
$favorites_sql = "SELECT a.*, f.created_at as favorite_date FROM articles a 
                INNER JOIN favorites f ON a.id = f.article_id 
                WHERE f.user_id = ? 
                ORDER BY f.created_at DESC";
$favorite_articles = db_query($favorites_sql, [$user_id]);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的收藏 - 樱花梦境</title>
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
            <i class="fas fa-heart mr-2"></i>我的收藏
        </h1>
        
        <!-- 返回按钮 -->
        <a href="profile.php" class="inline-flex items-center text-pink-500 hover:text-pink-600 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>返回个人中心
        </a>
        
        <!-- 收藏列表 -->
        <div class="glass-morphism rounded-2xl p-6">
            <?php if (count($favorite_articles) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($favorite_articles as $article): ?>
                        <div class="flex flex-col rounded-xl overflow-hidden shadow-md bg-white hover:shadow-lg transition-shadow duration-200">
                            <div class="h-40 bg-gradient-to-r from-pink-300 to-purple-300 overflow-hidden">
                                <img src="<?php echo $article['cover_image'] ?: 'https://picsum.photos/id/1/800/400'; ?>" alt="文章封面" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <div class="text-sm text-gray-500 mb-2">
                                    <i class="fas fa-calendar-alt mr-1"></i><?php echo date('Y-m-d', strtotime($article['favorite_date'])); ?>
                                </div>
                                <h3 class="text-lg font-medium text-gray-800 mb-2 hover:text-pink-500 transition-colors duration-200">
                                    <a href="/article.php?id=<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-3 flex-1 line-clamp-2"><?php echo $article['excerpt']; ?></p>
                                <div class="flex items-center justify-between text-sm">
                                    <div class="text-gray-500">
                                        <i class="fas fa-eye mr-1"></i><?php echo $article['view_count']; ?>
                                        <i class="fas fa-comment ml-3 mr-1"></i><?php echo $article['comment_count']; ?>
                                    </div>
                                    <button class="text-pink-500 hover:text-red-500 transition-colors duration-200">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-heart-broken text-4xl mb-3"></i>
                    <p class="text-lg">您还没有收藏任何文章</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- 包含底部 -->
    <?php require_once __DIR__ . '/../footer.php'; ?>
</body>
</html>