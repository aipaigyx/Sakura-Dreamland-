/**
 * AniBlog - 二次元风格博客网站 JavaScript 主文件
 * 包含网站的主要交互功能和动画效果
 */

// 全局变量
let scene, camera, renderer, particles = [];

// 等待DOM加载完成
document.addEventListener('DOMContentLoaded', function() {
    // 初始化导航栏
    initNavbar();
    
    // 初始化侧边栏
    initSidebar();
    
    // 初始化弹幕系统
    initDanmaku();
    
    // 初始化角色立绘生成器
    initCharacterGenerator();
    
    // 初始化图片画廊
    initGallery();
    
    // 初始化滚动动画
    initScrollAnimations();
    
    // 初始化响应式处理
    initResponsive();
    
    // 初始化AJAX实时更新
    initAjaxUpdates();
});

/**
 * 初始化AJAX实时更新功能
 */
function initAjaxUpdates() {
    // 定时获取最新文章
    setInterval(() => {
        fetchLatestArticles();
    }, 60000); // 每分钟更新一次
    
    // 定时获取最新评论
    setInterval(() => {
        fetchLatestComments();
    }, 30000); // 每30秒更新一次
    
    // 定时获取统计数据
    setInterval(() => {
        fetchStatistics();
    }, 300000); // 每5分钟更新一次
    
    // 立即执行一次更新
    fetchLatestArticles();
    fetchLatestComments();
    fetchStatistics();
}

/**
 * 获取最新文章
 */
function fetchLatestArticles(limit = 5) {
    fetch(`/api.php?action=get_latest_articles&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                updateArticlesList(data.data);
            }
        })
        .catch(error => {
            console.error('获取最新文章失败:', error);
        });
}

/**
 * 获取最新评论
 */
function fetchLatestComments(limit = 5) {
    fetch(`/api.php?action=get_latest_comments&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                updateCommentsList(data.data);
            }
        })
        .catch(error => {
            console.error('获取最新评论失败:', error);
        });
}

/**
 * 获取统计数据
 */
function fetchStatistics() {
    fetch('/api.php?action=get_statistics')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatistics(data.data);
            }
        })
        .catch(error => {
            console.error('获取统计数据失败:', error);
        });
}

/**
 * 更新文章列表
 * @param {Array} articles - 文章数据
 */
function updateArticlesList(articles) {
    // 找到文章列表容器
    const articlesContainer = document.querySelector('.latest-articles-container');
    if (!articlesContainer) return;
    
    // 更新文章列表
    const articleCards = articlesContainer.querySelectorAll('.modern-card');
    if (articleCards.length && articles.length) {
        articleCards.forEach((card, index) => {
            if (index < articles.length) {
                const article = articles[index];
                const titleElement = card.querySelector('.article-title');
                const contentElement = card.querySelector('.article-content');
                const imageElement = card.querySelector('.article-image');
                
                if (titleElement) {
                    titleElement.textContent = article.title;
                }
                
                if (contentElement) {
                    contentElement.textContent = article.summary || article.content.substring(0, 100) + '...';
                }
                
                if (imageElement) {
                    imageElement.src = article.cover_image || 'https://via.placeholder.com/400x200?text=No+Image';
                    imageElement.alt = article.title;
                }
            }
        });
    }
}

/**
 * 更新评论列表
 * @param {Array} comments - 评论数据
 */
function updateCommentsList(comments) {
    // 找到评论列表容器
    const commentsContainer = document.querySelector('.comments-list-container');
    if (!commentsContainer) return;
    
    // 更新评论列表
    const commentItems = commentsContainer.querySelectorAll('.comment-item');
    if (commentItems.length && comments.length) {
        commentItems.forEach((item, index) => {
            if (index < comments.length) {
                const comment = comments[index];
                const contentElement = item.querySelector('.comment-content');
                const authorElement = item.querySelector('.comment-author');
                const timeElement = item.querySelector('.comment-time');
                
                if (contentElement) {
                    contentElement.textContent = comment.content;
                }
                
                if (authorElement) {
                    authorElement.textContent = '用户';
                }
                
                if (timeElement) {
                    timeElement.textContent = formatRelativeTime(new Date(comment.created_at));
                }
            }
        });
    }
}

