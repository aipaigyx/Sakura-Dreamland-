<?php
/**
 * 文章详情页模板
 */

// 包含功能函数
require_once __DIR__ . '/functions.php';



// 获取文章ID
$article_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 1;

// 获取文章详情
$article = aniblog_get_article($article_id);

// 将文章信息存入全局变量，供页面标题使用
$GLOBALS['article'] = $article;

// 增加文章浏览量
if ($article) {
    $sql = "UPDATE articles SET view_count = view_count + 1 WHERE id = ?";
    db_exec($sql, [$article_id]);
}

// 获取推荐文章
$recommended_articles = get_popular_content('articles', 4);

// 获取上一篇和下一篇文章
$previous_article = get_previous_article($article_id);
$next_article = get_next_article($article_id);

// 获取设置值
$settings = get_settings();

// 检查用户登录状态并获取用户信息
$current_user = null;
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $current_user_id = $_SESSION['user_id'];
    // 获取用户详细信息，包括头像
    $sql = "SELECT id, username, email, avatar, role FROM users WHERE id = ?";
    $current_user = db_query_one($sql, [$current_user_id]);
}

// 获取评论列表
$comments = [];
if ($article_id) {
    $sql = "SELECT c.*, u.username, u.avatar FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.article_id = ? AND c.parent_id IS NULL ORDER BY c.created_at DESC";
    $comments = db_query($sql, [$article_id]);
    
    // 获取每个评论的回复
    foreach ($comments as &$comment) {
        $sql = "SELECT c.*, u.username, u.avatar FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.parent_id = ? ORDER BY c.created_at ASC";
        $comment['replies'] = db_query($sql, [$comment['id']]);
    }
}

// 处理评论提交后的提示信息
$message = '';
$message_type = '';
if (isset($_GET['success'])) {
    $message = '评论提交成功！';
    $message_type = 'success';
} elseif (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 1:
            $message = '请填写完整的评论信息！';
            break;
        case 2:
            $message = '请输入有效的邮箱地址！';
            break;
        case 3:
            $message = '评论提交失败，请稍后重试！';
            break;
        default:
            $message = '评论提交失败，请稍后重试！';
    }
    $message_type = 'error';
}

