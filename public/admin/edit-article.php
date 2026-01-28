<?php
/**
 * 编辑文章页面
 */

// 包含认证中间件
require_once 'auth.php';

// 检查权限
require_permission('manage_articles');

// 设置页面标题和描述
$page_title = '编辑文章';
$page_description = '编辑或发布新文章';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';

// 获取所有分类
$categories = aniblog_get_categories();

// 获取文章ID（如果是编辑模式）
$article_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $article_id > 0;

// 初始化文章数据
$article = [
    'title' => '',
    'content' => '',
    'summary' => '',
    'cover_image' => '',
    'category_id' => 0
];

// 如果是编辑模式，获取文章数据
if ($is_edit) {
    $sql = "SELECT * FROM articles WHERE id = ?";
    $result = db_query($sql, [$article_id]);
    if (!empty($result)) {
        $article = $result[0];
    } else {
        // 文章不存在，重定向到文章列表
        header('Location: articles.php');
        exit;
    }
}

// 处理表单提交
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取表单数据
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $summary = isset($_POST['summary']) ? trim($_POST['summary']) : '';
    $category_id = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $cover_image = $article['cover_image']; // 默认使用现有图片
    
    // 验证表单数据
    if (empty($title)) {
        $error = '文章标题不能为空';
    } elseif (empty($content)) {
        $error = '文章内容不能为空';
    } else {
        // 处理文件上传（如果有）
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/article-covers/';
            
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
                // 处理标签
                $tags = isset($_POST['tags']) ? $_POST['tags'] : [];
                
                if ($is_edit) {
                    // 更新文章
                    $sql = "UPDATE articles SET title = ?, content = ?, summary = ?, cover_image = ?, category_id = ? WHERE id = ?";
                    if (db_exec($sql, [$title, $content, $summary, $cover_image, $category_id, $article_id])) {
                        // 更新标签
                        add_article_tags($article_id, $tags);
                        $success = '文章更新成功';
                    } else {
                        $error = '文章更新失败';
                    }
                } else {
                    // 添加新文章
                    $sql = "INSERT INTO articles (title, content, summary, cover_image, category_id, author_id) VALUES (?, ?, ?, ?, ?, ?)";
                    $author_id = 1; // 默认为管理员
                    if (db_exec($sql, [$title, $content, $summary, $cover_image, $category_id, $author_id])) {
                        $new_article_id = db_last_insert_id();
                        // 添加标签
                        add_article_tags($new_article_id, $tags);
                        $success = '文章添加成功';
                        // 重置表单
                        $article = [
                            'title' => '',
                            'content' => '',
                            'summary' => '',
                            'cover_image' => '',
                            'category_id' => 0
                        ];
                    } else {
                        $error = '文章添加失败';
                    }
                }
            }
    }
}
?>
            <!-- 文章编辑 -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold"><?php echo $is_edit ? '编辑文章' : '添加文章'; ?></h3>
                    <a href="articles.php" class="btn-primary px-4 py-2 rounded-lg text-white hover:bg-pink-600 transition-colors duration-200">
                        <i class="fas fa-list mr-2"></i>返回列表
                    </a>
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
                
                <!-- 文章表单 -->
                <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                    <!-- 文章标题 -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">文章标题</label>
                        <input type="text" id="title" name="title" required 
                               value="<?php echo htmlspecialchars($article['title']); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                    </div>
                    
                    <!-- 文章分类 -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">文章分类</label>
                        <select id="category_id" name="category_id" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="0">请选择分类</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" 
                                        <?php echo $article['category_id'] === $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo $cat['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- 文章标签 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">文章标签</label>
                        <div class="flex flex-wrap gap-2">
                            <?php 
                                $all_tags = get_all_tags();
                                $article_tags = [];
                                if ($is_edit) {
                                    $article_tags = get_article_tags($article_id);
                                    $article_tag_ids = array_column($article_tags, 'id');
                                }
                            ?>
                            <?php foreach ($all_tags as $tag): ?>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="tags[]" value="<?php echo $tag['id']; ?>" 
                                           <?php echo $is_edit && in_array($tag['id'], $article_tag_ids) ? 'checked' : ''; ?> 
                                           class="text-pink-500 focus:ring-pink-500 border-gray-300">
                                    <span class="text-sm text-gray-700"><?php echo $tag['name']; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- 文章封面 -->
                    <div>
                        <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-1">文章封面</label>
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-24 bg-gradient-to-r from-pink-300 to-purple-300 rounded-lg overflow-hidden">
                                <?php if (!empty($article['cover_image'])): ?>
                                    <img src="<?php echo $article['cover_image']; ?>" 
                                         alt="文章封面" 
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-white opacity-70">
                                        <i class="fas fa-image text-xl"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <input type="file" id="cover_image" name="cover_image" 
                                       accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200">
                                <p class="text-xs text-gray-500 mt-1">支持 JPG, PNG, GIF 格式，建议尺寸 800x400</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 文章摘要 -->
                    <div>
                        <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">文章摘要</label>
                        <textarea id="summary" name="summary" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <?php echo htmlspecialchars($article['summary']); ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">简要描述文章内容，建议 150-200 字</p>
                    </div>
                    
                    <!-- 文章内容 -->
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">文章内容</label>
                        <textarea id="content" name="content" rows="15" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <?php echo htmlspecialchars($article['content']); ?></textarea>
                        <p class="text-xs text-gray-500 mt-1">支持 HTML 格式，可以使用古腾堡编辑器样式</p>
                    </div>
                    
                    <!-- 提交按钮 -->
                <div class="flex justify-end gap-4">
                    <a href="articles.php" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                        取消
                    </a>
                    <button type="submit" 
                            class="btn-primary px-6 py-2 rounded-lg text-white hover:bg-pink-600 transition-colors duration-200">
                        <?php echo $is_edit ? '更新文章' : '发布文章'; ?>
                    </button>
                </div>
            </form>
        </div>

<!-- TinyMCE编辑器 -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<!-- 加载中文语言包 - 本地资源 -->
<script src="/assets/tinymce/langs/zh_CN.min.js"></script>
<script>
// 使用简化配置初始化编辑器
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
                'paste', 'directionality', 'emoticons'
            ],
            toolbar: 'undo redo | styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | 
                      bullist numlist outdent indent | link image media emoticons | table |
                      code fullscreen preview | searchreplace | help',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; max-width: 100%; }',
            automatic_uploads: true,
            images_upload_url: '/upload-image.php',
            images_upload_credentials: true,
            relative_urls: false,
            convert_urls: true,
            remove_script_host: false,
            paste_as_text: false,
            powerpaste_word_import: 'clean',
            powerpaste_html_import: 'clean',
            image_title: true,
            image_caption: true,
            image_advtab: true,
            init_instance_callback: function(editor) {
                console.log('编辑器初始化成功！');
                // 确保编辑器处于可编辑模式
                editor.setMode('design');
            },
            error_callback: function(error) {
                console.error('编辑器错误:', error);
                // 错误发生时，显示原始文本框
                contentEl.style.display = 'block';
                contentEl.classList.add('rich-text-area');
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
    min-height: 300px;
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
// 包含公共尾部
require_once 'footer.php';