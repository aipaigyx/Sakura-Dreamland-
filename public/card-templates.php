<?php
/**
 * 首页卡片模板文件
 */

// 文章卡片模板
function render_article_card($card) {
    $settings = json_decode($card['settings'] ?? '{}', true);
    $count = $settings['count'] ?? 3;
    $category_id = $settings['category_id'] ?? null;
    $layout = $settings['layout'] ?? 'auto';
    $responsive = $settings['responsive'] ?? 'auto';
    $image_height = $settings['image_height'] ?? 200;
    $style = $settings['style'] ?? 'style3';
    $show_meta = $settings['show_meta'] ?? 1;
    $show_summary = $settings['show_summary'] ?? 0;
    $hover_effect = $settings['hover_effect'] ?? 'scale';
    $width = $settings['width'] ?? 100;
    $margin = $settings['margin'] ?? 8;
    $padding = $settings['padding'] ?? 12;
    
    // 直接使用设置的图片高度
    $image_style = "height: {$image_height}px; overflow: hidden;";
    
    // 生成悬停效果类
    $hover_class = '';
    switch ($hover_effect) {
        case 'scale':
            $hover_class = 'hover:scale-105 transition-transform duration-300';
            break;
        case 'fade':
            $hover_class = 'hover:opacity-90 transition-opacity duration-300';
            break;
        case 'slide':
            $hover_class = 'hover:-translate-y-1 transition-transform duration-300';
            break;
        case 'rotate':
            $hover_class = 'hover:rotate-1 transition-transform duration-300';
            break;
        case 'none':
        default:
            $hover_class = '';
            break;
    }
    
    // 生成网格类
    $grid_classes = 'grid ';
    if ($layout === 'horizontal') {
        $grid_classes .= 'grid-cols-1 md:grid-cols-3 lg:grid-cols-4 ';
    } elseif ($layout === 'vertical') {
        $grid_classes .= 'grid-cols-1 ';
    } else {
        // 自动适应
        $grid_classes .= 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 ';
    }
    
    // 使用设置的间距
    $grid_classes .= "gap-{$margin}";
    
    // 生成卡片容器样式
    $container_style = "width: {$width}%; margin: {$margin}px auto; padding: {$padding}px;";
    
    // 获取文章数据
    $sql = "SELECT * FROM articles";
    $params = [];
    
    if ($category_id) {
        $sql .= " WHERE category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ?";
    $params[] = $count;
    
    $articles = db_query($sql, $params);
    
    global $card_border_radius, $card_shadow, $card_hover_effect;
    
    ob_start();
    ?>
    <section class="mb-20" style="<?php echo $container_style; ?>">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10">
            <h2 class="section-title text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500"><?php echo $card['title']; ?></h2>
            <a href="/article" class="btn-primary mt-4 md:mt-0">
                查看全部 <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="<?php echo $grid_classes; ?>">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                    <posts class="posts-item card ajax-item <?php echo $style; ?> <?php echo $hover_class; ?>">
                        <div class="item-thumbnail" style="<?php echo $image_style; ?>">
                            <a href="/article.php?id=<?php echo $article['id']; ?>">
                                <img src="<?php echo !empty($article['cover_image']) ? $article['cover_image'] : 'https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=400&h=225&fit=crop&crop=center'; ?>" alt="<?php echo $article['title']; ?>" class="w-full h-full object-cover radius8">
                            </a>
                            <badge class="img-badge left jb-red"><?php echo $article['category_id'] ? aniblog_get_categories()[$article['category_id'] - 1]['name'] : '未分类'; ?></badge>
                        </div>
                        <div class="item-body">
                            <h2 class="item-heading line-clamp-2">
                                <a href="/article.php?id=<?php echo $article['id']; ?>" class="hover:text-pink-500 transition-colors duration-200"><?php echo $article['title']; ?></a>
                            </h2>
                            <!-- 移除了重复的分类标签，因为图片上已有分类徽章 -->
                            <?php if ($show_summary): ?>
                                <div class="item-summary text-gray-600 mb-3 line-clamp-2">
                                    <?php echo $article['summary'] ? $article['summary'] : substr(strip_tags($article['content']), 0, 100) . '...'; ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($show_meta): ?>
                                <div class="item-meta muted-2-color flex jsb ac text-sm">
                                    <item class="meta-author flex ac">
                                        <span class="avatar-mini">
                                            <img alt="作者头像" src="https://i.pravatar.cc/150?img=32" class="avatar" style="width: 20px; height: 20px;">
                                        </span>
                                        <span class="ml-2">管理员</span>
                                    </item>
                                    <div class="meta-right flex gap-3">
                                        <item class="meta-comm">
                                            <a rel="nofollow" href="/article.php?id=<?php echo $article['id']; ?>#comments" class="flex items-center gap-1">
                                                <i class="far fa-comment text-xs"></i><?php echo $article['comment_count'] ?? 0; ?>
                                            </a>
                                        </item>
                                        <item class="meta-view">
                                            <span class="flex items-center gap-1">
                                                <i class="far fa-eye text-xs"></i><?php echo $article['view_count']; ?>
                                            </span>
                                        </item>
                                        <item class="meta-like">
                                            <span class="flex items-center gap-1">
                                                <i class="far fa-heart text-xs"></i><?php echo $article['like_count'] ?? 0; ?>
                                            </span>
                                        </item>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </posts>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- 默认文章卡片 -->
                <posts class="posts-item card ajax-item <?php echo $style; ?> <?php echo $hover_class; ?>">
                    <div class="item-thumbnail" style="<?php echo $image_style; ?>">
                        <a href="/article.php?id=1">
                            <img src="https://images.unsplash.com/photo-1511919884226-fd3cad34687c?w=400&h=225&fit=crop&crop=center" alt="2025年春季新番推荐" class="w-full h-full object-cover radius8">
                        </a>
                        <badge class="img-badge left jb-red">动漫资讯</badge>
                    </div>
                    <div class="item-body">
                        <h2 class="item-heading line-clamp-2">
                            <a href="/article.php?id=1" class="hover:text-pink-500 transition-colors duration-200">2025年春季新番推荐</a>
                        </h2>
                        <!-- 移除了重复的分类标签，因为图片上已有分类徽章 -->
                        <?php if ($show_summary): ?>
                            <div class="item-summary text-gray-600 mb-3 line-clamp-2">
                                2025年春季新番即将开播，本文为大家推荐几部值得期待的作品，涵盖各种题材类型。
                            </div>
                        <?php endif; ?>
                        <?php if ($show_meta): ?>
                            <div class="item-meta muted-2-color flex jsb ac text-sm">
                                <item class="meta-author flex ac">
                                    <span class="avatar-mini">
                                        <img alt="作者头像" src="https://i.pravatar.cc/150?img=32" class="avatar" style="width: 20px; height: 20px;">
                                    </span>
                                    <span class="ml-2">管理员</span>
                                </item>
                                <div class="meta-right flex gap-3">
                                    <item class="meta-comm">
                                        <a rel="nofollow" href="/article.php?id=1#comments" class="flex items-center gap-1">
                                            <i class="far fa-comment text-xs"></i>0
                                        </a>
                                    </item>
                                    <item class="meta-view">
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-eye text-xs"></i>0
                                        </span>
                                    </item>
                                    <item class="meta-like">
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-heart text-xs"></i>0
                                        </span>
                                    </item>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </posts>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// 画廊卡片模板
function render_gallery_card($card) {
    $settings = json_decode($card['settings'] ?? '{}', true);
    $count = $settings['count'] ?? 4;
    $category_id = $settings['category_id'] ?? null;
    $layout = $settings['layout'] ?? 'auto';
    $responsive = $settings['responsive'] ?? 'auto';
    $width = $settings['width'] ?? 100;
    $margin = $settings['margin'] ?? 8;
    $padding = $settings['padding'] ?? 12;
    $style = $settings['style'] ?? 'modern'; // 默认样式为modern，新增style选项
    
    // 生成网格类
    $grid_classes = 'grid ';
    if ($layout === 'horizontal') {
        $grid_classes .= 'grid-cols-2 md:grid-cols-4 lg:grid-cols-6 ';
    } elseif ($layout === 'vertical') {
        $grid_classes .= 'grid-cols-2 ';
    } else {
        // 自动适应
        $grid_classes .= 'grid-cols-2 md:grid-cols-4 ';
    }
    
    // 使用设置的间距
    $grid_classes .= "gap-{$margin}";
    
    // 生成卡片容器样式
    $container_style = "width: {$width}%; margin: {$margin}px auto; padding: {$padding}px;";
    
    // 获取画廊数据
    $sql = "SELECT * FROM images";
    $params = [];
    
    if ($category_id) {
        $sql .= " WHERE category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT ?";
    $params[] = $count;
    
    $gallery_images = db_query($sql, $params);
    
    global $card_border_radius, $card_shadow, $card_hover_effect;
    
    ob_start();
    ?>
    <section class="mb-20" style="<?php echo $container_style; ?>">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10">
            <h2 class="section-title text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500"><?php echo $card['title']; ?></h2>
            <a href="/gallery" class="btn-primary mt-4 md:mt-0">
                查看全部 <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="<?php echo $grid_classes; ?>">
            <?php if (!empty($gallery_images)): ?>
                <?php foreach ($gallery_images as $image): ?>
                    <?php if ($style === 'anime'): ?>
                        <!-- 二次元风格画廊卡片 -->
                        <div class="relative group anime-gallery-card overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500">
                            <div class="aspect-square overflow-hidden">
                                <img src="<?php echo $image['file_path']; ?>" alt="<?php echo $image['title']; ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-125">
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-t from-pink-900/80 via-purple-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-4">
                                <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                    <h3 class="text-white font-bold text-lg mb-1 bg-pink-500/80 backdrop-blur-sm px-3 py-1 rounded-full inline-block"><?php echo $image['title']; ?></h3>
                                    <p class="text-pink-100 text-sm bg-purple-500/60 backdrop-blur-sm px-3 py-1 rounded-full inline-block"><?php echo $image['description'] ?? ''; ?></p>
                                </div>
                                <div class="mt-3 flex justify-end transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700 delay-100">
                                    <button class="bg-white/20 backdrop-blur-sm hover:bg-white/40 text-white rounded-full p-2 transition-colors duration-300">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- 装饰元素 -->
                            <div class="absolute top-2 right-2 w-12 h-12 bg-pink-500/30 backdrop-blur-sm rounded-full flex items-center justify-center transform rotate-45 scale-0 group-hover:scale-100 transition-all duration-500">
                                <i class="fas fa-star text-white"></i>
                            </div>
                            <div class="absolute bottom-2 left-2 w-8 h-8 bg-purple-500/30 backdrop-blur-sm rounded-full flex items-center justify-center transform -rotate-45 scale-0 group-hover:scale-100 transition-all duration-500 delay-100">
                                <i class="fas fa-moon text-white"></i>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- 现代风格画廊卡片 -->
                        <div class="modern-card overflow-hidden group rounded-<?php echo $card_border_radius; ?> border-2 border-transparent hover:border-pink-300 shadow-<?php echo $card_shadow; ?><?php echo $card_hover_effect ? ' hover:scale-105 transition-transform duration-300' : ''; ?>>
                            <div class="h-56 relative rounded-<?php echo $card_border_radius; ?> overflow-hidden">
                                <img src="<?php echo $image['file_path']; ?>" alt="<?php echo $image['title']; ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-4">
                                    <p class="text-white font-medium text-sm bg-gradient-to-r from-pink-300 to-purple-300 px-3 py-1 rounded-full backdrop-blur-sm"><?php echo $image['title']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- 默认画廊图片 -->
                <?php if ($style === 'anime'): ?>
                    <!-- 二次元风格默认图片 -->
                    <div class="relative group anime-gallery-card overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500">
                        <div class="aspect-square overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1586525198429-2412ee2b5e9b?w=200&h=225&fit=crop&crop=center" alt="樱花飘落" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-125">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-pink-900/80 via-purple-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-4">
                            <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                                <h3 class="text-white font-bold text-lg mb-1 bg-pink-500/80 backdrop-blur-sm px-3 py-1 rounded-full inline-block">樱花飘落</h3>
                                <p class="text-pink-100 text-sm bg-purple-500/60 backdrop-blur-sm px-3 py-1 rounded-full inline-block">二次元风格画廊</p>
                            </div>
                            <div class="mt-3 flex justify-end transform translate-y-4 group-hover:translate-y-0 transition-transform duration-700 delay-100">
                                <button class="bg-white/20 backdrop-blur-sm hover:bg-white/40 text-white rounded-full p-2 transition-colors duration-300">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                        <!-- 装饰元素 -->
                        <div class="absolute top-2 right-2 w-12 h-12 bg-pink-500/30 backdrop-blur-sm rounded-full flex items-center justify-center transform rotate-45 scale-0 group-hover:scale-100 transition-all duration-500">
                            <i class="fas fa-star text-white"></i>
                        </div>
                        <div class="absolute bottom-2 left-2 w-8 h-8 bg-purple-500/30 backdrop-blur-sm rounded-full flex items-center justify-center transform -rotate-45 scale-0 group-hover:scale-100 transition-all duration-500 delay-100">
                            <i class="fas fa-moon text-white"></i>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- 现代风格默认图片 -->
                    <div class="modern-card overflow-hidden hover:scale-105 transition-transform duration-300 group rounded-2xl border-2 border-transparent hover:border-pink-300">
                        <div class="h-56 relative rounded-2xl overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1586525198429-2412ee2b5e9b?w=200&h=225&fit=crop&crop=center" alt="樱花飘落" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-4">
                                <p class="text-white font-medium text-sm bg-gradient-to-r from-pink-300 to-purple-300 px-3 py-1 rounded-full backdrop-blur-sm">樱花飘落</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// 分类卡片模板
function render_category_card($card) {
    global $card_border_radius, $card_shadow, $card_hover_effect;
    
    // 获取分类数据
    $categories = aniblog_get_categories();
    
    ob_start();
    ?>
    <div class="modern-card p-6 fade-in bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-<?php echo $card_border_radius; ?> shadow-<?php echo $card_shadow; ?><?php echo $card_hover_effect ? ' hover:scale-105 transition-transform duration-300' : ''; ?>>
        <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4 flex items-center">
            <i class="fas fa-tags text-pink-500 mr-2"></i> <?php echo $card['title']; ?>
        </h3>
        <ul class="space-y-3">
            <?php foreach ($categories as $category): ?>
                <li>
                    <a href="/articles.php?category=<?php echo $category['id']; ?>" class="flex items-center justify-between p-3 bg-white/80 rounded-lg hover:bg-pink-100 transition-colors duration-200 shadow-sm hover:shadow-md">
                        <span class="text-gray-800 hover:text-pink-500 font-medium"><?php echo $category['name']; ?></span>
                        <span class="bg-gradient-to-r from-pink-400 to-purple-400 text-white px-2 py-0.5 rounded-full text-xs font-medium shadow-sm">
                            <?php echo rand(10, 100); ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return ob_get_clean();
}

// 统计卡片模板
function render_stats_card($card) {
    global $card_border_radius, $card_shadow, $card_hover_effect;
    
    ob_start();
    ?>
    <div class="modern-card p-6 fade-in bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50 border-2 border-pink-200 rounded-<?php echo $card_border_radius; ?> shadow-<?php echo $card_shadow; ?><?php echo $card_hover_effect ? ' hover:scale-105 transition-transform duration-300' : ''; ?>>
        <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4 flex items-center">
            <i class="fas fa-chart-line text-pink-500 mr-2"></i> <?php echo $card['title']; ?>
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div>
                    <div class="text-sm text-gray-600">文章总数</div>
                    <div class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-500">128</div>
                </div>
                <i class="fas fa-newspaper text-pink-400 text-xl"></i>
            </div>
            <div class="flex items-center justify-between p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div>
                    <div class="text-sm text-gray-600">图片数量</div>
                    <div class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-500">456</div>
                </div>
                <i class="fas fa-image text-purple-400 text-xl"></i>
            </div>
            <div class="flex items-center justify-between p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div>
                    <div class="text-sm text-gray-600">总浏览量</div>
                    <div class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-500">7,890</div>
                </div>
                <i class="fas fa-eye text-blue-400 text-xl"></i>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 公告卡片模板
function render_announcement_card($card) {
    global $card_border_radius, $card_shadow, $card_hover_effect;
    
    ob_start();
    ?>
    <div class="modern-card p-6 fade-in bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-<?php echo $card_border_radius; ?> shadow-<?php echo $card_shadow; ?><?php echo $card_hover_effect ? ' hover:scale-105 transition-transform duration-300' : ''; ?>>
        <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4 flex items-center">
            <i class="fas fa-bullhorn text-pink-500 mr-2"></i> <?php echo $card['title']; ?>
        </h3>
        
        <div class="space-y-4">
            <div class="p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="font-medium text-gray-800 text-sm">网站全新改版上线！</div>
                <div class="text-xs text-pink-500 mt-1">2026-01-10</div>
                <div class="text-xs text-gray-600 mt-2 line-clamp-2">
                    樱花梦境网站全新改版，带来更好的用户体验和更多精彩功能！
                </div>
            </div>
            <div class="p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="font-medium text-gray-800 text-sm">角色生成器功能更新</div>
                <div class="text-xs text-pink-500 mt-1">2026-01-08</div>
                <div class="text-xs text-gray-600 mt-2 line-clamp-2">
                    角色生成器增加了新的发型、服装和配饰选项，快来试试吧！
                </div>
            </div>
            <div class="p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <div class="font-medium text-gray-800 text-sm">画廊功能正式开放</div>
                <div class="text-xs text-pink-500 mt-1">2026-01-05</div>
                <div class="text-xs text-gray-600 mt-2 line-clamp-2">
                    图片画廊功能正式开放，欢迎大家上传分享自己的作品！
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 链接卡片模板
function render_link_card($card) {
    global $card_border_radius, $card_shadow, $card_hover_effect;
    
    ob_start();
    ?>
    <div class="modern-card p-6 fade-in bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-<?php echo $card_border_radius; ?> shadow-<?php echo $card_shadow; ?><?php echo $card_hover_effect ? ' hover:scale-105 transition-transform duration-300' : ''; ?>>
        <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4 flex items-center">
            <i class="fas fa-link text-pink-500 mr-2"></i> <?php echo $card['title']; ?>
        </h3>
        
        <div class="grid grid-cols-2 gap-2">
            <a href="/article" class="flex items-center p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:bg-pink-100">
                <i class="fas fa-newspaper text-pink-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-800">文章</span>
            </a>
            <a href="/gallery" class="flex items-center p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:bg-pink-100">
                <i class="fas fa-image text-purple-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-800">画廊</span>
            </a>
            <a href="/character-generator" class="flex items-center p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:bg-pink-100">
                <i class="fas fa-magic text-blue-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-800">角色生成器</span>
            </a>
            <a href="/about" class="flex items-center p-3 bg-white/80 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 hover:bg-pink-100">
                <i class="fas fa-info-circle text-pink-500 mr-2"></i>
                <span class="text-sm font-medium text-gray-800">关于我们</span>
            </a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 用户卡片模板
function render_user_card($card) {
    global $card_border_radius, $card_shadow, $card_hover_effect;
    
    ob_start();
    ?>
    <div class="modern-card p-6 fade-in bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50 border-2 border-pink-200 rounded-<?php echo $card_border_radius; ?> shadow-<?php echo $card_shadow; ?><?php echo $card_hover_effect ? ' hover:scale-105 transition-transform duration-300' : ''; ?>>
        <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4 flex items-center">
            <i class="fas fa-users text-pink-500 mr-2"></i> <?php echo $card['title']; ?>
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- 用户卡片 1 - 二次元创作者 -->
            <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-lg shadow-sm p-4 hover:shadow-md transition-all duration-300 border border-pink-100 hover:border-pink-300">
                <div class="flex items-center">
                    <img src="https://i.pravatar.cc/150?img=32" alt="樱花酱" class="w-14 h-14 rounded-full object-cover border-3 border-pink-300 shadow-md mr-4 transition-transform hover:scale-110 duration-300">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">樱花酱</h4>
                        <p class="text-pink-600 text-xs font-medium">✨ 二次元创作者</p>
                        <div class="flex items-center mt-1">
                            <i class="fa fa-star text-yellow-400 text-xs"></i>
                            <i class="fa fa-star text-yellow-400 text-xs"></i>
                            <i class="fa fa-star text-yellow-400 text-xs"></i>
                            <i class="fa fa-star text-yellow-400 text-xs"></i>
                            <i class="fa fa-star-half-o text-yellow-400 text-xs"></i>
                            <span class="text-xs text-gray-500 ml-1">4.5k</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 用户卡片 2 - 游戏爱好者 -->
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg shadow-sm p-4 hover:shadow-md transition-all duration-300 border border-blue-100 hover:border-blue-300">
                <div class="flex items-center">
                    <img src="https://i.pravatar.cc/150?img=64" alt="蓝星" class="w-14 h-14 rounded-full object-cover border-3 border-blue-300 shadow-md mr-4 transition-transform hover:scale-110 duration-300">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">蓝星</h4>
                        <p class="text-blue-600 text-xs font-medium">🎮 游戏爱好者</p>
                        <div class="flex items-center mt-1">
                            <i class="fa fa-gamepad text-blue-500 text-xs"></i>
                            <span class="text-xs text-gray-500 ml-1">1.2k 游戏</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 用户卡片 3 - 插画师 -->
            <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-lg shadow-sm p-4 hover:shadow-md transition-all duration-300 border border-green-100 hover:border-green-300">
                <div class="flex items-center">
                    <img src="https://i.pravatar.cc/150?img=23" alt="抹茶" class="w-14 h-14 rounded-full object-cover border-3 border-green-300 shadow-md mr-4 transition-transform hover:scale-110 duration-300">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">抹茶</h4>
                        <p class="text-green-600 text-xs font-medium">🖌️ 插画师</p>
                        <div class="flex items-center mt-1">
                            <i class="fa fa-image text-green-500 text-xs"></i>
                            <span class="text-xs text-gray-500 ml-1">567 作品</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 用户卡片 4 - 动画爱好者 -->
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg shadow-sm p-4 hover:shadow-md transition-all duration-300 border border-yellow-100 hover:border-yellow-300">
                <div class="flex items-center">
                    <img src="https://i.pravatar.cc/150?img=45" alt="阳光" class="w-14 h-14 rounded-full object-cover border-3 border-yellow-300 shadow-md mr-4 transition-transform hover:scale-110 duration-300">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">阳光</h4>
                        <p class="text-yellow-600 text-xs font-medium">🎬 动画爱好者</p>
                        <div class="flex items-center mt-1">
                            <i class="fa fa-film text-yellow-500 text-xs"></i>
                            <span class="text-xs text-gray-500 ml-1">892 收藏</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// 幻灯片卡片模板
function render_slider_card($card) {
    $settings = json_decode($card['settings'] ?? '{}', true);
    $count = $settings['count'] ?? 5;
    $category_id = $settings['category_id'] ?? null;
    $width = $settings['width'] ?? 100;
    $margin = $settings['margin'] ?? 8;
    $padding = $settings['padding'] ?? 12;
    $autoplay = $settings['autoplay'] ?? 1;
    $interval = $settings['interval'] ?? 5000;
    $custom_images = $settings['custom_images'] ?? [];
    $use_custom_images = $settings['use_custom_images'] ?? 0;
    
    // 生成卡片容器样式
    $container_style = "width: {$width}%; margin: {$margin}px auto; padding: {$padding}px;";
    
    // 获取图片数据
    $slider_images = [];
    if ($use_custom_images && !empty($custom_images)) {
        // 使用自定义图片
        $slider_images = $custom_images;
    } else {
        // 使用数据库中的图片
        $sql = "SELECT * FROM images";
        $params = [];
        
        if ($category_id) {
            $sql .= " WHERE category_id = ?";
            $params[] = $category_id;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $count;
        
        $slider_images = db_query($sql, $params);
    }
    
    ob_start();
    ?>
    <section class="mb-20 pt-8" style="<?php echo $container_style; ?>">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10">
            <h2 class="section-title text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500"><?php echo $card['title']; ?></h2>
        </div>
        
        <!-- 幻灯片容器 -->
        <div class="relative overflow-hidden rounded-2xl shadow-xl">
            <!-- 从上往下的渐变遮罩 -->
            <div class="absolute top-0 left-0 right-0 h-24 bg-gradient-to-b from-white via-transparent to-transparent z-10 pointer-events-none"></div>
            <!-- 幻灯片 -->
            <div class="slider-container relative" id="slider-<?php echo $card['id']; ?>">
                <div class="slider-wrapper flex transition-transform duration-500 ease-out">
                    <?php if (!empty($slider_images)): ?>
                        <?php foreach ($slider_images as $index => $image): ?>
                            <?php 
                            // 检查是否为自定义图片（自定义图片有image_url字段，数据库图片有file_path字段）
                            $is_custom = isset($image['image_url']);
                            $image_url = $is_custom ? $image['image_url'] : $image['file_path'];
                            $image_title = $image['title'] ?? '';
                            $image_description = $image['description'] ?? '';
                            $image_link = $is_custom ? $image['link'] : '';
                            $has_link = !empty($image_link);
                            // 获取所有图片URL用于灯箱
                            $all_image_urls = array_map(function($img) use ($is_custom) {
                                return $is_custom ? $img['image_url'] : $img['file_path'];
                            }, $slider_images);
                            ?>
                            <div class="slider-item flex-shrink-0 w-full relative cursor-pointer">
                                <?php if ($has_link): ?>
                                    <!-- 有自定义链接，跳转到链接 -->
                                    <a href="<?php echo $image_link; ?>" target="_blank" class="block w-full h-full">
                                        <img src="<?php echo $image_url; ?>" alt="<?php echo $image_title; ?>" class="radius8 swiper-lazy swiper-lazy-loaded lazyloaded w-full h-80 md:h-96 object-cover">
                                        <!-- 链接图标 -->
                                        <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm text-pink-500 p-2 rounded-full shadow-md">
                                            <i class="fas fa-external-link-alt"></i>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <!-- 没有自定义链接，打开灯箱 -->
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo $image_title; ?>" class="radius8 swiper-lazy swiper-lazy-loaded lazyloaded w-full h-80 md:h-96 object-cover" onclick="openLightbox(<?php echo $index; ?>, <?php echo json_encode($all_image_urls); ?>)">
                                <?php endif; ?>
                                <!-- 图片信息 -->
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-6 text-white">
                                    <h3 class="text-xl font-bold mb-2"><?php echo $image_title; ?></h3>
                                    <p class="text-sm opacity-90 line-clamp-2"><?php echo $image_description; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- 默认幻灯片图片 -->
                        <div class="slider-item flex-shrink-0 w-full relative cursor-pointer" onclick="openLightbox(0, ['https://images.unsplash.com/photo-1586525198429-2412ee2b5e9b?w=1200&h=600&fit=crop&crop=center'])">
                            <img src="https://images.unsplash.com/photo-1586525198429-2412ee2b5e9b?w=1200&h=600&fit=crop&crop=center" alt="樱花飘落" class="radius8 swiper-lazy swiper-lazy-loaded lazyloaded w-full h-80 md:h-96 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent p-6 text-white">
                                <h3 class="text-xl font-bold mb-2">樱花飘落</h3>
                                <p class="text-sm opacity-90 line-clamp-2">二次元风格幻灯片展示</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- 导航按钮 -->
                <button class="slider-btn slider-prev absolute top-1/2 left-4 transform -translate-y-1/2 bg-white/20 backdrop-blur-sm text-white p-3 rounded-full hover:bg-white/40 transition-colors duration-300">
                    <i class="fas fa-chevron-left text-xl"></i>
                </button>
                <button class="slider-btn slider-next absolute top-1/2 right-4 transform -translate-y-1/2 bg-white/20 backdrop-blur-sm text-white p-3 rounded-full hover:bg-white/40 transition-colors duration-300">
                    <i class="fas fa-chevron-right text-xl"></i>
                </button>
                
                <!-- 指示器 -->
                <div class="slider-indicators absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
                    <?php if (!empty($slider_images)): ?>
                        <?php foreach ($slider_images as $index => $image): ?>
                            <button class="slider-indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-colors duration-300 <?php echo $index === 0 ? 'bg-white' : ''; ?>" data-index="<?php echo $index; ?>"></button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <button class="slider-indicator w-3 h-3 rounded-full bg-white transition-colors duration-300" data-index="0"></button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <!-- 幻灯片脚本 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sliderId = 'slider-<?php echo $card['id']; ?>';
            const sliderContainer = document.getElementById(sliderId);
            const sliderWrapper = sliderContainer.querySelector('.slider-wrapper');
            const sliderItems = sliderContainer.querySelectorAll('.slider-item');
            const prevBtn = sliderContainer.querySelector('.slider-prev');
            const nextBtn = sliderContainer.querySelector('.slider-next');
            const indicators = sliderContainer.querySelectorAll('.slider-indicator');
            
            let currentIndex = 0;
            let autoplayInterval;
            
            // 设置幻灯片宽度
            const setSliderWidth = () => {
                sliderItems.forEach(item => {
                    item.style.width = `${sliderContainer.offsetWidth}px`;
                });
                updateSliderPosition();
            };
            
            // 更新幻灯片位置
            const updateSliderPosition = () => {
                sliderWrapper.style.transform = `translateX(-${currentIndex * sliderContainer.offsetWidth}px)`;
                
                // 更新指示器
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('bg-white', index === currentIndex);
                    indicator.classList.toggle('bg-white/50', index !== currentIndex);
                });
            };
            
            // 下一张幻灯片
            const nextSlide = () => {
                currentIndex = (currentIndex + 1) % sliderItems.length;
                updateSliderPosition();
            };
            
            // 上一张幻灯片
            const prevSlide = () => {
                currentIndex = (currentIndex - 1 + sliderItems.length) % sliderItems.length;
                updateSliderPosition();
            };
            
            // 跳转到指定幻灯片
            const goToSlide = (index) => {
                currentIndex = index;
                updateSliderPosition();
            };
            
            // 自动播放
            const startAutoplay = () => {
                if (<?php echo $autoplay; ?>) {
                    autoplayInterval = setInterval(nextSlide, <?php echo $interval; ?>);
                }
            };
            
            const stopAutoplay = () => {
                clearInterval(autoplayInterval);
            };
            
            // 事件监听
            prevBtn.addEventListener('click', () => {
                stopAutoplay();
                prevSlide();
                startAutoplay();
            });
            
            nextBtn.addEventListener('click', () => {
                stopAutoplay();
                nextSlide();
                startAutoplay();
            });
            
            indicators.forEach(indicator => {
                indicator.addEventListener('click', () => {
                    stopAutoplay();
                    goToSlide(parseInt(indicator.dataset.index));
                    startAutoplay();
                });
            });
            
            // 鼠标悬停时停止自动播放
            sliderContainer.addEventListener('mouseenter', stopAutoplay);
            sliderContainer.addEventListener('mouseleave', startAutoplay);
            
            // 窗口大小变化时重新设置宽度
            window.addEventListener('resize', setSliderWidth);
            
            // 初始化
            setSliderWidth();
            startAutoplay();
        });
        
        // 灯箱功能
        var lightboxImages = [];
        var lightboxCurrentIndex = 0;
        
        function openLightbox(index, images) {
            lightboxImages = images;
            lightboxCurrentIndex = index;
            
            // 创建灯箱元素
            let lightbox = document.getElementById('lightbox');
            if (!lightbox) {
                lightbox = document.createElement('div');
                lightbox.id = 'lightbox';
                lightbox.className = 'fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4';
                lightbox.innerHTML = `
                    <div class="lightbox-content relative max-w-6xl max-h-full">
                        <img src="" alt="Lightbox image" class="lightbox-image max-w-full max-h-[90vh] object-contain">
                        <button class="lightbox-close absolute top-4 right-4 text-white hover:text-pink-500 text-3xl" onclick="closeLightbox()">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="lightbox-prev absolute top-1/2 left-4 transform -translate-y-1/2 text-white hover:text-pink-500 text-3xl" onclick="prevLightbox()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="lightbox-next absolute top-1/2 right-4 transform -translate-y-1/2 text-white hover:text-pink-500 text-3xl" onclick="nextLightbox()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                `;
                document.body.appendChild(lightbox);
            }
            
            // 更新灯箱图片
            lightbox.querySelector('.lightbox-image').src = lightboxImages[lightboxCurrentIndex];
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            if (lightbox) {
                lightbox.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
        
        function prevLightbox() {
            lightboxCurrentIndex = (lightboxCurrentIndex - 1 + lightboxImages.length) % lightboxImages.length;
            document.querySelector('.lightbox-image').src = lightboxImages[lightboxCurrentIndex];
        }
        
        function nextLightbox() {
            lightboxCurrentIndex = (lightboxCurrentIndex + 1) % lightboxImages.length;
            document.querySelector('.lightbox-image').src = lightboxImages[lightboxCurrentIndex];
        }
        
        // 点击灯箱外部关闭
        document.addEventListener('click', function(e) {
            const lightbox = document.getElementById('lightbox');
            if (lightbox && e.target === lightbox) {
                closeLightbox();
            }
        });
        
        // ESC键关闭灯箱
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>
    <?php
    return ob_get_clean();
}

// 渲染卡片
function render_card($card) {
    $card_type = $card['card_type'] ?? 'articles';
    
    switch ($card_type) {
        case 'article':
        case 'articles':
            return render_article_card($card);
        case 'gallery':
            return render_gallery_card($card);
        case 'category':
        case 'categories':
            return render_category_card($card);
        case 'stats':
            return render_stats_card($card);
        case 'announcement':
        case 'announcements':
            return render_announcement_card($card);
        case 'link':
        case 'links':
            return render_link_card($card);
        case 'user':
        case 'users':
            return render_user_card($card);
        case 'slider':
            return render_slider_card($card);
        default:
            return '<div class="card">未知卡片类型</div>';
    }
}