// 引入头部
include_once __DIR__ . '/header.php';
?>
    <!-- 封面图灯箱效果 -->
    <div class="relative w-full h-96 md:h-[500px] overflow-hidden mb-8">
        <!-- 二次元装饰元素 -->
        <div class="absolute top-10 left-10 w-16 h-16 text-pink-300 opacity-70 animate-float-slow">
            <i class="fas fa-heart"></i>
        </div>
        <div class="absolute top-20 right-20 w-12 h-12 text-purple-300 opacity-60 animate-float-medium">
            <i class="fas fa-star"></i>
        </div>
        <div class="absolute bottom-20 left-20 w-14 h-14 text-blue-300 opacity-70 animate-float-fast">
            <i class="fas fa-moon"></i>
        </div>
        <!-- 背景渐变 -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/30 to-black/70 z-10"></div>
        
        <!-- 封面图片 -->
        <?php if (!empty($article['cover_image'])): ?>
            <img src="<?php echo $article['cover_image']; ?>" alt="<?php echo $article['title']; ?>" class="w-full h-full object-cover">
        <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-pink-400 to-purple-500">
                <i class="fas fa-image text-8xl text-white opacity-70"></i>
            </div>
        <?php endif; ?>
        
        <!-- 文章信息叠加层 -->
        <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 text-white z-20">
            <div class="container mx-auto">
                <!-- 分类标签 -->
                <div class="text-sm font-medium mb-3 inline-block bg-gradient-to-r from-pink-400 to-purple-400 text-white px-3 py-1 rounded-full">
                    <?php echo $article['category_id'] ? aniblog_get_categories()[$article['category_id'] - 1]['name'] : '未分类'; ?>
                </div>
                
                <!-- 文章标题 -->
                <h1 class="text-3xl md:text-5xl font-bold mb-4">
                    <?php echo $article['title']; ?>
                </h1>
                
                <!-- 文章元信息 -->
                <div class="flex flex-wrap items-center text-white/90 text-sm mb-6">
                    <span class="flex items-center mr-6 mb-2">
                        <img src="<?php echo !empty($article['author_avatar']) ? $article['author_avatar'] : 'https://i.pravatar.cc/150?img=' . $article['author_id']; ?>" alt="作者头像" class="w-6 h-6 rounded-full object-cover mr-2 border border-pink-400">
                        作者：<?php echo $article['author_id'] == 1 ? '樱花酱' : '未知作者'; ?>
                    </span>
                    <span class="flex items-center mr-6 mb-2"><i class="far fa-calendar-alt text-pink-400 mr-2"></i> <?php echo date('Y-m-d', strtotime($article['created_at'])); ?></span>
                    <span class="flex items-center mr-6 mb-2"><i class="far fa-eye text-pink-400 mr-2"></i> <?php echo $article['view_count']; ?>次浏览</span>
                    <span class="flex items-center mb-2"><i class="far fa-comment text-pink-400 mr-2"></i> <?php echo $article['comment_count']; ?>条评论</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 文章详情 -->
    <section class="mb-12 relative">
        <?php if ($article): ?>
            <!-- 封面图渐变延伸效果 -->
            <div class="absolute top-0 left-0 right-0 h-32 -mt-16 bg-gradient-to-b from-transparent to-pink-50/80 z-0"></div>
            
            <!-- 封面图投影渐变 -->
            <div class="absolute top-0 left-0 right-0 h-40 -mt-20" style="background: radial-gradient(circle at center, rgba(255,107,139,0.1) 0%, rgba(255,255,255,0) 70%);"></div>
            
            <div class="modern-card p-8 md:p-12 bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl relative z-10">
                <!-- 文章内容 -->
                <div class="prose max-w-none mb-8 text-gray-700 gutenberg-content">
                    <?php echo $article['content']; ?>
                </div>
                    
                <!-- 二次元动画效果 -->
                <style>
                    @keyframes float-slow {
                        0%, 100% { transform: translateY(0px) rotate(0deg); }
                        50% { transform: translateY(-20px) rotate(180deg); }
                    }
                    
                    @keyframes float-medium {
                        0%, 100% { transform: translateY(0px) rotate(0deg); }
                        50% { transform: translateY(-15px) rotate(180deg); }
                    }
                    
                    @keyframes float-fast {
                        0%, 100% { transform: translateY(0px) rotate(0deg); }
                        50% { transform: translateY(-10px) rotate(180deg); }
                    }
                    
                    @keyframes twinkle {
                        0%, 100% { opacity: 0.3; }
                        50% { opacity: 1; }
                    }
                    
                    @keyframes twinkle-slow {
                        0%, 100% { opacity: 0.2; }
                        50% { opacity: 0.8; }
                    }
                    
                    @keyframes twinkle-fast {
                        0%, 100% { opacity: 0.4; }
                        50% { opacity: 1; }
                    }
                    
                    .animate-float-slow {
                        animation: float-slow 8s ease-in-out infinite;
                    }
                    
                    .animate-float-medium {
                        animation: float-medium 6s ease-in-out infinite;
                    }
                    
                    .animate-float-fast {
                        animation: float-fast 4s ease-in-out infinite;
                    }
                    
                    .animate-twinkle {
                        animation: twinkle 2s ease-in-out infinite;
                    }
                    
                    .animate-twinkle-slow {
                        animation: twinkle-slow 3s ease-in-out infinite;
                    }
                    
                    .animate-twinkle-fast {
                        animation: twinkle-fast 1.5s ease-in-out infinite;
                    }
                </style>
                
                <!-- 文章标签 -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-tags text-pink-500 mr-2"></i>
                        <span class="text-gray-600 font-medium">文章标签</span>
                    </div>
                    
                    <?php 
                        $article_tags = get_article_tags($article_id);
                        if (empty($article_tags)): 
                    ?>
                        <span class="text-gray-400 text-sm">暂无标签</span>
                    <?php else: ?>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($article_tags as $tag): ?>
                                <a href="#" class="inline-block bg-gradient-to-r from-pink-400 to-purple-400 text-white px-4 py-1 rounded-full text-sm hover:from-pink-500 hover:to-purple-500 transition-all duration-200 shadow-sm hover:shadow-md relative overflow-hidden group">
                                    <span class="relative z-10"><?php echo $tag['name']; ?></span>
                                    <span class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300 origin-right"></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- 文章点赞、收藏、分享按钮 -->
                <div class="flex flex-wrap gap-3 mb-8">
                    <button class="like-btn px-6 py-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-full hover:from-pink-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 flex items-center gap-2" data-article-id="<?php echo $article_id; ?>">
                        <i class="far fa-heart"></i>
                        <span>点赞 (<?php echo $article['like_count'] ?? 0; ?>)</span>
                    </button>
                    <button class="favorite-btn px-6 py-2 bg-gradient-to-r from-purple-500 to-blue-600 text-white font-medium rounded-full hover:from-purple-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 flex items-center gap-2" data-article-id="<?php echo $article_id; ?>">
                        <i class="far fa-bookmark"></i>
                        <span>收藏 (0)</span>
                    </button>
                    <button class="share-btn px-6 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-medium rounded-full hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 flex items-center gap-2" data-article-id="<?php echo $article_id; ?>">
                        <i class="far fa-share-square"></i>
                        <span>分享</span>
                    </button>
                </div>
                
                <!-- 点赞、收藏和分享的JavaScript -->
                <script>
                    // 点赞功能
                        document.addEventListener('DOMContentLoaded', function() {
                            const likeBtns = document.querySelectorAll('.like-btn');
                            
                            likeBtns.forEach(btn => {
                                btn.addEventListener('click', function() {
                                    const articleId = this.getAttribute('data-article-id');
                                    const heartIcon = this.querySelector('i');
                                    const countSpan = this.querySelector('span');
                                    
                                    // 发送点赞请求
                                    fetch('/like.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: 'article_id=' + articleId
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            // 更新UI
                                            if (data.is_liked) {
                                                heartIcon.classList.remove('far');
                                                heartIcon.classList.add('fas', 'text-red-500');
                                                btn.classList.add('liked');
                                                btn.classList.add('bg-gradient-to-r', 'from-red-500', 'to-pink-600');
                                            } else {
                                                heartIcon.classList.remove('fas', 'text-red-500');
                                                heartIcon.classList.add('far');
                                                btn.classList.remove('liked');
                                                btn.classList.remove('bg-gradient-to-r', 'from-red-500', 'to-pink-600');
                                                btn.classList.add('bg-gradient-to-r', 'from-pink-500', 'to-purple-600');
                                            }
                                            countSpan.textContent = '点赞 (' + data.like_count + ')';
                                            
                                            // 添加动画效果
                                            btn.classList.add('scale-105');
                                            setTimeout(() => {
                                                btn.classList.remove('scale-105');
                                            }, 200);
                                        } else {
                                            alert('点赞失败：' + data.message);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('点赞请求失败:', error);
                                        alert('点赞失败，请稍后重试');
                                    });
                                });
                            });
                        
                        // 收藏功能
                        const favoriteBtns = document.querySelectorAll('.favorite-btn');
                        
                        favoriteBtns.forEach(btn => {
                            const articleId = btn.getAttribute('data-article-id');
                            const bookmarkIcon = btn.querySelector('i');
                            const countSpan = btn.querySelector('span');
                            
                            // 初始化收藏状态
                            fetch('/favorite.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'article_id=' + articleId + '&action=check'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    if (data.is_favorited) {
                                        bookmarkIcon.classList.remove('far');
                                        bookmarkIcon.classList.add('fas', 'text-yellow-500');
                                        btn.classList.add('favorited');
                                        countSpan.textContent = '收藏 (' + data.favorite_count + ')';
                                    } else {
                                        countSpan.textContent = '收藏 (' + data.favorite_count + ')';
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('初始化收藏状态失败:', error);
                            });
                            
                            // 收藏按钮点击事件
                            btn.addEventListener('click', function() {
                                // 发送收藏请求
                                fetch('/favorite.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'article_id=' + articleId
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // 更新UI
                                        if (data.is_favorited) {
                                            bookmarkIcon.classList.remove('far');
                                            bookmarkIcon.classList.add('fas', 'text-yellow-500');
                                            btn.classList.add('favorited');
                                            btn.classList.add('bg-gradient-to-r', 'from-yellow-500', 'to-orange-600');
                                        } else {
                                            bookmarkIcon.classList.remove('fas', 'text-yellow-500');
                                            bookmarkIcon.classList.add('far');
                                            btn.classList.remove('favorited');
                                            btn.classList.remove('bg-gradient-to-r', 'from-yellow-500', 'to-orange-600');
                                        }
                                        countSpan.textContent = '收藏 (' + data.favorite_count + ')';
                                        
                                        // 添加动画效果
                                        btn.classList.add('scale-105');
                                        setTimeout(() => {
                                            btn.classList.remove('scale-105');
                                        }, 200);
                                    } else {
                                        alert('收藏失败：' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('收藏请求失败:', error);
                                    alert('收藏失败，请稍后重试');
                                });
                            });
                        });
                        
                        // 评论点赞功能
                        const commentLikeBtns = document.querySelectorAll('.comment-like-btn');
                        
                        commentLikeBtns.forEach(btn => {
                            const commentId = btn.getAttribute('data-comment-id');
                            const heartIcon = btn.querySelector('i');
                            const countSpan = btn.querySelector('span');
                            
                            // 评论点赞点击事件
                            btn.addEventListener('click', function() {
                                // 发送点赞请求
                                fetch('/comment-like.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'comment_id=' + commentId
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // 更新UI
                                        if (data.is_liked) {
                                            heartIcon.classList.remove('far');
                                            heartIcon.classList.add('fas', 'text-red-500');
                                            btn.classList.add('liked');
                                        } else {
                                            heartIcon.classList.remove('fas', 'text-red-500');
                                            heartIcon.classList.add('far');
                                            btn.classList.remove('liked');
                                        }
                                        countSpan.textContent = data.like_count;
                                        
                                        // 添加动画效果
                                        btn.classList.add('scale-105');
                                        setTimeout(() => {
                                            btn.classList.remove('scale-105');
                                        }, 200);
                                    } else {
                                        alert('点赞失败：' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('点赞请求失败:', error);
                                    alert('点赞失败，请稍后重试');
                                });
                            });
                        });
                        
                        // 分享功能
                        const shareBtns = document.querySelectorAll('.share-btn');
                        
                        shareBtns.forEach(btn => {
                            btn.addEventListener('click', function() {
                                const articleId = this.getAttribute('data-article-id');
                                const shareUrl = window.location.href;
                                
                                // 简单的分享逻辑，实际项目中可以集成社交媒体分享SDK
                                if (navigator.share) {
                                    // 使用Web Share API（如果支持）
                                    navigator.share({
                                        title: document.title,
                                        text: '分享一篇精彩的动漫文章',
                                        url: shareUrl
                                    })
                                    .catch(error => {
                                        console.error('分享失败:', error);
                                        // 降级方案
                                        copyToClipboard(shareUrl);
                                    });
                                } else {
                                    // 降级方案：复制链接到剪贴板
                                    copyToClipboard(shareUrl);
                                }
                            });
                        });
                        
                        // 复制到剪贴板功能
                        function copyToClipboard(text) {
                            navigator.clipboard.writeText(text)
                            .then(() => {
                                alert('链接已复制到剪贴板，您可以分享给好友了！');
                            })
                            .catch(error => {
                                console.error('复制失败:', error);
                                alert('复制失败，请手动复制链接');
                            });
                        }
                    });
                </script>
                
                <!-- 上一篇/下一篇导航 -->
                <div class="flex flex-col md:flex-row justify-between gap-4 py-6 border-t border-pink-100 relative overflow-hidden bg-gradient-to-b from-pink-50/50 to-purple-50/50 rounded-2xl">
                    <!-- 动漫装饰元素 -->
                    <div class="absolute top-0 left-0 w-20 h-20 text-pink-300 opacity-70 animate-float-slow">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="absolute top-10 right-10 w-16 h-16 text-purple-300 opacity-60 animate-float-fast">
                        <i class="fas fa-sparkles"></i>
                    </div>
                    <div class="absolute bottom-0 right-0 w-16 h-16 text-blue-300 opacity-50 animate-float-medium">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="absolute bottom-10 left-20 w-12 h-12 text-yellow-300 opacity-60 animate-float-slow">
                        <i class="fas fa-sun"></i>
                    </div>
                    
                    <!-- 上一篇 -->
                    <div class="flex-1 relative z-10">
                        <?php if ($previous_article): ?>
                            <a href="/article.php?id=<?php echo $previous_article['id']; ?>" class="block group hover:text-pink-500 transition-all duration-300 p-5 bg-white/80 rounded-2xl hover:bg-gradient-to-r from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 hover:border-pink-300 shadow-sm hover:shadow-lg transform hover:-translate-y-1">
                                <!-- 可爱猫咪装饰 -->
                                <div class="absolute top-0 right-0 w-20 h-20 -mt-5 -mr-5 relative">
                                    <!-- 猫猫头 -->
                                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-16 h-16 bg-gradient-to-r from-pink-400 to-purple-400 rounded-full flex items-center justify-center text-white shadow-lg animate-pulse"></div>
                                    
                                    <!-- 猫猫耳朵 -->
                                    <div class="absolute top-0 left-1/4 w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-500 rounded-tl-full rounded-tr-full transform rotate-[-20deg] shadow-lg"></div>
                                    <div class="absolute top-0 right-1/4 w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-500 rounded-tl-full rounded-tr-full transform rotate-[20deg] shadow-lg"></div>
                                    
                                    <!-- 猫猫眼睛 -->
                                    <div class="absolute bottom-6 left-1/2 transform -translate-x-3 w-3 h-3 bg-white rounded-full shadow-inner"></div>
                                    <div class="absolute bottom-6 left-1/2 transform translate-x-1 w-3 h-3 bg-white rounded-full shadow-inner"></div>
                                    
                                    <!-- 猫猫鼻子 -->
                                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rounded-full"></div>
                                </div>
                                
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3 group-hover:text-pink-500 transition-colors duration-200">
                                    <i class="fas fa-chevron-left text-lg"></i>
                                    <span class="font-medium">上一篇</span>
                                </div>
                                
                                <div class="font-medium text-lg bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 group-hover:from-pink-600 group-hover:via-purple-600 group-hover:to-blue-600 transition-all duration-300 line-clamp-2"><?php echo $previous_article['title']; ?></div>
                                
                                <!-- 可爱表情装饰 -->
                                <div class="flex items-center gap-2 mt-3 text-xs text-pink-500 opacity-80">
                                    <span>💕</span>
                                    <span><?php echo $previous_article['category_id'] ? aniblog_get_categories()[$previous_article['category_id'] - 1]['name'] : '未分类'; ?></span>
                                    <span>💕</span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="p-5 bg-white/80 rounded-2xl border-2 border-pink-200 opacity-50 cursor-not-allowed">
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                                    <i class="fas fa-chevron-left text-lg"></i>
                                    <span class="font-medium">上一篇</span>
                                </div>
                                <div class="font-medium text-lg text-gray-400">暂无上一篇文章</div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- 下一篇 -->
                    <div class="flex-1 text-right relative z-10">
                        <?php if ($next_article): ?>
                            <a href="/article.php?id=<?php echo $next_article['id']; ?>" class="block group hover:text-pink-500 transition-all duration-300 p-5 bg-white/80 rounded-2xl hover:bg-gradient-to-r from-purple-50 via-pink-50 to-purple-50 border-2 border-purple-200 hover:border-purple-300 shadow-sm hover:shadow-lg transform hover:-translate-y-1">
                                <!-- 可爱猫咪装饰 -->
                                <div class="absolute top-0 left-0 w-20 h-20 -mt-5 -ml-5 relative">
                                    <!-- 猫猫头 -->
                                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-16 h-16 bg-gradient-to-r from-purple-400 to-blue-400 rounded-full flex items-center justify-center text-white shadow-lg animate-pulse"></div>
                                    
                                    <!-- 猫猫耳朵 -->
                                    <div class="absolute top-0 left-1/4 w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-tl-full rounded-tr-full transform rotate-[-20deg] shadow-lg"></div>
                                    <div class="absolute top-0 right-1/4 w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-tl-full rounded-tr-full transform rotate-[20deg] shadow-lg"></div>
                                    
                                    <!-- 猫猫眼睛 -->
                                    <div class="absolute bottom-6 left-1/2 transform -translate-x-3 w-3 h-3 bg-white rounded-full shadow-inner"></div>
                                    <div class="absolute bottom-6 left-1/2 transform translate-x-1 w-3 h-3 bg-white rounded-full shadow-inner"></div>
                                    
                                    <!-- 猫猫鼻子 -->
                                    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rounded-full"></div>
                                </div>
                                
                                <div class="flex items-center justify-end gap-2 text-sm text-gray-500 mb-3 group-hover:text-pink-500 transition-colors duration-200">
                                    <span class="font-medium">下一篇</span>
                                    <i class="fas fa-chevron-right text-lg"></i>
                                </div>
                                
                                <div class="font-medium text-lg bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 group-hover:from-pink-600 group-hover:via-purple-600 group-hover:to-blue-600 transition-all duration-300 line-clamp-2"><?php echo $next_article['title']; ?></div>
                                
                                <!-- 可爱表情装饰 -->
                                <div class="flex items-center justify-end gap-2 mt-3 text-xs text-purple-500 opacity-80">
                                    <span>✨</span>
                                    <span><?php echo $next_article['category_id'] ? aniblog_get_categories()[$next_article['category_id'] - 1]['name'] : '未分类'; ?></span>
                                    <span>✨</span>
                                </div>
                            </a>
                        <?php else: ?>
                            <div class="p-5 bg-white/80 rounded-2xl border-2 border-purple-200 opacity-50 cursor-not-allowed">
                                <div class="flex items-center justify-end gap-2 text-sm text-gray-500 mb-3">
                                    <span class="font-medium">下一篇</span>
                                    <i class="fas fa-chevron-right text-lg"></i>
                                </div>
                                <div class="font-medium text-lg text-gray-400">暂无下一篇文章</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- 文章不存在时显示 -->
            <div class="modern-card p-8 text-center bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
                <i class="fas fa-exclamation-triangle text-6xl text-pink-500 mb-4"></i>
                <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2">文章不存在</h2>
                <p class="text-gray-600 mb-6">抱歉，您访问的文章不存在或已被删除。</p>
                <a href="/" class="inline-block px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-full hover:opacity-90 transition-opacity duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300">
                    返回首页
                </a>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- 推荐文章 -->
    <section class="mb-12">
        <div class="modern-card p-8 bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
            <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6 flex items-center">
                <i class="fas fa-fire text-pink-500 mr-2"></i> 推荐文章
            </h2>
            <!-- 横向滚动的推荐文章卡片 -->
            <div class="overflow-x-auto pb-4">
                <div class="flex gap-4 min-w-max">
                    <?php foreach ($recommended_articles as $rec_article): ?>
                        <div class="modern-card p-4 bg-white/80 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 w-64 flex-shrink-0">
                            <div class="h-32 bg-gradient-to-r from-pink-300 to-purple-300 rounded-lg overflow-hidden mb-3">
                                <?php if (!empty($rec_article['cover_image'])): ?>
                                    <img src="<?php echo $rec_article['cover_image']; ?>" alt="<?php echo $rec_article['title']; ?>" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-r from-pink-400 to-purple-500">
                                        <i class="fas fa-image text-3xl text-white opacity-70"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <a href="/article.php?id=<?php echo $rec_article['id']; ?>" class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2 line-clamp-2 hover:text-pink-600">
                                <?php echo $rec_article['title']; ?>
                            </a>
                            <div class="flex items-center justify-between text-xs">
                                <span class="bg-pink-100 text-pink-600 px-2 py-0.5 rounded-full">
                                    <?php echo $rec_article['category_id'] ? aniblog_get_categories()[$rec_article['category_id'] - 1]['name'] : '未分类'; ?>
                                </span>
                                <span class="text-pink-500 flex items-center gap-1"><i class="far fa-eye"></i> <?php echo $rec_article['view_count']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    

    
    <?php if ($settings['enable_danmaku'] == 1): ?>
    <!-- 弹幕互动区 -->
    <section class="mb-12">
        <div class="modern-card p-8 bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6 flex items-center">
                <i class="fas fa-comment-dots text-pink-500 mr-2"></i> 弹幕互动
            </h2>
            
            <!-- 弹幕容器 -->
            <div class="danmaku-container relative w-full h-64 md:h-80 bg-black/5 rounded-2xl overflow-hidden mb-6">
                <!-- 弹幕会通过JavaScript动态添加到这里 -->
            </div>
            
            <!-- 弹幕发送表单 -->
            <div class="danmaku-input bg-white rounded-2xl p-4 shadow-md">
                <form class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
                    <div class="flex-1">
                        <input type="text" name="danmaku_content" placeholder="发送弹幕..." 
                               class="w-full px-4 py-2 border border-blue-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <div>
                            <select name="danmaku_color" class="px-3 py-2 border border-blue-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200 hover:border-blue-400">
                                <option value="#ffffff" <?php echo $settings['default_danmaku_color'] === '#ffffff' ? 'selected' : ''; ?>>白色</option>
                                <option value="#ff6b81" <?php echo $settings['default_danmaku_color'] === '#ff6b81' ? 'selected' : ''; ?>>粉色</option>
                                <option value="#ff6348" <?php echo $settings['default_danmaku_color'] === '#ff6348' ? 'selected' : ''; ?>>红色</option>
                                <option value="#32cd32" <?php echo $settings['default_danmaku_color'] === '#32cd32' ? 'selected' : ''; ?>>绿色</option>
                                <option value="#1e90ff" <?php echo $settings['default_danmaku_color'] === '#1e90ff' ? 'selected' : ''; ?>>蓝色</option>
                                <option value="#ffd700" <?php echo $settings['default_danmaku_color'] === '#ffd700' ? 'selected' : ''; ?>>金色</option>
                                <option value="#9370db" <?php echo $settings['default_danmaku_color'] === '#9370db' ? 'selected' : ''; ?>>紫色</option>
                                <option value="#00ffff" <?php echo $settings['default_danmaku_color'] === '#00ffff' ? 'selected' : ''; ?>>青色</option>
                            </select>
                        </div>
                        <div>
                            <select name="danmaku_size" class="px-3 py-2 border border-blue-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200 hover:border-blue-400">
                                <option value="20" <?php echo $settings['default_danmaku_size'] == 20 ? 'selected' : ''; ?>>小</option>
                                <option value="25" <?php echo $settings['default_danmaku_size'] == 25 ? 'selected' : ''; ?>>中</option>
                                <option value="30" <?php echo $settings['default_danmaku_size'] == 30 ? 'selected' : ''; ?>>大</option>
                                <option value="35" <?php echo $settings['default_danmaku_size'] == 35 ? 'selected' : ''; ?>>超大</option>
                            </select>
                        </div>
                        <div>
                            <select name="danmaku_mode" class="px-3 py-2 border border-blue-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200 hover:border-blue-400">
                                <option value="scroll" <?php echo $settings['default_danmaku_mode'] === 'scroll' ? 'selected' : ''; ?>>滚动</option>
                                <option value="top" <?php echo $settings['default_danmaku_mode'] === 'top' ? 'selected' : ''; ?>>顶部</option>
                                <option value="bottom" <?php echo $settings['default_danmaku_mode'] === 'bottom' ? 'selected' : ''; ?>>底部</option>
                            </select>
                        </div>
                        <button type="button" class="danmaku-send-btn px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 text-white font-medium rounded-full hover:from-blue-600 hover:to-cyan-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            发射弹幕
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- 评论区 -->
    <section class="mb-12">
        <div class="modern-card p-8 bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6 flex items-center">
                <i class="fas fa-comments text-pink-500 mr-2"></i> 评论区
            </h2>
            

            
            <!-- 评论表单 -->
            <div class="mb-12">
                <?php if ($current_user): ?>
                    <!-- 已登录用户 -->
                    <div class="flex items-center gap-4 mb-6">
                        <img src="<?php echo !empty($current_user['avatar']) ? $current_user['avatar'] : 'https://i.pravatar.cc/150?img=' . $current_user['id']; ?>" alt="当前用户头像" class="w-12 h-12 rounded-full object-cover border-2 border-pink-400 shadow-md">
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500"><?php echo $current_user['username']; ?></h3>
                                <span class="text-xs bg-gradient-to-r from-pink-400 to-purple-400 text-white px-2 py-1 rounded-full shadow-sm">
                                    <?php echo $current_user['role'] === 'admin' ? '管理员' : ($current_user['role'] === 'editor' ? '编辑' : '用户'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4">发表评论</h3>
                    <form class="space-y-4 comment-form">
                        <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
                        <input type="hidden" name="parent_id" id="parent_id" value="">
                        <input type="hidden" name="user_id" value="<?php echo $current_user['id']; ?>">
                        <input type="hidden" name="name" value="<?php echo $current_user['username']; ?>">
                        <input type="hidden" name="email" value="<?php echo $current_user['email']; ?>">
                        
                        <div>
                            <label for="comment-content" class="block text-sm font-medium text-gray-700 mb-1">评论内容</label>
                            <textarea id="comment-content" name="content" rows="4" class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 bg-white/80" placeholder="请输入您的评论"></textarea>
                        </div>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-full hover:from-pink-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 comment-submit-btn">
                            发表评论
                        </button>
                    </form>
                <?php else: ?>
                    <!-- 未登录用户 -->
                    <h3 class="text-lg font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4">发表评论</h3>
                    <form class="space-y-4 comment-form">
                        <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
                        <input type="hidden" name="parent_id" id="parent_id" value="">
                        <?php 
                            // 为未登录用户生成唯一的整数ID
                            if (!isset($_COOKIE['visitor_id'])) {
                                // 生成唯一的整数ID，确保在MySQL int(11)范围内（-2147483648到2147483647）
                                // 直接生成一个在100000到2147483647之间的随机数
                                $visitor_id = rand(100000, 2147483647);
                                // 设置Cookie，有效期为1年
                                setcookie('visitor_id', $visitor_id, time() + 365 * 24 * 60 * 60, '/');
                            } else {
                                $visitor_id = (int)$_COOKIE['visitor_id'];
                            }
                        ?>
                        <input type="hidden" name="user_id" value="<?php echo $visitor_id; ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="comment-name" class="block text-sm font-medium text-gray-700 mb-1">昵称</label>
                                <input type="text" id="comment-name" name="name" class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 bg-white/80" placeholder="请输入您的昵称">
                            </div>
                            <div>
                                <label for="comment-email" class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
                                <input type="email" id="comment-email" name="email" class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 bg-white/80" placeholder="请输入您的邮箱">
                            </div>
                        </div>
                        <div>
                            <label for="comment-content" class="block text-sm font-medium text-gray-700 mb-1">评论内容</label>
                            <textarea id="comment-content" name="content" rows="4" class="w-full px-4 py-2 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 bg-white/80" placeholder="请输入您的评论"></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                <a href="/login.php" class="text-pink-500 hover:text-pink-600 transition-colors duration-200">登录</a> 或 <a href="/register.php" class="text-pink-500 hover:text-pink-600 transition-colors duration-200">注册</a> 后可获得更好的评论体验
                            </div>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-full hover:from-pink-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 comment-submit-btn">
                                发表评论
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <!-- 评论消息提示 -->
                <div id="comment-message" class="mt-4 p-4 rounded-lg hidden"></div>
            </div>
            
            <!-- 评论列表 -->
            <div class="space-y-6" id="comments-list">
                <?php if (empty($comments)): ?>
                    <div class="text-center py-8 bg-white/80 rounded-xl">
                        <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">还没有评论，快来发表第一条评论吧！</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="flex gap-4 p-4 bg-white/80 rounded-xl hover:shadow-md transition-shadow duration-300" data-comment-id="<?php echo $comment['id']; ?>">
                            <img src="<?php echo !empty($comment['avatar']) ? $comment['avatar'] : 'https://i.pravatar.cc/150?img=' . $comment['user_id']; ?>" alt="用户头像" class="w-10 h-10 rounded-full object-cover flex-shrink-0 border-2 border-pink-400">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500">
                                        <?php echo !empty($comment['username']) ? $comment['username'] : (!empty($comment['name']) ? $comment['name'] : '匿名用户'); ?>
                                    </div>
                                    <div class="text-sm text-pink-500">
                                        <?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="text-gray-700 mb-3">
                                    <?php echo $comment['content']; ?>
                                </div>
                                <div class="flex items-center gap-4 text-sm">
                                    <button class="text-gray-500 hover:text-pink-500 flex items-center gap-1 transition-all duration-200 hover:translate-x-1 reply-btn" data-comment-id="<?php echo $comment['id']; ?>">
                                        <i class="far fa-comment-dots"></i> 回复
                                    </button>
                                    <button class="text-gray-500 hover:text-pink-500 flex items-center gap-1 transition-all duration-200 comment-like-btn" data-comment-id="<?php echo $comment['id']; ?>">
                                        <i class="far fa-heart"></i> <span><?php echo $comment['likes']; ?></span>
                                    </button>
                                </div>
                                
                                <!-- 回复输入框 -->
                                <div class="reply-container hidden mt-4 ml-8">
                                    <div class="flex gap-4">
                                        <img src="<?php echo !empty($current_user['avatar']) ? $current_user['avatar'] : 'https://i.pravatar.cc/150?img=' . ($current_user ? $current_user['id'] : '1'); ?>" alt="当前用户头像" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border-2 border-pink-400">
                                        <div class="flex-1">
                                            <textarea rows="2" class="reply-content w-full px-4 py-2 border border-pink-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200 bg-white/80" placeholder="回复这条评论..."></textarea>
                                            <div class="flex justify-end gap-2 mt-2">
                                                <button class="reply-cancel-btn text-sm px-4 py-1 border border-gray-300 rounded-full hover:bg-gray-100 transition-colors duration-200">取消</button>
                                                <button class="reply-submit-btn text-sm px-4 py-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-full hover:from-pink-600 hover:to-purple-700 transition-all duration-200 shadow-sm" data-comment-id="<?php echo $comment['id']; ?>">
                                                    发表回复
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 回复层级显示 -->
                                <?php if (!empty($comment['replies'])): ?>
                                    <div class="mt-4 ml-10 space-y-4">
                                        <?php foreach ($comment['replies'] as $reply): ?>
                                            <div class="flex gap-4 p-3 bg-pink-50/80 rounded-lg">
                                                <img src="<?php echo !empty($reply['avatar']) ? $reply['avatar'] : 'https://i.pravatar.cc/150?img=' . $reply['user_id']; ?>" alt="用户头像" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border-2 border-pink-400">
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <div class="font-medium text-sm bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500">
                                                            <?php echo !empty($reply['username']) ? $reply['username'] : (!empty($reply['name']) ? $reply['name'] : '匿名用户'); ?>
                                                        </div>
                                                        <div class="text-xs text-pink-500">
                                                            <?php echo date('Y-m-d H:i', strtotime($reply['created_at'])); ?>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm text-gray-700 mb-2">
                                                        <?php echo $reply['content']; ?>
                                                    </div>
                                                    <div class="flex items-center gap-4 text-xs">
                                                        <button class="text-gray-500 hover:text-pink-500 flex items-center gap-1 transition-all duration-200 hover:translate-x-1 reply-btn" data-comment-id="<?php echo $reply['id']; ?>">
                                                            <i class="far fa-comment-dots"></i> 回复
                                                        </button>
                                                        <button class="text-gray-500 hover:text-pink-500 flex items-center gap-1 transition-all duration-200 comment-like-btn" data-comment-id="<?php echo $reply['id']; ?>">
                                                            <i class="far fa-heart"></i> <span><?php echo $reply['likes']; ?></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- 评论相关JavaScript -->
            <script>
                // 显示消息提示
                function showMessage(message, type = 'error') {
                    const messageEl = document.getElementById('comment-message');
                    messageEl.textContent = message;
                    messageEl.className = 'mt-4 p-4 rounded-lg ' + (type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
                    messageEl.classList.remove('hidden');
                    
                    // 3秒后隐藏消息
                    setTimeout(() => {
                        messageEl.classList.add('hidden');
                    }, 3000);
                }
                
                // 评论表单提交 - 处理所有具有comment-form类的表单
                const commentForms = document.querySelectorAll('.comment-form');
                commentForms.forEach(function(commentForm) {
                    commentForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        const submitBtn = this.querySelector('.comment-submit-btn');
                        const originalText = submitBtn.innerHTML;
                        
                        // 显示加载状态
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> 提交中...';
                        
                        // 准备表单数据
                        const formData = new FormData(this);
                        const urlEncodedData = new URLSearchParams(formData).toString();
                        
                        // 发送评论请求
                        fetch('/comments-api.php?action=add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: urlEncodedData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('HTTP error! status: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                showMessage(data.message, 'success');
                                // 清空评论内容
                                const contentTextarea = this.querySelector('textarea[name="content"]');
                                if (contentTextarea) {
                                    contentTextarea.value = '';
                                }
                                // 刷新页面以显示新评论
                                location.reload();
                            } else {
                                showMessage(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('评论提交失败:', error);
                            showMessage('评论提交失败，请稍后重试: ' + error.message);
                        })
                        .finally(() => {
                            // 恢复按钮状态
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        });
                    });
                });
                
                // 评论点赞功能
                const likeBtns = document.querySelectorAll('.comment-like-btn');
                likeBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const commentId = this.getAttribute('data-comment-id');
                        const heartIcon = this.querySelector('i');
                        const countSpan = this.querySelector('span');
                        
                        // 发送点赞请求
                        fetch('/comments-api.php?action=like', {
                            method: 'POST',
                            body: 'comment_id=' + commentId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // 更新点赞数
                                countSpan.textContent = data.data.likes;
                                // 更新图标状态
                                heartIcon.classList.remove('far');
                                heartIcon.classList.add('fas', 'text-red-500');
                            }
                        })
                        .catch(error => {
                            console.error('点赞失败:', error);
                        });
                    });
                });
                
                // 回复功能
                const replyBtns = document.querySelectorAll('.reply-btn');
                replyBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const commentId = this.getAttribute('data-comment-id');
                        const commentCard = this.closest('[data-comment-id]');
                        const existingReplyContainer = commentCard.querySelector('.reply-container');
                        
                        // 隐藏所有其他回复容器
                        document.querySelectorAll('.reply-container').forEach(container => {
                            container.classList.add('hidden');
                        });
                        
                        // 显示/隐藏当前回复容器
                        if (existingReplyContainer) {
                            existingReplyContainer.classList.toggle('hidden');
                        }
                    });
                });
                
                // 回复取消按钮
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('reply-cancel-btn')) {
                        const replyContainer = e.target.closest('.reply-container');
                        replyContainer.classList.add('hidden');
                    }
                });
                
                // 回复提交按钮
                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('reply-submit-btn')) {
                        const commentId = e.target.getAttribute('data-comment-id');
                        const replyContainer = e.target.closest('.reply-container');
                        const replyContent = replyContainer.querySelector('.reply-content').value.trim();
                        const submitBtn = e.target;
                        const originalText = submitBtn.textContent;
                        
                        if (!replyContent) {
                            showMessage('回复内容不能为空');
                            return;
                        }
                        
                        // 显示加载状态
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> 提交中...';
                        
                        // 发送回复请求
                        // 准备表单数据
                        const formData = new FormData();
                        formData.append('comment_id', commentId);
                        formData.append('content', replyContent);
                        
                        // 添加用户信息
                        <?php if ($current_user): ?>
                            formData.append('user_id', '<?php echo $current_user['id']; ?>');
                            formData.append('name', '<?php echo $current_user['username']; ?>');
                            formData.append('email', '<?php echo $current_user['email']; ?>');
                        <?php else: ?>
                            // 获取临时用户ID
                            const visitorId = document.querySelector('input[name="user_id"]').value;
                            const name = document.querySelector('input[name="name"]').value;
                            const email = document.querySelector('input[name="email"]').value;
                            
                            if (name && email) {
                                formData.append('user_id', visitorId);
                                formData.append('name', name);
                                formData.append('email', email);
                            } else {
                                // 如果没有填写姓名和邮箱，使用默认值
                                formData.append('user_id', visitorId || '1');
                                formData.append('name', '访客');
                                formData.append('email', 'visitor@example.com');
                            }
                        <?php endif; ?>
                        
                        fetch('/comments-api.php?action=reply', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams(formData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showMessage(data.message, 'success');
                                // 清空回复内容
                                replyContainer.querySelector('.reply-content').value = '';
                                // 隐藏回复容器
                                replyContainer.classList.add('hidden');
                                // 刷新评论列表
                                location.reload();
                            } else {
                                showMessage(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('回复提交失败:', error);
                            showMessage('回复提交失败，请稍后重试');
                        })
                        .finally(() => {
                            // 恢复按钮状态
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        });
                    }
                });
            </script>
            
            <!-- 弹幕样式 -->
            <style>
        @keyframes danmakuMoveScroll {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(-100%);
            }
        }
        
        /* 顶部弹幕 */
        @keyframes danmakuMoveTop {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(-100%);
            }
        }
        
        /* 底部弹幕 */
        @keyframes danmakuMoveBottom {
            from {
                transform: translateX(100%);
            }
            to {
                transform: translateX(-100%);
            }
        }
        
        .danmaku-scroll {
            animation: danmakuMoveScroll linear;
        }
        
        .danmaku-top {
            top: 20px;
            animation: danmakuMoveTop linear;
        }
        
        .danmaku-bottom {
            bottom: 20px;
            animation: danmakuMoveBottom linear;
        }
    </style>
    
    <script>
        // 回复功能
        document.addEventListener('DOMContentLoaded', function() {
            const replyBtns = document.querySelectorAll('.reply-btn');
            const parentIdInput = document.getElementById('parent_id');
            const commentTextarea = document.getElementById('comment');
            
            replyBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const commentId = this.getAttribute('data-comment-id');
                    parentIdInput.value = commentId;
                    
                    // 滚动到评论框
                    commentTextarea.focus();
                    window.scrollTo({
                        top: commentTextarea.offsetTop - 100,
                        behavior: 'smooth'
                    });
                });
            });
        });
        
        // 弹幕功能
        document.addEventListener('DOMContentLoaded', function() {
            // 获取弹幕设置
            const danmakuSettings = {
                durationMin: <?php echo $settings['danmaku_duration_min']; ?>,
                durationMax: <?php echo $settings['danmaku_duration_max']; ?>
            };
            
            const danmakuContainer = document.querySelector('.danmaku-container');
            const danmakuContent = document.querySelector('input[name="danmaku_content"]');
            const danmakuColor = document.querySelector('select[name="danmaku_color"]');
            const danmakuSize = document.querySelector('select[name="danmaku_size"]');
            const danmakuMode = document.querySelector('select[name="danmaku_mode"]');
            const danmakuSendBtn = document.querySelector('.danmaku-send-btn');
            const articleId = document.querySelector('input[name="article_id"]').value;
            
            // 加载历史弹幕
            function loadHistoryDanmakus() {
                fetch(`/danmaku.php?article_id=${articleId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            // 延迟显示历史弹幕，避免一次性全部显示
                            data.data.forEach((danmaku, index) => {
                                setTimeout(() => {
                                    createDanmaku(danmaku);
                                }, index * 200);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('加载历史弹幕失败:', error);
                    });
            }
            
            // 发送弹幕
            function sendDanmaku() {
                const content = danmakuContent.value.trim();
                if (!content) return;
                
                const danmaku = {
                    content: content,
                    color: danmakuColor.value,
                    size: parseInt(danmakuSize.value),
                    mode: danmakuMode.value
                };
                
                // 立即显示弹幕（不等待服务器响应）
                createDanmaku(danmaku);
                
                // 发送到服务器保存
                fetch('/danmaku.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `content=${encodeURIComponent(content)}&article_id=${articleId}&color=${encodeURIComponent(danmakuColor.value)}&size=${danmakuSize.value}&mode=${danmakuMode.value}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('弹幕发送失败:', data.message);
                    }
                })
                .catch(error => {
                    console.error('弹幕发送失败:', error);
                });
                
                // 清空输入框
                danmakuContent.value = '';
            }
            
            // 创建弹幕元素
            function createDanmaku(danmaku) {
                if (!danmakuContainer) return;
                
                const danmakuEl = document.createElement('div');
                danmakuEl.className = `danmaku danmaku-${danmaku.mode}`;
                danmakuEl.textContent = danmaku.content;
                danmakuEl.style.color = danmaku.color;
                danmakuEl.style.fontSize = `${danmaku.size}px`;
                
                // 设置弹幕位置和动画
                const containerWidth = danmakuContainer.offsetWidth;
                const containerHeight = danmakuContainer.offsetHeight;
                const danmakuWidth = danmaku.content.length * danmaku.size * 0.6;
                
                // 随机动画持续时间（从设置获取范围）
                const duration = danmakuSettings.durationMin + Math.random() * (danmakuSettings.durationMax - danmakuSettings.durationMin);
                
                // 根据模式设置弹幕位置
                switch (danmaku.mode) {
                    case "top":
                        danmakuEl.style.top = '20px';
                        danmakuEl.style.left = `${containerWidth}px`;
                        break;
                    case "bottom":
                        danmakuEl.style.bottom = '20px';
                        danmakuEl.style.left = `${containerWidth}px`;
                        break;
                    case "scroll":
                    default:
                        danmakuEl.style.top = `${20 + Math.random() * (containerHeight - 40 - danmaku.size)}px`;
                        danmakuEl.style.left = `${containerWidth}px`;
                        break;
                }
                
                // 设置动画持续时间
                danmakuEl.style.animationDuration = `${duration}s`;
                
                // 添加到容器
                danmakuContainer.appendChild(danmakuEl);
                
                // 动画结束后移除元素
                setTimeout(() => {
                    if (danmakuEl.parentNode === danmakuContainer) {
                        danmakuContainer.removeChild(danmakuEl);
                    }
                }, duration * 1000);
            }
            
            // 绑定发送按钮点击事件
            if (danmakuSendBtn) {
                danmakuSendBtn.addEventListener('click', sendDanmaku);
            }
            
            // 绑定输入框回车事件
            if (danmakuContent) {
                danmakuContent.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendDanmaku();
                    }
                });
            }
            
            // 加载历史弹幕
            loadHistoryDanmakus();
        });
    </script>

<?php
// 引入底部
include_once __DIR__ . '/footer.php';
?>