/**
 * 更新统计数据
 * @param {Object} stats - 统计数据
 */
function updateStatistics(stats) {
    // 更新统计数据显示
    const articlesCountElement = document.querySelector('.articles-count');
    const imagesCountElement = document.querySelector('.images-count');
    const commentsCountElement = document.querySelector('.comments-count');
    const usersCountElement = document.querySelector('.users-count');
    
    if (articlesCountElement) {
        articlesCountElement.textContent = stats.articles;
    }
    
    if (imagesCountElement) {
        imagesCountElement.textContent = stats.images;
    }
    
    if (commentsCountElement) {
        commentsCountElement.textContent = stats.comments;
    }
    
    if (usersCountElement) {
        usersCountElement.textContent = stats.users;
    }
}

/**
 * 初始化导航栏
 */
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarNav = document.querySelector('.navbar-nav');
    const navbarSearch = document.querySelector('.navbar-search input');
    
    // 导航栏滚动效果
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // 移动端菜单切换
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            navbarNav.classList.toggle('open');
            navbarToggler.classList.toggle('active');
            
            // 切换图标
            const icon = navbarToggler.querySelector('i');
            if (navbarNav.classList.contains('open')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    
    // 搜索框功能
    if (navbarSearch) {
        navbarSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    alert(`搜索: ${searchTerm}`);
                    // 实际项目中这里会跳转到搜索结果页
                    // window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
                }
            }
        });
    }
    
    // 导航链接点击事件
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // 移除所有链接的active类
            navLinks.forEach(item => item.classList.remove('active'));
            
            // 为当前点击的链接添加active类
            this.classList.add('active');
            
            // 如果是移动端，点击后关闭菜单
            if (window.innerWidth < 768) {
                navbarNav.classList.remove('open');
                if (navbarToggler) {
                    const icon = navbarToggler.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    });
}

/**
 * 初始化侧边栏
 */
function initSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebarNavItems = document.querySelectorAll('.sidebar-nav-item');
    
    // 侧边栏切换
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
    
    // 侧边栏导航项点击事件
    if (sidebarNavItems.length) {
        sidebarNavItems.forEach(item => {
            item.addEventListener('click', function() {
                // 移除所有项的active类
                sidebarNavItems.forEach(navItem => navItem.classList.remove('active'));
                
                // 为当前点击的项添加active类
                this.classList.add('active');
            });
        });
    }
    
    // 点击页面其他区域关闭侧边栏（移动端）
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992 && 
            sidebar && sidebar.classList.contains('open') && 
            !sidebar.contains(e.target) && 
            !e.target.closest('.sidebar-toggle')) {
            sidebar.classList.remove('open');
        }
    });
}

/**
 * 初始化弹幕系统
 */
