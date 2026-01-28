<?php
/**
 * 画廊页面模板
 */

// 加载功能函数
require_once __DIR__ . '/functions.php';

// 获取画廊图片列表
$gallery_images = aniblog_get_gallery_images(8);

// 加载页面模板
include __DIR__ . '/header.php';
?>
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <!-- 页面标题 -->
        <section class="mb-12">
            <div class="modern-card p-8 text-center bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500">
                    ✨ 图片画廊 ✨
                </h1>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                    探索二次元世界的精美图片，分享你的创作与发现
                </p>
            </div>
        </section>
        
        <!-- 画廊筛选 -->
        <section class="mb-8">
            <div class="modern-card p-4 flex flex-wrap items-center justify-center gap-2 bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
                <button class="filter-btn px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-full text-sm font-medium hover:from-pink-600 hover:to-purple-600 transition-all duration-300 shadow-sm" data-category="all">
                    全部
                </button>
                <?php 
                    $categories = aniblog_get_categories();
                    foreach ($categories as $category): 
                ?>
                    <button class="filter-btn px-4 py-2 bg-white/80 text-gray-700 rounded-full text-sm font-medium hover:bg-pink-100 transition-colors duration-200 shadow-sm" data-category="<?php echo $category['id']; ?>">
                        <?php echo $category['name']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>
        
        <!-- 画廊图片网格 -->
        <section class="mb-12">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php if (!empty($gallery_images)): ?>
                    <?php foreach ($gallery_images as $image): ?>
                        <div class="modern-card overflow-hidden hover:scale-105 transition-transform duration-300 group bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-2xl">
                            <div class="h-48 bg-gradient-to-r from-pink-300 to-purple-300 flex items-center justify-center relative overflow-hidden rounded-t-xl">
                                <img src="<?php echo $image['file_path']; ?>" alt="<?php echo $image['title']; ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-4">
                                    <button class="px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg text-sm font-medium hover:from-pink-600 hover:to-purple-600 transition-colors duration-200 shadow-md">
                                        <i class="fas fa-search-plus mr-2"></i>查看详情
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-1"><?php echo $image['title']; ?></h3>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="bg-pink-100 text-pink-600 px-2 py-0.5 rounded-full">插画</span>
                                    <span class="flex items-center gap-1 text-pink-500"><i class="far fa-heart"></i> <?php echo rand(50, 200); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- 默认画廊图片 -->
                    <div class="modern-card overflow-hidden hover:scale-105 transition-transform duration-300 group bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-2xl">
                        <div class="h-48 bg-gradient-to-r from-pink-300 to-purple-300 flex items-center justify-center relative overflow-hidden rounded-t-xl">
                            <i class="fas fa-image text-2xl text-white opacity-70"></i>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-4">
                                <button class="px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg text-sm font-medium hover:from-pink-600 hover:to-purple-600 transition-colors duration-200 shadow-md">
                                    <i class="fas fa-search-plus mr-2"></i>查看详情
                                </button>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-1">暂无图片</h3>
                            <div class="flex items-center justify-between text-xs">
                                <span class="bg-pink-100 text-pink-600 px-2 py-0.5 rounded-full">插画</span>
                                <span class="flex items-center gap-1 text-pink-500"><i class="far fa-heart"></i> 0</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <!-- 分页 -->
        <section class="text-center">
            <nav class="inline-flex items-center space-x-2">
                <button class="px-4 py-2 bg-white/80 text-gray-700 rounded-lg border-2 border-pink-300 hover:bg-pink-100 transition-colors duration-200 shadow-sm">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg hover:from-pink-600 hover:to-purple-600 transition-colors duration-200 shadow-md">
                    1
                </button>
                <button class="px-4 py-2 bg-white/80 text-gray-700 rounded-lg border-2 border-pink-300 hover:bg-pink-100 transition-colors duration-200 shadow-sm">
                    2
                </button>
                <button class="px-4 py-2 bg-white/80 text-gray-700 rounded-lg border-2 border-pink-300 hover:bg-pink-100 transition-colors duration-200 shadow-sm">
                    3
                </button>
                <button class="px-4 py-2 bg-white/80 text-gray-700 rounded-lg border-2 border-pink-300 hover:bg-pink-100 transition-colors duration-200 shadow-sm">
                    ...
                </button>
                <button class="px-4 py-2 bg-white/80 text-gray-700 rounded-lg border-2 border-pink-300 hover:bg-pink-100 transition-colors duration-200 shadow-sm">
                    10
                </button>
                <button class="px-4 py-2 bg-white/80 text-gray-700 rounded-lg border-2 border-pink-300 hover:bg-pink-100 transition-colors duration-200 shadow-sm">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </nav>
        </section>
    </main>

    <!-- 图片筛选功能 -->
    <script>
        // 图片筛选功能
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.filter-btn');
            const galleryGrid = document.querySelector('.grid');
            const originalContent = galleryGrid.innerHTML;
            
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // 更新按钮样式
                    filterBtns.forEach(b => {
                        b.classList.remove('bg-gradient-to-r', 'from-pink-500', 'to-purple-500', 'text-white');
                        b.classList.add('bg-white/80', 'text-gray-700');
                    });
                    this.classList.add('bg-gradient-to-r', 'from-pink-500', 'to-purple-500', 'text-white');
                    this.classList.remove('bg-white/80', 'text-gray-700');
                    
                    const category = this.getAttribute('data-category');
                    
                    // 显示加载状态
                    galleryGrid.innerHTML = '<div class="col-span-full text-center py-12"><i class="fas fa-spinner fa-spin text-4xl text-pink-500 mb-4"></i><p class="text-gray-600">加载中...</p></div>';
                    
                    // 发送筛选请求
                    fetch('/api.php?action=get_filtered_images&category=' + category)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data) {
                                // 更新画廊内容
                                let newContent = '';
                                if (data.data.length > 0) {
                                    data.data.forEach(image => {
                                        newContent += `
                                            <div class="modern-card overflow-hidden hover:scale-105 transition-transform duration-300 group bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-2xl">
                                                <div class="h-48 bg-gradient-to-r from-pink-300 to-purple-300 flex items-center justify-center relative overflow-hidden rounded-t-xl">
                                                    <img src="${image.file_path}" alt="${image.title}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center p-4">
                                                        <button class="px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg text-sm font-medium hover:from-pink-600 hover:to-purple-600 transition-colors duration-200 shadow-md">
                                                            <i class="fas fa-search-plus mr-2"></i>查看详情
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="p-4">
                                                    <h3 class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-1">${image.title}</h3>
                                                    <div class="flex items-center justify-between text-xs">
                                                        <span class="bg-pink-100 text-pink-600 px-2 py-0.5 rounded-full">${image.category_name || '未分类'}</span>
                                                        <span class="flex items-center gap-1 text-pink-500"><i class="far fa-heart"></i> ${image.likes || 0}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    });
                                } else {
                                    newContent = `
                                        <div class="col-span-full text-center py-12">
                                            <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-gray-500">该分类下暂无图片</p>
                                        </div>
                                    `;
                                }
                                galleryGrid.innerHTML = newContent;
                            } else {
                                // 恢复原始内容
                                galleryGrid.innerHTML = originalContent;
                                alert('筛选失败，请稍后重试');
                            }
                        })
                        .catch(error => {
                            console.error('筛选请求失败:', error);
                            galleryGrid.innerHTML = originalContent;
                            alert('筛选失败，请稍后重试');
                        });
                });
            });
        });
    </script>
<?php
// 加载页脚
include __DIR__ . '/footer.php';

