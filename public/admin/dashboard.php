<?php
/**
 * 后台首页（仪表盘）
 */

// 设置页面标题和描述
$page_title = '仪表盘';
$page_description = '网站数据统计和概览';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';

// 获取统计数据
$stats = get_statistics();
$latest_articles = get_latest_articles();
$latest_comments = get_latest_comments();
?>
            <!-- 统计卡片 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- 文章数量 -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-info">
                            <p class="stat-label">文章数量</p>
                            <h3 class="stat-value"><?php echo $stats['articles']; ?></h3>
                            <p class="stat-change positive">
                                <i class="fas fa-arrow-up mr-1"></i>
                                较上月增长 12%
                            </p>
                        </div>
                        <div class="stat-icon bg-gradient-to-br from-pink-500 to-purple-500">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    </div>
                </div>
                
                <!-- 图片数量 -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-info">
                            <p class="stat-label">图片数量</p>
                            <h3 class="stat-value"><?php echo $stats['images']; ?></h3>
                            <p class="stat-change positive">
                                <i class="fas fa-arrow-up mr-1"></i>
                                较上月增长 25%
                            </p>
                        </div>
                        <div class="stat-icon bg-gradient-to-br from-purple-500 to-blue-500">
                            <i class="fas fa-images"></i>
                        </div>
                    </div>
                </div>
                
                <!-- 评论数量 -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-info">
                            <p class="stat-label">评论数量</p>
                            <h3 class="stat-value"><?php echo $stats['comments']; ?></h3>
                            <p class="stat-change positive">
                                <i class="fas fa-arrow-up mr-1"></i>
                                较上月增长 8%
                            </p>
                        </div>
                        <div class="stat-icon bg-gradient-to-br from-blue-500 to-cyan-500">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                </div>
                
                <!-- 用户数量 -->
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-info">
                            <p class="stat-label">用户数量</p>
                            <h3 class="stat-value"><?php echo $stats['users']; ?></h3>
                            <p class="stat-change positive">
                                <i class="fas fa-arrow-up mr-1"></i>
                                较上月增长 15%
                            </p>
                        </div>
                        <div class="stat-icon bg-gradient-to-br from-cyan-500 to-teal-500">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 内容区域 -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 最新文章 -->
                <div class="lg:col-span-2">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">
                                <i class="fas fa-newspaper"></i>
                                最新文章
                            </h3>
                            <a href="articles.php" class="text-sm text-pink-500 hover:text-pink-600 transition-colors duration-200">
                                查看全部 <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        
                        <div class="admin-card-body">
                            <div class="space-y-4">
                                <?php if (!empty($latest_articles)): ?>
                                    <?php foreach ($latest_articles as $article): ?>
                                        <div class="flex gap-4 p-3 hover:bg-pink-50/50 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                                            <div class="w-16 h-16 bg-gradient-to-r from-pink-300 to-purple-300 rounded-lg overflow-hidden flex-shrink-0">
                                                <?php if (!empty($article['cover_image'])): ?>
                                                    <img src="<?php echo $article['cover_image']; ?>" alt="<?php echo $article['title']; ?>" class="w-full h-full object-cover">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex items-center justify-center text-white opacity-70">
                                                        <i class="fas fa-image text-xl"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-1">
                                                    <h4 class="text-sm font-medium hover:text-pink-500 transition-colors duration-200"><?php echo $article['title']; ?></h4>
                                                    <span class="text-xs bg-pink-100 text-pink-600 px-2 py-0.5 rounded-full">草稿</span>
                                                </div>
                                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                                    <span><i class="far fa-eye mr-1"></i> <?php echo $article['view_count']; ?></span>
                                                    <span><i class="far fa-comment mr-1"></i> <?php echo $article['comment_count']; ?></span>
                                                    <span><i class="far fa-calendar-alt mr-1"></i> <?php echo date('Y-m-d', strtotime($article['created_at'])); ?></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button class="text-gray-500 hover:text-blue-500 transition-colors duration-200 p-2 rounded-md hover:bg-blue-50">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-gray-500 hover:text-red-500 transition-colors duration-200 p-2 rounded-md hover:bg-red-50">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-8">
                                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">暂无文章</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 最新评论 -->
                <div>
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">
                                <i class="fas fa-comments"></i>
                                最新评论
                            </h3>
                            <a href="comments.php" class="text-sm text-pink-500 hover:text-pink-600 transition-colors duration-200">
                                查看全部 <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        
                        <div class="admin-card-body">
                            <div class="space-y-4">
                                <?php if (!empty($latest_comments)): ?>
                                    <?php foreach ($latest_comments as $comment): ?>
                                        <div class="p-3 hover:bg-pink-50/50 rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md">
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-6 h-6 rounded-full bg-gradient-to-r from-pink-500 to-purple-500 flex items-center justify-center text-white text-xs font-medium">
                                                    U
                                                </div>
                                                <span class="text-xs font-medium">用户</span>
                                                <span class="text-xs text-gray-400">•</span>
                                                <span class="text-xs text-gray-500"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                                            </div>
                                            <p class="text-sm text-gray-700 mb-2 line-clamp-2"><?php echo $comment['content']; ?></p>
                                            <div class="flex items-center justify-between">
                                                <a href="#" class="text-xs text-pink-500 hover:text-pink-600 transition-colors duration-200">
                                                    <?php echo $comment['article_title'] ?? '未知文章'; ?>
                                                </a>
                                                <div class="flex items-center gap-2">
                                                    <button class="text-gray-500 hover:text-green-500 transition-colors duration-200 p-1.5 rounded-md hover:bg-green-50">
                                                        <i class="far fa-thumbs-up"></i>
                                                    </button>
                                                    <button class="text-gray-500 hover:text-red-500 transition-colors duration-200 p-1.5 rounded-md hover:bg-red-50">
                                                        <i class="far fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-8">
                                        <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">暂无评论</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 网站统计 -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        <i class="fas fa-chart-line"></i>
                        网站统计
                    </h3>
                </div>
                
                <div class="admin-card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- 访问统计 -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3">访问统计</h4>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1.5">
                                        <span class="font-medium">今日访问</span>
                                        <span class="font-bold">123</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 65%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1.5">
                                        <span class="font-medium">本月访问</span>
                                        <span class="font-bold">3,456</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 82%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1.5">
                                        <span class="font-medium">总访问量</span>
                                        <span class="font-bold">45,678</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 流量来源 -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3">流量来源</h4>
                            <div class="space-y-3">
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1.5">
                                        <span class="font-medium">直接访问</span>
                                        <span class="font-bold">45%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 45%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1.5">
                                        <span class="font-medium">搜索引擎</span>
                                        <span class="font-bold">30%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 30%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1.5">
                                        <span class="font-medium">社交媒体</span>
                                        <span class="font-bold">25%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 25%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 热门分类 -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3">热门分类</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-pink-500"></span>
                                        动漫资讯
                                    </span>
                                    <span class="font-bold">42%</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                                        创作分享
                                    </span>
                                    <span class="font-bold">28%</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                                        漫评
                                    </span>
                                    <span class="font-bold">30%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php
// 包含公共尾部
require_once 'footer.php';