function initDanmaku() {
    const danmakuContainer = document.querySelector('.danmaku-container');
    const danmakuInput = document.querySelector('.danmaku-input input');
    const danmakuSend = document.querySelector('.danmaku-input button');
    const danmakuColor = document.querySelector('.danmaku-input select[name="color"]');
    const danmakuSize = document.querySelector('.danmaku-input select[name="size"]');
    const danmakuMode = document.querySelector('.danmaku-input select[name="mode"]');
    
    // 示例弹幕数据
    const sampleDanmakus = [
        { content: "这部动漫太好看了！", color: "#ffffff", size: 25, mode: "scroll" },
        { content: "主角好可爱~", color: "#ff69b4", size: 20, mode: "scroll" },
        { content: "画质真棒", color: "#00bfff", size: 25, mode: "top" },
        { content: "这个场景太感人了", color: "#32cd32", size: 20, mode: "bottom" },
        { content: "音乐很好听", color: "#ffa500", size: 25, mode: "scroll" },
        { content: "期待下一集", color: "#9370db", size: 20, mode: "top" },
        { content: "这个角色设定很有趣", color: "#ff6347", size: 25, mode: "scroll" },
        { content: "剧情发展不错", color: "#4682b4", size: 20, mode: "bottom" },
        { content: "画风很喜欢", color: "#ffc0cb", size: 25, mode: "scroll" },
        { content: "这个梗笑死我了", color: "#20b2aa", size: 20, mode: "scroll" }
    ];
    
    // 自动播放示例弹幕
    if (danmakuContainer) {
        let index = 0;
        const autoPlayInterval = setInterval(() => {
            if (index < sampleDanmakus.length) {
                createDanmaku(sampleDanmakus[index]);
                index++;
            } else {
                clearInterval(autoPlayInterval);
            }
        }, 1000);
    }
    
    // 发送弹幕
    function sendDanmaku() {
        if (!danmakuInput || !danmakuContainer) return;
        
        const content = danmakuInput.value.trim();
        if (!content) return;
        
        const color = danmakuColor ? danmakuColor.value : "#ffffff";
        const size = danmakuSize ? parseInt(danmakuSize.value) : 25;
        const mode = danmakuMode ? danmakuMode.value : "scroll";
        
        createDanmaku({ content, color, size, mode });
        
        // 清空输入框
        danmakuInput.value = "";
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
        
        // 随机动画持续时间（5-10秒）
        const duration = 5 + Math.random() * 5;
        
        // 根据模式设置弹幕位置
        switch (danmaku.mode) {
            case "top":
                danmakuEl.style.top = `${20 + Math.random() * (containerHeight * 0.3)}px`;
                danmakuEl.style.left = `${containerWidth}px`;
                danmakuEl.style.animationName = "danmakuMoveTop";
                break;
            case "bottom":
                danmakuEl.style.bottom = `${20 + Math.random() * (containerHeight * 0.3)}px`;
                danmakuEl.style.left = `${containerWidth}px`;
                danmakuEl.style.animationName = "danmakuMoveBottom";
                break;
            case "scroll":
            default:
                danmakuEl.style.top = `${20 + Math.random() * (containerHeight - 40 - danmaku.size)}px`;
                danmakuEl.style.left = `${containerWidth}px`;
                danmakuEl.style.animationName = "danmakuMoveScroll";
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
    if (danmakuSend) {
        danmakuSend.addEventListener('click', sendDanmaku);
    }
    
    // 绑定输入框回车事件
    if (danmakuInput) {
        danmakuInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendDanmaku();
            }
        });
    }
}

/**
 * 初始化角色立绘生成器
 */
