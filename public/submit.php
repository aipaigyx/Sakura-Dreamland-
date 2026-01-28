<?php
/**
 * 投稿页面
 */



// 加载核心功能文件
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

// 设置页面标题
$page_title = '文章投稿 - 樱花梦境';

// 初始化变量
$error = '';
$success = '';

// 获取当前登录用户信息
$current_user = null;
$author_id = 1; // 默认使用管理员ID
$submitter_name = '匿名用户';
$submitter_email = 'anonymous@example.com';

// 检查普通用户登录
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $current_user = db_query_one($sql, [$_SESSION['user_id']]);
    if ($current_user) {
        $author_id = $current_user['id'];
        $submitter_name = $current_user['username'];
        $submitter_email = $current_user['email'];
    }
} 
// 检查管理员登录
elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $current_user = db_query_one($sql, [$_SESSION['admin_id']]);
    if ($current_user) {
        $author_id = $current_user['id'];
        $submitter_name = $current_user['username'];
        $submitter_email = $current_user['email'];
    }
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
    $category_id = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $tags = isset($_POST['tags']) && is_array($_POST['tags']) ? $_POST['tags'] : [];
    
    // 验证表单数据
    if (empty($title)) {
        $error = '文章标题不能为空';
    } elseif (empty($content)) {
        $error = '文章内容不能为空';
    } elseif ($category_id <= 0) {
        $error = '请选择文章分类';
    } else {
        // 处理文件上传（如果有）
        $cover_image = '';
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/uploads/article-covers/';
            
            // 创建上传目录（如果不存在）
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // 生成唯一文件名
            $file_ext = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid('cover_') . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            // 上传文件
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $file_path)) {
                $cover_image = '/uploads/article-covers/' . $file_name;
            } else {
                $error = '文件上传失败';
            }
        }
        
        if (empty($error)) {
            // 保存投稿数据到数据库
            $sql = "INSERT INTO articles (title, content, summary, cover_image, category_id, author_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            
            // 使用当前登录用户ID或管理员ID 1
            $pdo = get_db_connection();
            $result = db_exec($sql, [$title, $content, $summary, $cover_image, $category_id, $author_id], $pdo);
            
            if ($result) {
                // 获取最后插入的文章ID
                $article_id = $pdo->lastInsertId();
                
                // 记录投稿人信息
                $sql = "INSERT INTO submissions (article_id, submitter_name, submitter_email, status, created_at) VALUES (?, ?, ?, 'pending', CURRENT_TIMESTAMP)";
                db_exec($sql, [$article_id, $submitter_name, $submitter_email], $pdo);
                
                // 处理文章标签
                if (!empty($tags)) {
                    foreach ($tags as $tag_id) {
                        if (is_numeric($tag_id)) {
                            $tag_sql = "INSERT INTO article_tags (article_id, tag_id, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)";
                            db_exec($tag_sql, [$article_id, $tag_id], $pdo);
                        }
                    }
                }
                
                $success = '文章投稿成功！我们将在审核后发布。';
                // 重置表单
                $_POST = [];
            } else {
                $error = '投稿失败，请稍后重试';
            }
        }
    }
}

// 获取所有分类
$categories = aniblog_get_categories();

// 加载页面模板
include __DIR__ . '/header.php';
?>

