// 滚动触发的动画效果
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // 根据元素类名或随机选择动画类型
                const element = entry.target;
                let animationType = 'fade-in';
                
                // 检查元素是否有特定的动画类型类
                if (element.classList.contains('animate-fade-in-up')) {
                    animationType = 'fade-in-up';
                } else if (element.classList.contains('animate-fade-in-down')) {
                    animationType = 'fade-in-down';
                } else if (element.classList.contains('animate-fade-in-left')) {
                    animationType = 'fade-in-left';
                } else if (element.classList.contains('animate-fade-in-right')) {
                    animationType = 'fade-in-right';
                } else if (element.classList.contains('animate-bounce-in')) {
                    animationType = 'bounce-in';
                } else {
                    // 随机选择动画类型
                    const animations = ['fade-in-up', 'fade-in-down', 'fade-in-left', 'fade-in-right', 'bounce-in'];
                    animationType = animations[Math.floor(Math.random() * animations.length)];
                }
                
                // 添加动画类
                element.classList.add(animationType);
                
                // 添加延迟以创建错落有致的效果
                element.style.animationDelay = `${index * 0.1}s`;
                
                observer.unobserve(element);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

// 平滑滚动功能
function initSmoothScroll() {
    // 为锚点链接添加平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// 回到顶部按钮
function initBackToTop() {
    const backToTopBtn = document.createElement('button');
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTopBtn.className = 'back-to-top fixed bottom-6 right-6 p-3 bg-pink-500 text-white rounded-full shadow-lg hover:bg-pink-600 transition-all duration-300 transform hover:scale-110 opacity-0 invisible z-50';
    
    document.body.appendChild(backToTopBtn);
    
    // 显示/隐藏按钮
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            backToTopBtn.classList.remove('opacity-0', 'invisible');
            backToTopBtn.classList.add('opacity-100', 'visible');
        } else {
            backToTopBtn.classList.add('opacity-0', 'invisible');
            backToTopBtn.classList.remove('opacity-100', 'visible');
        }
    });
    
    // 回到顶部功能
    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// 文章点赞功能
function initArticleLikes() {
    const likeButtons = document.querySelectorAll('.article-like-btn');
    
    likeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;
            const likeCountElement = this.querySelector('.like-count');
            const iconElement = this.querySelector('i');
            
            // 切换点赞状态
            this.classList.toggle('liked');
            
            // 更新点赞数
            let currentCount = parseInt(likeCountElement.textContent);
            if (this.classList.contains('liked')) {
                likeCountElement.textContent = currentCount + 1;
                iconElement.classList.remove('far');
                iconElement.classList.add('fas', 'text-pink-500');
            } else {
                likeCountElement.textContent = currentCount - 1;
                iconElement.classList.remove('fas', 'text-pink-500');
                iconElement.classList.add('far');
            }
            
            // 这里可以添加AJAX请求来保存点赞状态到服务器
            // 暂时使用本地存储模拟
            let likedArticles = JSON.parse(localStorage.getItem('likedArticles')) || [];
            if (this.classList.contains('liked')) {
                if (!likedArticles.includes(articleId)) {
                    likedArticles.push(articleId);
                }
            } else {
                likedArticles = likedArticles.filter(id => id !== articleId);
            }
            localStorage.setItem('likedArticles', JSON.stringify(likedArticles));
        });
    });
    
    // 恢复点赞状态
    const likedArticles = JSON.parse(localStorage.getItem('likedArticles')) || [];
    likeButtons.forEach(btn => {
        const articleId = btn.dataset.articleId;
        if (likedArticles.includes(articleId)) {
            btn.classList.add('liked');
            const likeCountElement = btn.querySelector('.like-count');
            const iconElement = btn.querySelector('i');
            likeCountElement.textContent = parseInt(likeCountElement.textContent) + 1;
            iconElement.classList.remove('far');
            iconElement.classList.add('fas', 'text-pink-500');
        }
    });
}