function initCharacterGenerator() {
    const generatorForm = document.querySelector('.generator-form');
    const generateBtn = document.querySelector('.generator-actions .btn-primary');
    const randomBtn = document.querySelector('.generator-actions .btn-secondary');
    const previewImage = document.querySelector('.preview-image');
    const previewPlaceholder = document.querySelector('.preview-placeholder');
    
    // 角色属性选项
    const characterOptions = {
        hairColor: ['粉色', '蓝色', '紫色', '金色', '黑色', '棕色'],
        eyeColor: ['蓝色', '紫色', '红色', '绿色', '金色', '棕色'],
        hairStyle: ['短发', '长发', '双马尾', '单马尾', '卷发', '麻花辫'],
        clothing: ['校服', '魔法袍', '连衣裙', '休闲装', '和服', '洋装'],
        accessories: ['发带', '帽子', '眼镜', '项链', '耳环', '手环'],
        expression: ['微笑', '惊讶', '害羞', '生气', '悲伤', ' neutral'],
        background: ['星空', '樱花', '森林', '城市', '海边', '魔法阵']
    };
    
    // 示例角色图片（实际项目中会通过API生成）
    const sampleCharacters = [
        'https://p26-doubao-search-sign.byteimg.com/labis/0097908982e2ae086ab14383a097ef38~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=JUu3v9V3r6vz6fzZBMeUPFABnvA%3D',
        'https://p26-doubao-search-sign.byteimg.com/labis/7bb08bebd255bc1a8631fa9e0131b928~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=WUeq0dvxQwcNLWXc5CQ5Tp7AFMM%3D',
        'https://p26-doubao-search-sign.byteimg.com/tos-cn-i-qvj2lq49k0/f1570af9f1ce450dab1d05d7e5d51294~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=aCJg%2FXUomJwxSUJFX1mleJSFzq8%3D',
        'https://p26-doubao-search-sign.byteimg.com/tos-cn-i-qvj2lq49k0/97787d82c80140b8913ee13a8271c4d5~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=9n8urgN%2BXyZ3U8Oeb0SZjXc%2Fu6E%3D',
        'https://p3-doubao-search-sign.byteimg.com/mosaic-legacy/39a000002abc809b45ca~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=qdr32t2qi0dP0FXJ0PcpNJ5uqZI%3D',
        'https://p11-doubao-search-sign.byteimg.com/labis/843ce5a438b3012ed110f1dd03028e11~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=mqayAapVs627p2nk5dld8X8FQ20%3D',
        'https://p26-doubao-search-sign.byteimg.com/labis/0abd3187cf6e5074068564238ce7e800~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=8mNWKyp4vomEQvux7gqObvISGFs%3D',
        'https://p11-doubao-search-sign.byteimg.com/labis/image/026e63959e2aef885306f9b9c6160cc2~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=IxXLnbpBhc19z%2F4%2BR8AJND62f8g%3D'
    ];
    
    // 生成角色
    function generateCharacter() {
        if (!previewImage || !previewPlaceholder) return;
        
        // 显示加载状态
        previewPlaceholder.innerHTML = '<i class="fa fa-spinner fa-spin"></i><p>正在生成角色...</p>';
        previewPlaceholder.style.display = 'flex';
        previewImage.style.display = 'none';
        
        // 模拟生成延迟
        setTimeout(() => {
            // 随机选择一个示例角色图片
            const randomIndex = Math.floor(Math.random() * sampleCharacters.length);
            const characterImage = sampleCharacters[randomIndex];
            
            // 显示生成的角色
            previewImage.src = characterImage;
            previewImage.style.display = 'block';
            previewPlaceholder.style.display = 'none';
            
            // 添加动画效果
            previewImage.classList.add('bounce-in');
            setTimeout(() => {
                previewImage.classList.remove('bounce-in');
            }, 600);
        }, 1500);
    }
    
    // 随机生成角色
    function randomGenerateCharacter() {
        // 随机选择表单选项
        const selects = generatorForm.querySelectorAll('select');
        selects.forEach(select => {
            const randomIndex = Math.floor(Math.random() * select.options.length);
            select.selectedIndex = randomIndex;
        });
        
        // 随机设置滑块值
        const ranges = generatorForm.querySelectorAll('input[type="range"]');
        ranges.forEach(range => {
            const min = parseInt(range.min);
            const max = parseInt(range.max);
            range.value = Math.floor(Math.random() * (max - min + 1)) + min;
        });
        
        // 生成角色
        generateCharacter();
    }
    
    // 绑定生成按钮点击事件
    if (generateBtn) {
        generateBtn.addEventListener('click', generateCharacter);
    }
    
    // 绑定随机生成按钮点击事件
    if (randomBtn) {
        randomBtn.addEventListener('click', randomGenerateCharacter);
    }
    
    // 初始化表单滑块显示值
    const ranges = document.querySelectorAll('input[type="range"]');
    ranges.forEach(range => {
        const valueDisplay = document.createElement('span');
        valueDisplay.className = 'range-value';
        valueDisplay.textContent = range.value;
        range.parentNode.appendChild(valueDisplay);
        
        range.addEventListener('input', function() {
            valueDisplay.textContent = this.value;
        });
    });
}

/**
 * 初始化图片画廊
 */