<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <!-- 页面标题 -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-600 mb-3">
                文章投稿
            </h1>
            <p class="text-gray-600">分享你的创作，让更多人看到你的作品</p>
        </div>
        
        <!-- 消息提示 -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- 投稿表单 -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8">
            <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                <!-- 表单加载状态 -->
                <div id="form-loading" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-xl p-6 text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-pink-500 mx-auto mb-4"></div>
                        <p class="text-gray-700">投稿处理中，请稍候...</p>
                    </div>
                </div>
                
                <!-- 投稿人信息（自动填充） -->
                <div class="modern-card p-4 bg-gradient-to-br from-pink-50 to-purple-50 rounded-xl shadow-md relative overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-2 mb-6 flex flex-col items-center">
                    <h3 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-user-check text-pink-500 mr-2"></i> 投稿人信息
                    </h3>
                    <div class="flex flex-col items-center text-center">
                        <!-- 用户头像 -->
                        <div class="mb-3">
                            <?php 
                // 只使用用户自己上传的头像
                $avatar = '';
                
                // 优先从当前用户对象获取头像
                if ($current_user && !empty($current_user['avatar'])) {
                    $avatar = $current_user['avatar'];
                }
                // 其次使用会话中的普通用户头像
                elseif (isset($_SESSION['user_avatar']) && !empty($_SESSION['user_avatar'])) {
                    $avatar = $_SESSION['user_avatar'];
                }
                // 然后使用会话中的管理员头像
                elseif (isset($_SESSION['admin_avatar']) && !empty($_SESSION['admin_avatar'])) {
                    $avatar = $_SESSION['admin_avatar'];
                }
                // 只有在有用户头像时才显示头像元素
                if (!empty($avatar)) {
            ?>  
            <img src="<?php echo $avatar; ?>" 
                                 alt="用户头像" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md ring-3 ring-pink-300">
            <?php } ?>
                        </div>
                        <!-- 用户信息 -->
                        <div class="text-sm text-gray-600">
                            <p class="font-medium text-gray-800">用户名：<?php echo htmlspecialchars($submitter_name); ?></p>
                            <p>邮箱：<?php echo htmlspecialchars($submitter_email); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- 文章标题 -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        文章标题 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                           placeholder="请输入文章标题" 
                           class="w-full px-4 py-4 text-lg border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 transition-all hover:border-pink-300">
                    <div class="error-message text-red-500 text-xs mt-1" id="title-error"></div>
                </div>
                
                <!-- 文章分类 -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        文章分类 <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 transition-all hover:border-pink-300">
                        <option value="0">请选择分类</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo isset($_POST['category_id']) && (int)$_POST['category_id'] === $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-message text-red-500 text-xs mt-1" id="category-error"></div>
                </div>
                
                <!-- 文章标签 -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        文章标签
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <?php 
                            $all_tags = get_all_tags();
                        ?>
                        <?php foreach ($all_tags as $tag): ?>
                            <label class="inline-flex items-center gap-2 cursor-pointer bg-gray-100 hover:bg-pink-100 px-4 py-2 rounded-full transition-colors duration-200">
                                <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" 
                                       class="text-pink-500 focus:ring-pink-500 border-gray-300 rounded-full">
                                <span class="text-sm text-gray-700 hover:text-pink-700"><?php echo $tag['name']; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">选择与文章相关的标签，可多选</p>
                </div>
                
                <!-- 文章摘要 -->
                <div>
                    <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">
                        文章摘要
                    </label>
                    <textarea id="summary" name="summary" rows="4"
                              placeholder="简要描述文章内容，建议 150-200 字"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 transition-all hover:border-pink-300 resize-y">
                        <?php echo isset($_POST['summary']) ? htmlspecialchars($_POST['summary']) : ''; ?></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">建议 150-200 字</p>
                        <p class="text-xs text-gray-500" id="summary-count">0 字</p>
                    </div>
                </div>
                
                <!-- 文章封面 -->
                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-3">
                        文章封面
                    </label>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="w-32 h-32 bg-gradient-to-r from-pink-300 to-purple-300 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                            <img id="cover-preview" 
                                 src="<?php echo isset($_POST['cover_image']) ? $_POST['cover_image'] : ''; ?>" 
                                 alt="文章封面预览" 
                                 class="w-full h-full object-cover <?php echo !isset($_POST['cover_image']) ? 'hidden' : ''; ?>">
                            <div id="cover-placeholder" class="w-full h-full flex items-center justify-center text-white opacity-70 <?php echo isset($_POST['cover_image']) ? 'hidden' : ''; ?>">
                                <i class="fas fa-image text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" id="cover_image" name="cover_image" 
                                   accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200 transition-colors duration-200">
                            <p class="text-xs text-gray-500 mt-2">支持 JPG, PNG, GIF 格式，建议尺寸 800x400，大小不超过 2MB</p>
                            <div class="error-message text-red-500 text-xs mt-1" id="cover-error"></div>
                        </div>
                    </div>
                </div>
                
                <!-- 文章内容 -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                        文章内容 <span class="text-red-500">*</span>
                    </label>
                    <textarea id="content" name="content" rows="18"
                              required
                              placeholder="请输入文章内容，支持 HTML 格式"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 transition-all hover:border-pink-300 resize-y">
                        <?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">支持 HTML 格式，请勿发布违规内容</p>
                        <p class="text-xs text-gray-500" id="content-count">0 字</p>
                    </div>
                    <div class="error-message text-red-500 text-xs mt-1" id="content-error"></div>
                </div>
                
                <!-- 表单底部 -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <button type="reset" 
                                class="px-8 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-all duration-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <i class="fas fa-undo mr-2"></i>重置
                        </button>
                        <button type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:from-pink-600 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-pink-400 transform hover:-translate-y-0.5">
                            <i class="fas fa-paper-plane mr-2"></i>提交投稿
                        </button>
                    </div>
                    <p class="text-center text-xs text-gray-500 mt-4">
                        点击提交即表示您同意我们的 <a href="#" class="text-pink-500 hover:underline">投稿须知</a> 和 <a href="#" class="text-pink-500 hover:underline">隐私政策</a>
                    </p>
                </div>
            </form>
        </div>
        
        <!-- 投稿须知 -->
        <div class="mt-12 bg-gray-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">
                <i class="fas fa-info-circle text-pink-500 mr-2"></i>投稿须知
            </h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>投稿内容必须符合国家法律法规，不得包含违法违规信息</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>请确保投稿内容为原创或已获得授权，不得侵犯他人版权</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>我们会在24-48小时内审核您的投稿，审核通过后会发布</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>投稿成功后，您将收到邮件通知</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i>如有任何问题，请联系我们的客服邮箱：admin@example.com</li>
            </ul>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 实时字数统计
            function updateCharacterCount(textarea, counterId) {
                const text = textarea.value;
                const counter = document.getElementById(counterId);
                counter.textContent = text.length + ' 字';
            }
            
            // 文章摘要字数统计
            const summaryTextarea = document.getElementById('summary');
            if (summaryTextarea) {
                updateCharacterCount(summaryTextarea, 'summary-count');
                summaryTextarea.addEventListener('input', function() {
                    updateCharacterCount(this, 'summary-count');
                });
            }
            
            // 文章内容字数统计
            const contentTextarea = document.getElementById('content');
            if (contentTextarea) {
                updateCharacterCount(contentTextarea, 'content-count');
                contentTextarea.addEventListener('input', function() {
                    updateCharacterCount(this, 'content-count');
                });
            }
            
            // 封面图片预览
            const coverImageInput = document.getElementById('cover_image');
            const coverPreview = document.getElementById('cover-preview');
            const coverPlaceholder = document.getElementById('cover-placeholder');
            
            if (coverImageInput && coverPreview && coverPlaceholder) {
                coverImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // 验证文件大小（最大2MB）
                        if (file.size > 2 * 1024 * 1024) {
                            alert('图片大小不能超过2MB');
                            e.target.value = '';
                            return;
                        }
                        
                        // 验证文件类型
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('只允许上传JPG、PNG、GIF格式的图片');
                            e.target.value = '';
                            return;
                        }
                        
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            coverPreview.src = e.target.result;
                            coverPreview.classList.remove('hidden');
                            coverPlaceholder.classList.add('hidden');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        coverPreview.classList.add('hidden');
                        coverPlaceholder.classList.remove('hidden');
                    }
                });
            }
            
            // 表单验证
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // 清除之前的错误信息
                    document.querySelectorAll('.error-message').forEach(el => {
                        el.textContent = '';
                    });
                    
                    // 验证标题
                    const titleInput = document.getElementById('title');
                    if (!titleInput.value.trim()) {
                        isValid = false;
                        document.getElementById('title-error').textContent = '请输入文章标题';
                    }
                    
                    // 验证分类
                    const categorySelect = document.getElementById('category_id');
                    if (categorySelect.value == 0) {
                        isValid = false;
                        document.getElementById('category-error').textContent = '请选择文章分类';
                    }
                    
                    // 验证内容
                    const contentInput = document.getElementById('content');
                    if (!contentInput.value.trim()) {
                        isValid = false;
                        document.getElementById('content-error').textContent = '请输入文章内容';
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        // 滚动到第一个错误位置
                        const firstError = document.querySelector('.error-message:not(:empty)');
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    } else {
                        // 显示加载状态
                        const loadingOverlay = document.getElementById('form-loading');
                        if (loadingOverlay) {
                            loadingOverlay.classList.remove('hidden');
                        }
                    }
                });
            }
            
            // 表单重置时的处理
            const resetBtn = document.querySelector('button[type="reset"]');
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    // 重置字数统计
                    if (summaryTextarea) {
                        updateCharacterCount(summaryTextarea, 'summary-count');
                    }
                    if (contentTextarea) {
                        updateCharacterCount(contentTextarea, 'content-count');
                    }
                    
                    // 重置封面预览
                    if (coverPreview && coverPlaceholder) {
                        coverPreview.classList.add('hidden');
                        coverPlaceholder.classList.remove('hidden');
                    }
                    
                    // 清除错误信息
                    document.querySelectorAll('.error-message').forEach(el => {
                        el.textContent = '';
                    });
                });
            }
            
            // 添加表单元素的焦点效果
            const formElements = document.querySelectorAll('input, textarea, select');
            formElements.forEach(element => {
                element.addEventListener('focus', function() {
                    if (this.parentElement) {
                        this.parentElement.classList.add('focused');
                    }
                });
                
                element.addEventListener('blur', function() {
                    if (this.parentElement) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
            
            // 标签选择的交互效果
            const tagLabels = document.querySelectorAll('label:has(input[type="checkbox"])');
            tagLabels.forEach(label => {
                const checkbox = label.querySelector('input[type="checkbox"]');
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        label.classList.remove('bg-gray-100');
                        label.classList.add('bg-pink-100', 'text-pink-700');
                    } else {
                        label.classList.remove('bg-pink-100', 'text-pink-700');
                        label.classList.add('bg-gray-100');
                    }
                });
            });
        });
        </script>
        
        <style>
        /* 添加一些额外的样式 */
        .error-message {
            transition: all 0.3s ease;
        }
        
        .focused .error-message {
            opacity: 1;
        }
        
        textarea {
            transition: all 0.3s ease;
        }
        
        /* 美化滚动条 */
        textarea::-webkit-scrollbar {
            width: 8px;
        }
        
        textarea::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        textarea::-webkit-scrollbar-thumb {
            background: #ec4899;
            border-radius: 4px;
        }
        
        textarea::-webkit-scrollbar-thumb:hover {
            background: #db2777;
        }
        
        /* 表单元素聚焦效果 */
        input:focus,
        textarea:focus,
        select:focus {
            border-color: #ec4899 !important;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1) !important;
        }
        
        /* 标签选择效果 */
        label:has(input[type="checkbox"]:checked) {
            background-color: #fce7f3 !important;
            color: #db2777 !important;
        }
        
        /* 加载动画 */
        #form-loading {
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        </style>
    </div>
