<?php
/**
 * 文章管理页面
 */

// 包含认证中间件
require_once 'auth.php';

// 检查权限
require_permission('manage_articles');

// 设置页面标题和描述
$page_title = '文章管理';
$page_description = '管理网站的所有文章';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';

// 获取当前页码
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// 获取文章总数
$total_sql = "SELECT COUNT(*) as count FROM articles";
$total_result = db_query_one($total_sql);
$total_articles = $total_result['count'];
$total_pages = ceil($total_articles / $per_page);

// 获取分页文章
$articles_sql = "SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
$articles = db_query($articles_sql, [$per_page, $offset]);

// 获取所有分类
$categories = aniblog_get_categories();
?>
            <!-- 文章管理 -->
            <div class="card p-6">
                <!-- 删除确认模态框 -->
                <div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-xl p-6 w-full max-w-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold">确认删除</h3>
                            <button id="closeDeleteModal" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <div class="mb-6">
                            <p class="text-gray-600">您确定要删除文章 <span id="deleteArticleTitle" class="font-medium text-pink-500"></span> 吗？</p>
                            <p class="text-xs text-gray-500 mt-2">此操作不可恢复，请谨慎操作。</p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" id="confirmDelete" class="flex-1 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>删除
                            </button>
                            <button type="button" id="cancelDelete" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                取消
                            </button>
                        </div>
                        <input type="hidden" id="deleteArticleId">
                    </div>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold">文章列表</h3>
                    <a href="edit-article.php" class="btn-primary px-4 py-2 rounded-lg text-white hover:bg-pink-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>添加文章
                    </a>
                </div>
                
                <!-- 筛选和搜索 -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <div class="flex items-center gap-2">
                        <label for="category-filter" class="text-sm font-medium text-gray-700">分类：</label>
                        <select id="category-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">全部</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <label for="status-filter" class="text-sm font-medium text-gray-700">状态：</label>
                        <select id="status-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">全部</option>
                            <option value="published">已发布</option>
                            <option value="draft">草稿</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="搜索文章标题..." 
                                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- 文章列表 -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">#</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">标题</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">分类</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">作者</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">发布时间</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">阅读量</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">评论数</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">状态</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-600">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($articles)): ?>
                                <?php foreach ($articles as $article): ?>
                                    <tr class="border-b border-gray-100 hover:bg-pink-50/50 transition-colors duration-200">
                                        <td class="py-3 px-4 text-sm text-gray-600"><?php echo $article['id']; ?></td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gradient-to-r from-pink-300 to-purple-300 rounded-lg overflow-hidden flex-shrink-0">
                                                    <?php if (!empty($article['cover_image'])): ?>
                                                        <img src="<?php echo $article['cover_image']; ?>" alt="<?php echo $article['title']; ?>" class="w-full h-full object-cover">
                                                    <?php else: ?>
                                                        <div class="w-full h-full flex items-center justify-center text-white opacity-70">
                                                            <i class="fas fa-image text-sm"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="text-sm font-medium hover:text-pink-500 transition-colors duration-200"><?php echo $article['title']; ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <?php echo $article['category_name'] ?? '未分类'; ?>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <?php echo $article['author_id'] == 1 ? '管理员' : '未知作者'; ?>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <?php echo date('Y-m-d', strtotime($article['created_at'])); ?>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <i class="far fa-eye mr-1 text-gray-400"></i><?php echo $article['view_count']; ?>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-600">
                                            <i class="far fa-comment mr-1 text-gray-400"></i><?php echo $article['comment_count']; ?>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-block text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">已发布</span>
                                        </td>
                                        <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                    <a href="edit-article.php?id=<?php echo $article['id']; ?>" class="text-gray-500 hover:text-blue-500 transition-colors duration-200">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../article.php?id=<?php echo $article['id']; ?>" target="_blank" class="text-gray-500 hover:text-green-500 transition-colors duration-200">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="text-gray-500 hover:text-red-500 transition-colors duration-200 delete-article-btn" 
                                            data-article-id="<?php echo $article['id']; ?>" 
                                            data-article-title="<?php echo htmlspecialchars($article['title']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="py-8 text-center">
                                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-500">暂无文章</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- 分页 -->
                <div class="flex items-center justify-center mt-8">
                    <nav class="inline-flex items-center space-x-2">
                        <!-- 上一页 -->
                        <a href="?page=<?php echo max(1, $page - 1); ?>" 
                           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors <?php echo $page <= 1 ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        
                        <!-- 页码 -->
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="px-4 py-2 rounded-lg <?php echo $i === $page ? 'bg-gradient-to-r from-pink-500 to-purple-500 text-white' : 'border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <!-- 省略号 -->
                        <?php if ($page + 2 < $total_pages): ?>
                            <span class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">...</span>
                            <a href="?page=<?php echo $total_pages; ?>" 
                               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                                <?php echo $total_pages; ?>
                            </a>
                        <?php endif; ?>
                        
                        <!-- 下一页 -->
                        <a href="?page=<?php echo min($total_pages, $page + 1); ?>" 
                           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors <?php echo $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </nav>
                </div>
            </div>

<script>
    // 删除文章功能
    const deleteModal = document.getElementById('deleteModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const deleteArticleTitle = document.getElementById('deleteArticleTitle');
    const deleteArticleId = document.getElementById('deleteArticleId');
    
    // 打开删除确认模态框
    const deleteArticleBtns = document.querySelectorAll('.delete-article-btn');
    deleteArticleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const articleId = btn.dataset.articleId;
            const articleTitle = btn.dataset.articleTitle;
            
            // 设置删除信息
            deleteArticleId.value = articleId;
            deleteArticleTitle.textContent = articleTitle;
            
            // 显示模态框
            deleteModal.classList.remove('hidden');
        });
    });
    
    // 关闭删除模态框
    const closeDeleteModal = () => {
        deleteModal.classList.add('hidden');
    };
    
    closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
    cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    
    // 点击模态框外部关闭
    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
    
    // 确认删除
    confirmDeleteBtn.addEventListener('click', () => {
        const articleId = deleteArticleId.value;
        
        // 显示加载状态
        const originalText = confirmDeleteBtn.innerHTML;
        confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>删除中...';
        confirmDeleteBtn.disabled = true;
        
        // 发送AJAX请求
        fetch('article-delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'article_id': articleId
            }).toString()
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // 恢复按钮状态
            confirmDeleteBtn.innerHTML = originalText;
            confirmDeleteBtn.disabled = false;
            
            if (data.success) {
                // 显示成功消息
                alert('文章已成功删除！');
                closeDeleteModal();
                // 刷新页面以显示更新后的文章列表
                location.reload();
            } else {
                // 显示错误消息
                alert('删除失败：' + data.message);
            }
        })
        .catch(error => {
            // 恢复按钮状态
            confirmDeleteBtn.innerHTML = originalText;
            confirmDeleteBtn.disabled = false;
            
            console.error('删除请求失败:', error);
            alert('删除失败，请稍后重试：' + error.message);
        });
    });
</script>
<?php
// 包含公共尾部
require_once 'footer.php';