function initGallery() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const galleryModal = document.getElementById('gallery-modal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const modalClose = document.getElementById('modal-close');
    
    // 示例图片数据
    const galleryImages = [
        {
            url: 'https://p3-doubao-search-sign.byteimg.com/labis/d1a0d38bd6a0217b768cf227b4f66e61~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=E6LmfQ%2FexysZylEkuMcx%2BeZPRyw%3D',
            title: '动漫小镇风景',
            description: '二次元风格的小镇风景插画'
        },
        {
            url: 'https://p11-doubao-search-sign.byteimg.com/ecom-shop-material/jpeg_m_9ec65cdf83cb0b755b00ca98b9f2c9b6_sx_91383_www800-800~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=%2Bxj9zo0lDJxFCWGY8JFy2ctbji8%3D',
            title: '蓝天白云',
            description: '清新的天空和云朵插画'
        },
        {
            url: 'https://p3-doubao-search-sign.byteimg.com/labis/b140cfe2d7daa1725d29b6e61c1a738f~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=1jJTFUEXaa4DZrfNsRZiy9lxlFM%3D',
            title: '神秘建筑',
            description: '充满神秘感的古老建筑插画'
        },
        {
            url: 'https://p26-doubao-search-sign.byteimg.com/ecom-shop-material/jpeg_m_46872f6eff899a2dcf9afd1817a4127f_sx_69466_www800-800~tplv-be4g95zd3a-image.jpeg?lk3s=feb11e32&x-expires=1783646807&x-signature=0R1fCkPw1eMVEpEqRobyCQVCGxY%3D',
            title: '远眺小镇',
            description: '从山坡上远眺小镇的风景插画'
        }
    ];
    
    // 填充画廊
    if (galleryItems.length && galleryImages.length) {
        galleryItems.forEach((item, index) => {
            if (index < galleryImages.length) {
                const image = galleryImages[index];
                const imgElement = item.querySelector('img');
                const titleElement = item.querySelector('.gallery-item-title');
                
                if (imgElement) {
                    imgElement.src = image.url;
                    imgElement.alt = image.title;
                }
                
                if (titleElement) {
                    titleElement.textContent = image.title;
                }
                
                // 点击打开模态框
                item.addEventListener('click', function() {
                    openGalleryModal(image);
                });
            }
        });
    }
    
    // 打开画廊模态框
    function openGalleryModal(image) {
        if (!galleryModal || !modalImage || !modalTitle) return;
        
        modalImage.src = image.url;
        modalImage.alt = image.title;
        modalTitle.textContent = image.title;
        
        galleryModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // 添加动画效果
        setTimeout(() => {
            galleryModal.querySelector('.modal-content').classList.add('bounce-in');
        }, 10);
    }
    
    // 关闭画廊模态框
    function closeGalleryModal() {
        if (!galleryModal) return;
        
        galleryModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // 移除动画效果
        const modalContent = galleryModal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.remove('bounce-in');
        }
    }
    
    // 绑定模态框关闭按钮点击事件
    if (modalClose) {
        modalClose.addEventListener('click', closeGalleryModal);
    }
    
    // 点击模态框背景关闭
    if (galleryModal) {
        galleryModal.addEventListener('click', function(e) {
            if (e.target === galleryModal) {
                closeGalleryModal();
            }
        });
    }
    
    // 按ESC键关闭模态框
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && galleryModal && galleryModal.style.display === 'flex') {
            closeGalleryModal();
        }
    });
}

/**
 * 初始化滚动动画
 */
function initScrollAnimations() {
    // 监听滚动事件，为元素添加动画
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // 获取延迟时间
                const delay = entry.target.getAttribute('data-delay') || 0;
                
                // 使用GSAP动画
                gsap.to(entry.target, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    delay: delay,
                    ease: "power3.out",
                    onComplete: () => {
                        observer.unobserve(entry.target);
                    }
                });
            }
        });
    }, {
        threshold: 0.1
    });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
    
    // 平滑滚动到锚点
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80, // 减去导航栏高度
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * 初始化响应式处理
 */
function initResponsive() {
    // 检测窗口大小变化
    function handleResize() {
        const sidebar = document.querySelector('.sidebar');
        const navbarNav = document.querySelector('.navbar-nav');
        
        // 在大屏幕上自动打开侧边栏
        if (window.innerWidth >= 992 && sidebar) {
            sidebar.classList.add('open');
        } else if (sidebar) {
            sidebar.classList.remove('open');
        }
        
        // 在大屏幕上确保导航菜单是打开的
        if (window.innerWidth >= 768 && navbarNav) {
            navbarNav.classList.remove('open');
            const navbarToggler = document.querySelector('.navbar-toggler');
            if (navbarToggler) {
                const icon = navbarToggler.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }
    }
    
    // 初始调用一次
    handleResize();
    
    // 监听窗口大小变化
    window.addEventListener('resize', handleResize);
    
    // 移动端触摸手势支持
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);
    
    function handleSwipe() {
        const sidebar = document.querySelector('.sidebar');
        const threshold = 50; // 滑动阈值
        
        // 从左向右滑动，打开侧边栏
        if (touchEndX - touchStartX > threshold && window.innerWidth < 992 && sidebar) {
            sidebar.classList.add('open');
        }
        
        // 从右向左滑动，关闭侧边栏
        if (touchStartX - touchEndX > threshold && window.innerWidth < 992 && sidebar) {
            sidebar.classList.remove('open');
        }
    }
}

