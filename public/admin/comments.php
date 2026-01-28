<?php
/**
 * 评论管理页面
 */

// 包含认证中间件
require_once 'auth.php';

// 检查权限
require_permission('manage_comments');

// 设置页面标题和描述
$page_title = '评论管理';
$page_description = '管理网站的所有评论';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';

// 获取所有评论
$comments = get_all_comments();
$categories = aniblog_get_categories();
?>
            <!-- 评论管理 -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold">评论列表</h3>
                    <div class="flex items-center gap-3">
                        <button type="button" class="btn-secondary px-4 py-2 rounded-lg text-white hover:bg-gray-700 transition-colors duration-200">
                            <i class="fas fa-check-double mr-2"></i>批量审核
                        </button>
                        <button type="button" class="btn-danger px-4 py-2 rounded-lg text-white hover:bg-red-600 transition-colors duration-200">
                            <i class="fas fa-trash-alt mr-2"></i>批量删除
                        </button>
                    </div>
                </div>
                
                <!-- 筛选和搜索 -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <div class="flex items-center gap-2">
                        <label for="comment-status-filter" class="text-sm font-medium text-gray-700">状态：</label>
                        <select id="comment-status-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">全部</option>
                            <option value="approved">已审核</option>
                            <option value="pending">待审核</option>
                            <option value="spam">垃圾评论</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="搜索评论内容..." 
                                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- 评论列表 -->
                <div class="space-y-4">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="bg-white border border-gray-100 rounded-lg p-4 hover:shadow-md transition-shadow duration-300">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-pink-500 to-purple-500 flex items-center justify-center text-white text-sm font-medium flex-shrink-0">
                                        U
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <span class="text-sm font-medium">用户</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <a href="#" class="text-xs text-pink-500 hover:text-pink-600 transition-colors duration-200">
                                                <?php echo $comment['article_title'] ?? '未知文章'; ?>
                                            </a>
                                            <span class="inline-block text-xs px-2 py-1 rounded-full ml-auto <?php 
                                                echo $comment['status'] === 'approved' ? 'bg-green-100 text-green-700' : 
                                                     ($comment['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'); 
                                            ?>">
                                                <?php 
                                                    echo $comment['status'] === 'approved' ? '已审核' : 
                                                         ($comment['status'] === 'pending' ? '待审核' : '已拒绝'); 
                                                ?>
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 mb-4"><?php echo $comment['content']; ?></p>
                                        <div class="flex items-center gap-3">
                                            <button type="button" class="text-sm text-blue-500 hover:text-blue-600 transition-colors duration-200 flex items-center gap-1">
                                                <i class="fas fa-reply mr-1"></i>回复
                                            </button>
                                            <button type="button" class="text-sm text-green-500 hover:text-green-600 transition-colors duration-200 flex items-center gap-1 comment-moderate-btn" data-comment-id="<?php echo $comment['id']; ?>" data-status="approved">
                                                <i class="fas fa-check mr-1"></i>审核通过
                                            </button>
                                            <button type="button" class="text-sm text-yellow-500 hover:text-yellow-600 transition-colors duration-200 flex items-center gap-1 comment-moderate-btn" data-comment-id="<?php echo $comment['id']; ?>" data-status="pending">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>标记为待审核
                                            </button>
                                            <button type="button" class="text-sm text-red-500 hover:text-red-600 transition-colors duration-200 flex items-center gap-1 comment-moderate-btn" data-comment-id="<?php echo $comment['id']; ?>" data-status="rejected">
                                                <i class="fas fa-times mr-1"></i>拒绝
                                            </button>
                                            <button type="button" class="text-sm text-red-500 hover:text-red-600 transition-colors duration-200 flex items-center gap-1 ml-auto">
                                                <i class="fas fa-trash mr-1"></i>删除
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">暂无评论</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- 分页 -->
                <div class="flex items-center justify-center mt-8">
                    <nav class="inline-flex items-center space-x-2">
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                            1
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            2
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            3
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            ...
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            10
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </nav>
                </div>
            </div>

<script>
    // 评论审核功能
    const moderateBtns = document.querySelectorAll('.comment-moderate-btn');
    
    moderateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentCard = this.closest('.bg-white');
            const commentId = this.getAttribute('data-comment-id');
            const status = this.getAttribute('data-status');
            const statusSpan = commentCard.querySelector('.rounded-full');
            
            if (!commentId || !status) {
                showAlert('无效的评论ID或状态', 'error');
                return;
            }
            
            // 显示加载状态
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> 处理中...';
            
            // 发送审核请求
            fetch('/comment-moderate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'comment_id=' + commentId + '&status=' + status
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('网络响应错误');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // 更新状态显示
                    if (data.status === 'approved') {
                        statusSpan.className = 'inline-block text-xs px-2 py-1 rounded-full ml-auto bg-green-100 text-green-700';
                        statusSpan.textContent = '已审核';
                    } else if (data.status === 'pending') {
                        statusSpan.className = 'inline-block text-xs px-2 py-1 rounded-full ml-auto bg-yellow-100 text-yellow-700';
                        statusSpan.textContent = '待审核';
                    } else {
                        statusSpan.className = 'inline-block text-xs px-2 py-1 rounded-full ml-auto bg-red-100 text-red-700';
                        statusSpan.textContent = '已拒绝';
                    }
                    
                    // 添加成功提示
                    showAlert('评论状态已更新', 'success');
                } else {
                    showAlert('更新失败: ' + (data.message || '未知错误'), 'error');
                }
            })
            .catch(error => {
                console.error('审核请求失败:', error);
                showAlert('审核失败，请检查网络连接或重试', 'error');
            })
            .finally(() => {
                // 恢复按钮状态
                btn.disabled = false;
                if (status === 'approved') {
                    btn.innerHTML = '<i class="fas fa-check mr-1"></i>审核通过';
                } else if (status === 'pending') {
                    btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>标记为待审核';
                } else {
                    btn.innerHTML = '<i class="fas fa-times mr-1"></i>拒绝';
                }
            });
        });
    });
    
    // 删除评论功能
    const deleteBtns = document.querySelectorAll('.text-red-500 .fas.fa-trash').closest('button');
    
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentCard = this.closest('.bg-white');
            const commentId = commentCard.querySelector('.comment-moderate-btn')?.getAttribute('data-comment-id');
            
            if (!commentId) {
                showAlert('无法获取评论ID', 'error');
                return;
            }
            
            if (confirm('确定要删除这条评论吗？')) {
                // 发送删除请求
                fetch('/comment-delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'comment_id=' + commentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 移除评论卡片
                        commentCard.remove();
                        showAlert('评论已删除', 'success');
                    } else {
                        showAlert('删除失败: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('删除请求失败:', error);
                    showAlert('删除失败，请稍后重试', 'error');
                });
            }
        });
    });
    
    // 回复评论功能
    const replyBtns = document.querySelectorAll('.text-blue-500 .fas.fa-reply').closest('button');
    
    replyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const commentCard = this.closest('.bg-white');
            const commentId = commentCard.querySelector('.comment-moderate-btn')?.getAttribute('data-comment-id');
            
            if (!commentId) {
                showAlert('无法获取评论ID', 'error');
                return;
            }
            
            // 检查是否已经存在回复输入框
            let replyContainer = commentCard.querySelector('.reply-container');
            if (replyContainer) {
                replyContainer.remove();
                return;
            }
            
            // 创建回复输入框容器
            replyContainer = document.createElement('div');
            replyContainer.className = 'reply-container mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200';
            replyContainer.innerHTML = `
                <div class="flex flex-col gap-3">
                    <div>
                        <label for="reply-content-${commentId}" class="block text-sm font-medium text-gray-700 mb-1">回复内容:</label>
                        <textarea id="reply-content-${commentId}" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="请输入回复内容"></textarea>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" class="reply-cancel-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">取消</button>
                        <button type="button" class="reply-submit-btn px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg hover:from-pink-600 hover:to-purple-600 transition-colors duration-200" data-comment-id="${commentId}">提交回复</button>
                    </div>
                </div>
            `;
            
            // 插入到评论内容下方
            const commentContent = commentCard.querySelector('.text-sm.text-gray-700');
            commentContent.parentNode.insertBefore(replyContainer, commentContent.nextSibling);
            
            // 取消回复按钮事件
            const cancelBtn = replyContainer.querySelector('.reply-cancel-btn');
            cancelBtn.addEventListener('click', function() {
                replyContainer.remove();
            });
            
            // 提交回复按钮事件
            const submitBtn = replyContainer.querySelector('.reply-submit-btn');
            submitBtn.addEventListener('click', function() {
                const replyContent = document.getElementById(`reply-content-${commentId}`).value.trim();
                
                if (!replyContent) {
                    showAlert('回复内容不能为空', 'error');
                    return;
                }
                
                // 这里可以添加提交回复的逻辑
                submitReply(commentId, replyContent, replyContainer);
            });
        });
    });
    
    // 提交回复函数
    function submitReply(commentId, replyContent, replyContainer) {
        // 发送回复请求
        fetch('/comment-reply.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'comment_id=' + commentId + '&content=' + encodeURIComponent(replyContent)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 显示成功提示
                showAlert('回复成功', 'success');
                
                // 移除回复输入框
                replyContainer.remove();
                
                // 重新加载页面，显示最新回复
                // 实际项目中，这里可以使用AJAX动态添加回复，而不需要刷新页面
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('回复失败: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('回复请求失败:', error);
            showAlert('回复失败，请稍后重试', 'error');
        });
    }
    
    // 批量审核按钮
    const bulkApproveBtn = document.querySelector('.btn-secondary');
    if (bulkApproveBtn) {
        bulkApproveBtn.addEventListener('click', function() {
            showAlert('批量审核功能将在后续版本实现', 'info');
        });
    }
    
    // 批量删除按钮
    const bulkDeleteBtn = document.querySelector('.btn-danger');
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            if (confirm('确定要批量删除选中的评论吗？')) {
                showAlert('批量删除功能将在后续版本实现', 'info');
            }
        });
    }
    
    // 状态筛选功能
    const statusFilter = document.getElementById('comment-status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const status = this.value;
            const commentCards = document.querySelectorAll('.bg-white');
            
            commentCards.forEach(card => {
                const statusSpan = card.querySelector('.rounded-full');
                const cardStatus = statusSpan.textContent;
                
                let showCard = true;
                if (status === 'approved' && cardStatus !== '已审核') {
                    showCard = false;
                } else if (status === 'pending' && cardStatus !== '待审核') {
                    showCard = false;
                } else if (status === 'rejected' && cardStatus !== '已拒绝') {
                    showCard = false;
                }
                
                card.style.display = showCard ? 'block' : 'none';
            });
        });
    }
    
    // 成功/错误提示函数
    function showAlert(message, type = 'success') {
        const alert = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-100' : type === 'error' ? 'bg-red-100' : 'bg-blue-100';
        const textColor = type === 'success' ? 'text-green-800' : type === 'error' ? 'text-red-800' : 'text-blue-800';
        
        alert.className = `fixed top-4 right-4 px-4 py-2 ${bgColor} ${textColor} rounded-lg shadow-lg z-50`;
        alert.textContent = message;
        document.body.appendChild(alert);
        
        setTimeout(() => {
            alert.remove();
        }, 2000);
    }
</script>
<?php
// 包含公共尾部
require_once 'footer.php';