</div>

<!-- TinyMCE编辑器 -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<!-- 加载中文语言包 - 本地资源 -->
<script src="/assets/tinymce/langs/zh_CN.min.js"></script>
<script>
// 使用直接方式初始化编辑器，解决只读问题
function initEditor() {
    console.log('正在初始化富文本编辑器...');
    
    // 检查content元素是否存在
    const contentEl = document.getElementById('content');
    if (!contentEl) {
        console.error('未找到ID为content的元素，无法初始化编辑器');
        return;
    }
    
    try {
        // 增强编辑器配置，添加更多插件和功能
        tinymce.init({
            selector: '#content',
            height: 600,
            menubar: true,
            statusbar: true,
            resize: 'vertical',
            branding: false,
            readonly: false,
            mode: 'design',
            language: 'zh_CN', // 设置语言为中文
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount',
                'directionality', 'emoticons'
            ],
            toolbar: 'undo redo | styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media emoticons | table | code fullscreen preview | searchreplace | help',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; max-width: 100%; }',
            automatic_uploads: true,
            images_upload_url: '/upload-image.php',
            images_upload_credentials: true,
            relative_urls: false,
            convert_urls: true,
            remove_script_host: false,
            image_title: true,
            image_caption: true,
            image_advtab: true,
            init_instance_callback: function(editor) {
                console.log('编辑器初始化成功！');
                // 移除了setMode调用，因为当前TinyMCE版本不支持该方法
            },
            error_callback: function(error) {
                console.error('编辑器错误:', error);
                // 错误发生时，显示原始文本框
                contentEl.style.display = 'block';
                contentEl.classList.add('rich-text-area');
            },
            setup: function(editor) {
                // 监听表单提交，确保内容同步
                const form = contentEl.closest('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        if (tinymce.get('content')) {
                            contentEl.value = tinymce.get('content').getContent();
                        }
                    });
                }
            }
        });
    } catch (error) {
        console.error('编辑器初始化失败:', error);
        // 初始化失败，显示原始文本框
        contentEl.style.display = 'block';
        contentEl.classList.add('rich-text-area');
    }
}

// 页面加载完成后初始化编辑器
window.addEventListener('DOMContentLoaded', initEditor);
</script>

<!-- 富文本编辑器样式 -->
<style>
.rich-text-area {
    min-height: 400px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    padding: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    resize: vertical;
}

.rich-text-area:focus {
    border-color: #ec4899;
    box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
}
</style>

<?php
// 加载页脚
include __DIR__ . '/footer.php';
?>