/**
 * 工具函数：防抖
 * @param {Function} func - 要执行的函数
 * @param {number} wait - 等待时间（毫秒）
 * @returns {Function} - 防抖后的函数
 */
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(context, args);
        }, wait);
    };
}

/**
 * 工具函数：节流
 * @param {Function} func - 要执行的函数
 * @param {number} limit - 时间限制（毫秒）
 * @returns {Function} - 节流后的函数
 */
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const context = this;
        const args = arguments;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => {
                inThrottle = false;
            }, limit);
        }
    };
}

/**
 * 工具函数：获取随机颜色
 * @returns {string} - 十六进制颜色值
 */
function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

/**
 * 工具函数：格式化时间
 * @param {Date} date - 日期对象
 * @returns {string} - 格式化后的时间字符串
 */
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * 工具函数：格式化相对时间
 * @param {Date} date - 日期对象
 * @returns {string} - 相对时间字符串
 */
function formatRelativeTime(date) {
    const now = new Date();
    const diff = now - date;
    
    const seconds = Math.floor(diff / 1000);
    const minutes = Math.floor(seconds / 60);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    const months = Math.floor(days / 30);
    const years = Math.floor(months / 12);
    
    if (years > 0) {
        return `${years}年前`;
    } else if (months > 0) {
        return `${months}个月前`;
    } else if (days > 0) {
        return `${days}天前`;
    } else if (hours > 0) {
        return `${hours}小时前`;
    } else if (minutes > 0) {
        return `${minutes}分钟前`;
    } else {
        return '刚刚';
    }
}

/**
 * 工具函数：生成随机ID
 * @param {number} length - ID长度
 * @returns {string} - 随机ID
 */
function generateRandomId(length = 8) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

/**
 * 工具函数：检查元素是否在视口中
 * @param {HTMLElement} element - 要检查的元素
 * @returns {boolean} - 是否在视口中
 */
function isElementInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * 工具函数：平滑滚动到顶部
 */
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

/**
 * 工具函数：复制文本到剪贴板
 * @param {string} text - 要复制的文本
 * @returns {Promise<boolean>} - 是否复制成功
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (err) {
        console.error('复制失败:', err);
        return false;
    }
}

/**
 * 工具函数：检测设备类型
 * @returns {Object} - 设备类型信息
 */
function detectDevice() {
    const ua = navigator.userAgent;
    return {
        isMobile: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua),
        isTablet: /iPad|Android(?!.*Mobile)/i.test(ua),
        isDesktop: !/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(ua),
        isIOS: /iPhone|iPad|iPod/i.test(ua),
        isAndroid: /Android/i.test(ua),
        isWindows: /Windows/i.test(ua),
        isMacOS: /Macintosh/i.test(ua)
    };
}

/**
 * 工具函数：检测浏览器类型
 * @returns {Object} - 浏览器类型信息
 */
function detectBrowser() {
    const ua = navigator.userAgent;
    return {
        isChrome: /Chrome/i.test(ua) && !/Edg/i.test(ua),
        isFirefox: /Firefox/i.test(ua),
        isSafari: /Safari/i.test(ua) && !/Chrome/i.test(ua),
        isEdge: /Edg/i.test(ua),
        isIE: /Trident/i.test(ua)
    };
}

/**
 * 工具函数：本地存储操作
 */
const storage = {
    set: function(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (err) {
            console.error('存储失败:', err);
            return false;
        }
    },
    
    get: function(key, defaultValue = null) {
        try {
            const value = localStorage.getItem(key);
            return value ? JSON.parse(value) : defaultValue;
        } catch (err) {
            console.error('读取失败:', err);
            return defaultValue;
        }
    },
    
    remove: function(key) {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (err) {
            console.error('删除失败:', err);
            return false;
        }
    },
    
    clear: function() {
        try {
            localStorage.clear();
            return true;
        } catch (err) {
            console.error('清空失败:', err);
            return false;
        }
    }
};

/**
 * 工具函数：Cookie操作
 */
const cookie = {
    set: function(name, value, days = 30) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/`;
    },
    
    get: function(name) {
        name = `${name}=`;
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return '';
    },
    
    remove: function(name) {
        document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
    }
};