// 文章收藏功能
function initArticleFavorites() {
    const favoriteButtons = document.querySelectorAll('.article-favorite-btn');
    
    favoriteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const articleId = this.dataset.articleId;
            const iconElement = this.querySelector('i');
            
            // 切换收藏状态
            this.classList.toggle('favorited');
            
            if (this.classList.contains('favorited')) {
                iconElement.classList.remove('far');
                iconElement.classList.add('fas', 'text-yellow-500');
            } else {
                iconElement.classList.remove('fas', 'text-yellow-500');
                iconElement.classList.add('far');
            }
            
            // 这里可以添加AJAX请求来保存收藏状态到服务器
            // 暂时使用本地存储模拟
            let favoritedArticles = JSON.parse(localStorage.getItem('favoritedArticles')) || [];
            if (this.classList.contains('favorited')) {
                if (!favoritedArticles.includes(articleId)) {
                    favoritedArticles.push(articleId);
                }
            } else {
                favoritedArticles = favoritedArticles.filter(id => id !== articleId);
            }
            localStorage.setItem('favoritedArticles', JSON.stringify(favoritedArticles));
        });
    });
    
    // 恢复收藏状态
    const favoritedArticles = JSON.parse(localStorage.getItem('favoritedArticles')) || [];
    favoriteButtons.forEach(btn => {
        const articleId = btn.dataset.articleId;
        if (favoritedArticles.includes(articleId)) {
            btn.classList.add('favorited');
            const iconElement = btn.querySelector('i');
            iconElement.classList.remove('far');
            iconElement.classList.add('fas', 'text-yellow-500');
        }
    });
}

// 阅读进度指示器
function initReadingProgress() {
    const progressBar = document.createElement('div');
    progressBar.className = 'reading-progress fixed top-0 left-0 right-0 h-1 bg-pink-500 z-50 transform origin-left scale-x-0 transition-transform duration-100';
    document.body.appendChild(progressBar);
    
    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset;
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrollPercentage = (scrollTop / docHeight) * 100;
        
        progressBar.style.transform = `scaleX(${scrollPercentage / 100})`;
    });
}

// 分享功能
function initShareButtons() {
    const shareButtons = document.querySelectorAll('.share-btn');
    
    shareButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const articleUrl = window.location.href;
            const articleTitle = document.title;
            const platform = this.dataset.platform;
            
            let shareUrl = '';
            
            switch (platform) {
                case 'weibo':
                    shareUrl = `https://service.weibo.com/share/share.php?url=${encodeURIComponent(articleUrl)}&title=${encodeURIComponent(articleTitle)}`;
                    break;
                case 'qq':
                    shareUrl = `https://connect.qq.com/widget/shareqq/index.html?url=${encodeURIComponent(articleUrl)}&title=${encodeURIComponent(articleTitle)}`;
                    break;
                case 'wechat':
                    // 微信分享需要特殊处理，这里可以显示二维码
                    alert('请使用微信扫描二维码分享');
                    return;
                default:
                    // 复制链接
                    navigator.clipboard.writeText(articleUrl).then(() => {
                        alert('链接已复制到剪贴板');
                    }).catch(err => {
                        console.error('复制失败:', err);
                    });
                    return;
            }
            
            window.open(shareUrl, '_blank', 'width=600,height=400');
        });
    });
}

// 暗色模式切换
function initDarkMode() {
    const darkModeToggle = document.createElement('button');
    darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    darkModeToggle.className = 'dark-mode-toggle fixed bottom-6 left-6 p-3 bg-purple-500 text-white rounded-full shadow-lg hover:bg-purple-600 transition-all duration-300 transform hover:scale-110 z-50';
    
    document.body.appendChild(darkModeToggle);
    
    // 检查当前主题
    const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    if (currentTheme === 'dark') {
        document.documentElement.classList.add('dark');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
    
    // 切换主题
    darkModeToggle.addEventListener('click', () => {
        document.documentElement.classList.toggle('dark');
        const newTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('theme', newTheme);
        
        if (newTheme === 'dark') {
            darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        } else {
            darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        }
    });
}

// 初始化所有互动功能
document.addEventListener('DOMContentLoaded', function() {
    initScrollAnimations();
    initSmoothScroll();
    initBackToTop();
    initArticleLikes();
    initArticleFavorites();
    initReadingProgress();
    initShareButtons();
    initDarkMode();
});

// 页面加载完成后初始化
window.addEventListener('load', function() {
    // 可以添加页面加载动画的结束逻辑
    const loader = document.getElementById('page-loader');
    if (loader) {
        loader.style.display = 'none';
    }
});
