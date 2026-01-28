<?php
/**
 * 文章列表页
 */

// 加载功能函数
require_once 'functions.php';

// 获取当前页码
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// 获取搜索关键词
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// 获取分类筛选
$category = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : 0;

// 构建查询条件
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "title LIKE ? OR content LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category > 0) {
    $where[] = "category_id = ?";
    $params[] = $category;
}

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// 获取文章总数
$count_sql = "SELECT COUNT(*) as count FROM articles $where_clause";
$total_result = db_query_one($count_sql, $params);
$total_articles = $total_result['count'];
$total_pages = ceil($total_articles / $per_page);

// 获取文章列表
$articles_sql = "SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id $where_clause ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$articles = db_query($articles_sql, $params);

// 获取所有分类
$categories = aniblog_get_categories();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章列表 - 樱花梦境</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- 自定义样式 -->
    <style>
        /* 统一的二次元风格 */
        body {
            background: linear-gradient(135deg, #fef2f2 0%, #f3e8ff 50%, #e0f2fe 100%);
            background-attachment: fixed;
        }
        
        .modern-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 16px;
            border: 1px solid rgba(255, 107, 139, 0.2);
            transition: all 0.3s ease;
        }
        
        .modern-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
        }
        
        .gradient-text {
            background: linear-gradient(90deg, #FF6B8B, #8B5CF6, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .pagination-btn {
            transition: all 0.3s ease;
        }
        
        .pagination-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- 导航栏 -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="text-2xl font-bold gradient-text">樱花梦境</a>
                
                <!-- 搜索框 -->
                <div class="relative w-1/3 max-w-md">
                    <form method="GET" action="articles.php">
                        <input type="text" name="search" placeholder="搜索文章..." 
                               value="<?php echo htmlspecialchars($search); ?>" 
                               class="w-full px-4 py-2 pl-10 rounded-full border border-pink-200 focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-pink-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- 导航链接 -->
                <div class="flex items-center space-x-6">
                    <a href="/" class="text-gray-700 hover:text-pink-500 transition-colors">首页</a>
                    <a href="articles.php" class="text-pink-500 font-medium">文章</a>
                    <a href="gallery.php" class="text-gray-700 hover:text-pink-500 transition-colors">画廊</a>
                    <a href="character.php" class="text-gray-700 hover:text-pink-500 transition-colors">角色生成器</a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <!-- 页面标题 -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold gradient-text mb-2">文章列表</h1>
            <p class="text-gray-600">共 <?php echo $total_articles; ?> 篇文章</p>
        </div>
        
        <!-- 筛选栏 -->
        <div class="modern-card p-4 mb-8 flex flex-wrap items-center gap-4">
            <div>
                <label for="category" class="text-sm font-medium text-gray-700 mr-2">分类：</label>
                <form method="GET" action="articles.php" class="inline">
                    <select id="category" name="category" 
                            onchange="this.form.submit()" 
                            class="px-3 py-1 border border-pink-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                        <option value="0">全部分类</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo $category === $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                </form>
            </div>
            
            <div class="ml-auto">
                <span class="text-sm text-gray-500">
                    第 <?php echo $page; ?> 页，共 <?php echo $total_pages; ?> 页
                </span>
            </div>
        </div>
        
        <!-- 文章列表 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if (empty($articles)): ?>
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">没有找到相关文章</p>
                    <?php if (!empty($search)): ?>
                        <a href="articles.php" class="text-pink-500 hover:underline mt-2 inline-block">
                            清除搜索条件
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <div class="modern-card overflow-hidden">
                        <?php if (!empty($article['cover_image'])): ?>
                            <div class="h-48 overflow-hidden">
                                <img src="<?php echo $article['cover_image']; ?>" 
                                     alt="<?php echo $article['title']; ?>" 
                                     class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                            </div>
                        <?php else: ?>
                            <div class="h-48 bg-gradient-to-r from-pink-400 to-purple-500 flex items-center justify-center text-white">
                                <i class="fas fa-image text-4xl opacity-70"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs bg-pink-100 text-pink-600 px-3 py-1 rounded-full">
                                    <?php echo $article['category_name'] ?? '未分类'; ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?php echo date('Y-m-d', strtotime($article['created_at'])); ?>
                                </span>
                            </div>
                            
                            <h3 class="text-xl font-semibold mb-3 hover:text-pink-500 transition-colors">
                                <a href="/article.php?id=<?php echo $article['id']; ?>">
                                    <?php echo $article['title']; ?>
                                </a>
                            </h3>
                            
                            <p class="text-gray-600 line-clamp-3 mb-4">
                                <?php echo $article['summary'] ?? substr(strip_tags($article['content']), 0, 150) . '...'; ?>
                            </p>
                            
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center gap-4">
                                    <span><i class="far fa-eye mr-1"></i> <?php echo $article['view_count']; ?></span>
                                    <span><i class="far fa-comment mr-1"></i> <?php echo $article['comment_count']; ?></span>
                                    <span><i class="far fa-heart mr-1"></i> <?php echo $article['like_count']; ?></span>
                                </div>
                                <a href="/article.php?id=<?php echo $article['id']; ?>" 
                                   class="text-pink-500 hover:underline flex items-center gap-1">
                                    阅读全文 <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- 分页 -->
        <div class="mt-12 flex justify-center">
            <nav class="flex items-center space-x-2">
                <!-- 上一页 -->
                <a href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo $category; ?>" 
                   class="pagination-btn px-4 py-2 rounded-full border border-pink-200 text-gray-700 hover:bg-pink-50 transition-colors <?php echo $page <= 1 ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
                
                <!-- 页码 -->
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo $category; ?>" 
                       class="pagination-btn px-4 py-2 rounded-full <?php echo $i === $page ? 'bg-gradient-to-r from-pink-500 to-purple-500 text-white font-medium' : 'border border-pink-200 text-gray-700 hover:bg-pink-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <!-- 下一页 -->
                <a href="?page=<?php echo min($total_pages, $page + 1); ?>&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo $category; ?>" 
                   class="pagination-btn px-4 py-2 rounded-full border border-pink-200 text-gray-700 hover:bg-pink-50 transition-colors <?php echo $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </nav>
        </div>
    </main>
    
    <!-- 页脚 -->
    <footer class="bg-white/80 backdrop-blur-md border-t border-pink-100 py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-600">
                <p>&copy; 2026 樱花梦境. 保留所有权利.</p>
            </div>
        </div>
    </footer>
</body>
</html>