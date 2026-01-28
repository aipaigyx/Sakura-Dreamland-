    </main>
    
    <!-- 回到顶部按钮 -->
    <button id="scroll-to-top" class="opacity-0 invisible" aria-label="回到顶部">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- 页脚 -->
    <?php $settings = get_settings(); ?>
    <footer class="bg-gradient-to-t from-pink-50/90 to-white/60 backdrop-blur-lg border-t border-pink-100 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- 关于我们 -->
                <div class="fade-in">
                    <h3 class="text-xl font-bold mb-5 bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 flex items-center">
                        <i class="fas fa-heart text-pink-500 mr-2"></i> <?php echo $settings['footer_about_title'] ?? '关于樱花梦境'; ?>
                    </h3>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        <?php echo $settings['footer_about_description'] ?? '樱花梦境是一个专注于二次元文化的博客网站，提供文章分享、图片画廊和角色生成器等功能。在这里，你可以发现更多精彩的二次元内容，分享你的创作与感悟。'; ?>
                    </p>
                    <div class="flex space-x-5">
                        <?php if (!empty($settings['footer_twitter_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['footer_twitter_url']); ?>" class="social-icon bg-gradient-to-br from-pink-400 to-purple-500 hover:scale-110 transition-all duration-300" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_instagram_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['footer_instagram_url']); ?>" class="social-icon bg-gradient-to-br from-pink-400 to-purple-500 hover:scale-110 transition-all duration-300" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_github_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['footer_github_url']); ?>" class="social-icon bg-gradient-to-br from-pink-400 to-purple-500 hover:scale-110 transition-all duration-300" target="_blank">
                            <i class="fab fa-github"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['footer_bilibili_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['footer_bilibili_url']); ?>" class="social-icon bg-gradient-to-br from-pink-400 to-purple-500 hover:scale-110 transition-all duration-300" target="_blank">
                            <i class="fab fa-bilibili"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- 快速链接 -->
                <div class="fade-in">
                    <h3 class="text-xl font-bold mb-5 bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 flex items-center">
                        <i class="fas fa-link text-pink-500 mr-2"></i> 快速链接
                    </h3>
                    <ul class="space-y-3">
                        <li><a href="/" class="footer-link hover:text-pink-500 transition-all duration-300">首页</a></li>
                        <li><a href="/articles.php" class="footer-link hover:text-pink-500 transition-all duration-300">文章列表</a></li>
                        <li><a href="/gallery.php" class="footer-link hover:text-pink-500 transition-all duration-300">图片画廊</a></li>
                        <li><a href="/character.php" class="footer-link hover:text-pink-500 transition-all duration-300">角色生成器</a></li>
                        <li><a href="#" class="footer-link hover:text-pink-500 transition-all duration-300">关于我们</a></li>
                    </ul>
                </div>
                
                <!-- 热门分类 -->
                <div class="fade-in">
                    <h3 class="text-xl font-bold mb-5 bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 flex items-center">
                        <i class="fas fa-tags text-pink-500 mr-2"></i> 热门分类
                    </h3>
                    <ul class="space-y-3">
                        <li><a href="/articles.php?category=1" class="footer-link hover:text-pink-500 transition-all duration-300">动漫资讯</a></li>
                        <li><a href="/articles.php?category=2" class="footer-link hover:text-pink-500 transition-all duration-300">创作分享</a></li>
                        <li><a href="/articles.php?category=3" class="footer-link hover:text-pink-500 transition-all duration-300">漫评</a></li>
                        <li><a href="/articles.php?category=4" class="footer-link hover:text-pink-500 transition-all duration-300">教程</a></li>
                        <li><a href="/articles.php?category=5" class="footer-link hover:text-pink-500 transition-all duration-300">杂谈</a></li>
                    </ul>
                </div>
                
                <!-- 联系我们 -->
                <div class="fade-in">
                    <h3 class="text-xl font-bold mb-5 bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 flex items-center">
                        <i class="fas fa-envelope text-pink-500 mr-2"></i> 联系我们
                    </h3>
                    <ul class="space-y-4 text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-pink-500 mr-3 mt-1"></i>
                            <span><?php echo $settings['footer_email'] ?? 'contact@sakuradream.com'; ?></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-pink-500 mr-3 mt-1"></i>
                            <span><?php echo $settings['footer_address'] ?? '二次元世界，樱花街道123号'; ?></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock text-pink-500 mr-3 mt-1"></i>
                            <span><?php echo $settings['footer_business_hours'] ?? '周一至周日 9:00 - 22:00'; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- 版权信息 -->
            <div class="mt-12 pt-8 border-t border-pink-100 text-center text-gray-500">
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <p class="bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500">&copy; <?php echo date('Y'); ?> <?php echo $settings['footer_copyright_text'] ?? '樱花梦境. 保留所有权利.'; ?></p>
                    <div class="flex space-x-6">
                    <a href="<?php echo $settings['footer_usage_guide_url'] ?? '/使用说明.md'; ?>" class="text-sm hover:text-pink-500 transition-colors duration-200">使用说明</a>
                    <a href="<?php echo $settings['footer_privacy_policy_url'] ?? '/privacy'; ?>" class="text-sm hover:text-pink-500 transition-colors duration-200">隐私政策</a>
                    <a href="<?php echo $settings['footer_terms_of_service_url'] ?? '/terms'; ?>" class="text-sm hover:text-pink-500 transition-colors duration-200">使用条款</a>
                    <a href="<?php echo $settings['footer_sitemap_url'] ?? '/sitemap'; ?>" class="text-sm hover:text-pink-500 transition-colors duration-200">网站地图</a>
                </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <?php aniblog_enqueue_scripts(); ?>
    <script>
        // 移动端菜单切换
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        });
        
        // 平滑滚动
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // 图片懒加载
        const lazyImages = document.querySelectorAll('img[data-src]');
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const image = entry.target;
                        image.src = image.dataset.src;
                        image.removeAttribute('data-src');
                        imageObserver.unobserve(image);
                    }
                });
            });
            
            lazyImages.forEach(image => {
                imageObserver.observe(image);
            });
        }
        
        // 页面加载动画
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            fadeElements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        });
        
        // 滚动时显示/隐藏回到顶部按钮
        const scrollToTopButton = document.getElementById('scroll-to-top');
        if (scrollToTopButton) {
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    scrollToTopButton.classList.remove('opacity-0', 'invisible');
                } else {
                    scrollToTopButton.classList.add('opacity-0', 'invisible');
                }
            });
            
            scrollToTopButton.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        // 文章卡片悬停效果
        const articleCards = document.querySelectorAll('.modern-card');
        articleCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
    <style>
        /* 社交图标样式 */
        .social-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
        }
        
        /* 页脚链接样式 */
        .footer-link {
            text-decoration: none;
            color: gray;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 8px;
        }
        
        .footer-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #ec4899, #a855f7);
            transition: width 0.3s ease;
        }
        
        .footer-link:hover::before {
            width: 5px;
        }
        
        /* 淡入动画 */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        /* 回到顶部按钮 */
        #scroll-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ec4899, #a855f7);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        #scroll-to-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.5);
        }
        
        /* 渐变背景 */
        .bg-gradient-hero {
            background: linear-gradient(135deg, #fef2f2 0%, #fdf4f4 100%);
        }
        
        /* 按钮样式 */
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #ec4899, #a855f7);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
            opacity: 1;
        }
        
        .btn-outline {
            display: inline-block;
            background: white;
            color: #ec4899;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.15);
            border: 2px solid #ec4899;
            cursor: pointer;
        }
        
        .btn-outline:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.25);
            background: rgba(236, 72, 153, 0.05);
        }
        
        /* 文章卡片动画 */
        .modern-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        /* 标题样式 */
        .section-title {
            position: relative;
            display: inline-block;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #ec4899, #a855f7);
            border-radius: 2px;
        }
    </style>
</body>
</html>
