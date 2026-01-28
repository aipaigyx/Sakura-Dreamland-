<?php
/**
 * 用户个人资料页面
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

// 获取用户统计信息
$user_stats = [
    'articles' => 0, // 假设用户还不能发表文章
    'comments' => 0,
    'favorites' => 0,
    'likes' => 0
];

// 获取用户评论数量
$comments_sql = "SELECT COUNT(*) as count FROM comments WHERE user_id = ?";
$user_stats['comments'] = db_query_one($comments_sql, [$user_id])['count'];

// 获取用户收藏数量
$favorites_sql = "SELECT COUNT(*) as count FROM favorites WHERE user_id = ?";
$user_stats['favorites'] = db_query_one($favorites_sql, [$user_id])['count'];

// 获取用户的最新评论
$latest_comments_sql = "SELECT c.*, a.title as article_title FROM comments c LEFT JOIN articles a ON c.article_id = a.id WHERE c.user_id = ? ORDER BY c.created_at DESC LIMIT 5";
$latest_comments = db_query($latest_comments_sql, [$user_id]);

// 获取用户的收藏文章
$favorite_articles_sql = "SELECT a.*, f.created_at as favorite_date FROM articles a INNER JOIN favorites f ON a.id = f.article_id WHERE f.user_id = ? ORDER BY f.created_at DESC LIMIT 5";
$favorite_articles = db_query($favorite_articles_sql, [$user_id]);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>个人中心 - 樱花梦境</title>
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
        .bg-gradient-profile {
            background: linear-gradient(135deg, #fef2f2 0%, #f3e8ff 100%);
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
            个人中心
        </h1>
        
        <!-- 个人资料卡片 -->
        <div class="glass-morphism rounded-2xl p-6 mb-8">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- 用户头像 -->
                <div class="relative">
                    <img src="<?php echo $user['avatar'] ?: 'https://picsum.photos/id/1005/150/150'; ?>" alt="用户头像" class="w-24 h-24 rounded-full object-cover border-4 border-pink-500 shadow-lg">
                    <button class="absolute bottom-0 right-0 p-2 bg-pink-500 text-white rounded-full shadow-lg hover:bg-pink-600 transition-colors duration-200">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                
                <!-- 用户信息 -->
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </h2>
                    <p class="text-gray-600 mb-4">
                        <i class="fas fa-envelope mr-2 text-pink-500"></i>
                        <?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 mb-4">
                        <div class="text-center">
                            <div class="text-lg font-bold text-pink-500"><?php echo $user_stats['articles']; ?></div>
                            <div class="text-sm text-gray-600">文章</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-purple-500"><?php echo $user_stats['comments']; ?></div>
                            <div class="text-sm text-gray-600">评论</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-500"><?php echo $user_stats['favorites']; ?></div>
                            <div class="text-sm text-gray-600">收藏</div>
                        </div>
                    </div>
                    <a href="settings.php" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:from-pink-600 hover:to-purple-700 transition-colors duration-200 shadow-md">
                        <i class="fas fa-cog mr-2"></i>编辑资料
                    </a>
                </div>
            </div>
        </div>
        
        <!-- 用户活动 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 最新评论 -->
            <div class="glass-morphism rounded-2xl p-6">
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4">
                    <i class="fas fa-comment-dots mr-2"></i>我的评论
                </h3>
                
                <?php if ($user_stats['comments'] > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($latest_comments as $comment): ?>
                            <div class="border-b border-gray-100 pb-3 last:border-b-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600 mb-2"><?php echo mb_substr($comment['content'], 0, 100); ?><?php echo strlen($comment['content']) > 100 ? '...' : ''; ?></p>
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span><i class="fas fa-calendar-alt mr-1"></i><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                                            <?php if (!empty($comment['article_title'])): ?>
                                                <span class="mx-2">•</span>
                                                <span><i class="fas fa-file-alt mr-1"></i>《<?php echo $comment['article_title']; ?>》</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="#" class="ml-2 text-pink-500 hover:text-pink-600">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="comments.php" class="block text-center mt-4 text-sm text-pink-500 hover:text-pink-600 font-medium">
                        查看所有评论 <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-comment-slash text-4xl mb-2"></i>
                        <p>还没有发表过评论</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 收藏的文章 -->
            <div class="glass-morphism rounded-2xl p-6">
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4">
                    <i class="fas fa-heart mr-2"></i>我的收藏
                </h3>
                
                <?php if ($user_stats['favorites'] > 0): ?>
                    <div class="space-y-4">
                        <?php foreach ($favorite_articles as $article): ?>
                            <div class="flex gap-3">
                                <div class="w-20 h-16 bg-gradient-to-r from-pink-300 to-purple-300 rounded-lg overflow-hidden flex-shrink-0">
                                    <img src="<?php echo $article['cover_image'] ?: 'https://picsum.photos/id/1/200/100'; ?>" alt="文章封面" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-800 mb-1 hover:text-pink-500 transition-colors duration-200 truncate">
                                        <a href="/article.php?id=<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a>
                                    </h4>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <span><i class="fas fa-calendar-alt mr-1"></i><?php echo date('Y-m-d', strtotime($article['favorite_date'])); ?></span>
                                        <span class="mx-2">•</span>
                                        <span><i class="fas fa-eye mr-1"></i><?php echo $article['view_count']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="favorites.php" class="block text-center mt-4 text-sm text-pink-500 hover:text-pink-600 font-medium">
                        查看所有收藏 <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-heart-broken text-4xl mb-2"></i>
                        <p>还没有收藏任何文章</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- 包含底部 -->
    <?php require_once __DIR__ . '/../footer.php'; ?>
</body>
</html>