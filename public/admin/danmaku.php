<?php
/**
 * 弹幕管理页面
 */

// 包含认证中间件
require_once 'auth.php';

// 设置页面标题和描述
$page_title = '弹幕管理';
$page_description = '管理网站的弹幕内容';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';

// 处理弹幕删除
if (isset($_POST['delete_danmaku'])) {
    $danmaku_id = $_POST['danmaku_id'];
    $sql = "DELETE FROM danmaku WHERE id = ?";
    db_exec($sql, [$danmaku_id]);
    $message = '弹幕已删除';
}

// 处理弹幕审核
if (isset($_POST['approve_danmaku']) || isset($_POST['reject_danmaku'])) {
    $danmaku_id = $_POST['danmaku_id'];
    $status = isset($_POST['approve_danmaku']) ? 'approved' : 'rejected';
    $sql = "UPDATE danmaku SET status = ? WHERE id = ?";
    db_exec($sql, [$status, $danmaku_id]);
    $message = '弹幕已' . ($status === 'approved' ? '通过' : '拒绝');
}

// 处理批量操作
if (isset($_POST['bulk_approve']) || isset($_POST['bulk_reject']) || isset($_POST['bulk_delete'])) {
    $danmaku_ids = $_POST['danmaku_ids'];
    if (!empty($danmaku_ids)) {
        $placeholders = implode(',', array_fill(0, count($danmaku_ids), '?'));
        
        if (isset($_POST['bulk_approve'])) {
            $sql = "UPDATE danmaku SET status = 'approved' WHERE id IN ($placeholders)";
            db_exec($sql, $danmaku_ids);
            $message = '选中的弹幕已通过';
        } elseif (isset($_POST['bulk_reject'])) {
            $sql = "UPDATE danmaku SET status = 'rejected' WHERE id IN ($placeholders)";
            db_exec($sql, $danmaku_ids);
            $message = '选中的弹幕已拒绝';
        } else {
            $sql = "DELETE FROM danmaku WHERE id IN ($placeholders)";
            db_exec($sql, $danmaku_ids);
            $message = '选中的弹幕已删除';
        }
    }
}

// 获取筛选参数
$article_id = isset($_GET['article_id']) ? (int)$_GET['article_id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// 构建查询条件
$where_clauses = [];
$params = [];

if ($article_id > 0) {
    $where_clauses[] = 'article_id = ?';
    $params[] = $article_id;
}

if (!empty($search)) {
    $where_clauses[] = 'content LIKE ?';
    $params[] = '%' . $search . '%';
}

if (!empty($status)) {
    $where_clauses[] = 'status = ?';
    $params[] = $status;
}

$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// 获取弹幕列表
$sql = "SELECT d.*, a.title as article_title, u.username FROM danmaku d LEFT JOIN articles a ON d.article_id = a.id LEFT JOIN users u ON d.user_id = u.id $where_sql ORDER BY d.created_at DESC";
$danmakus = db_query($sql, $params);

// 获取文章列表用于筛选
$articles_sql = "SELECT id, title FROM articles ORDER BY created_at DESC";
$articles = db_query($articles_sql);
?>
            <!-- 弹幕管理 -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold">弹幕管理</h3>
                </div>
                
                <?php if (isset($message)): ?>
                    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- 筛选和搜索 -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <label for="article_filter" class="block text-sm font-medium text-gray-700 mb-1">按文章筛选</label>
                            <select id="article_filter" name="article_id" 
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="0">所有文章</option>
                                <?php foreach ($articles as $article): ?>
                                    <option value="<?php echo $article['id']; ?>" <?php echo $article_id === $article['id'] ? 'selected' : ''; ?>>
                                        <?php echo $article['title']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="flex-1 min-w-[200px]">
                            <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-1">按状态筛选</label>
                            <select id="status_filter" name="status" 
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                                <option value="">所有状态</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>待审核</option>
                                <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>已通过</option>
                                <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>已拒绝</option>
                            </select>
                        </div>
                        
                        <div class="flex-1 min-w-[200px]">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">搜索内容</label>
                            <div class="relative">
                                <input type="text" id="search" name="search" value="<?php echo $search; ?>" 
                                       class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent" placeholder="搜索弹幕内容...">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="btn-primary px-6 py-2 rounded-lg text-white font-medium">
                                <i class="fas fa-filter mr-2"></i>筛选
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- 批量操作 -->
                <form method="POST" class="mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="select_all" class="form-checkbox text-pink-500">
                            <label for="select_all" class="text-sm font-medium">全选</label>
                            <div class="flex gap-2 ml-4">
                                <button type="submit" name="bulk_approve" class="btn-success px-4 py-2 rounded-lg text-white font-medium text-sm">
                                    <i class="fas fa-check mr-2"></i>批量通过
                                </button>
                                <button type="submit" name="bulk_reject" class="btn-warning px-4 py-2 rounded-lg text-white font-medium text-sm">
                                    <i class="fas fa-times mr-2"></i>批量拒绝
                                </button>
                                <button type="submit" name="bulk_delete" class="btn-danger px-4 py-2 rounded-lg text-white font-medium text-sm">
                                    <i class="fas fa-trash mr-2"></i>批量删除
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 弹幕列表 -->
                    <div class="overflow-x-auto mt-4">
                        <table class="min-w-full bg-white rounded-lg overflow-hidden">
                            <thead class="bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                                <tr>
                                    <th class="py-3 px-4 text-left text-sm font-medium">
                                        <input type="checkbox" id="select_all_header" class="form-checkbox">
                                    </th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">内容</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">文章</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">用户</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">颜色</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">大小</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">模式</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">状态</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">发送时间</th>
                                    <th class="py-3 px-4 text-left text-sm font-medium">操作</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php if (empty($danmakus)): ?>
                                    <tr>
                                        <td colspan="9" class="py-8 text-center text-gray-500">
                                            <i class="fas fa-comment-slash text-4xl mb-2"></i>
                                            <p>暂无弹幕数据</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($danmakus as $danmaku): ?>
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="py-3 px-4">
                                                <input type="checkbox" name="danmaku_ids[]" value="<?php echo $danmaku['id']; ?>" class="form-checkbox text-pink-500">
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                <div class="max-w-xs truncate" title="<?php echo $danmaku['content']; ?>">
                                                    <?php echo $danmaku['content']; ?>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                <?php if ($danmaku['article_title']): ?>
                                                    <a href="../article.php?id=<?php echo $danmaku['article_id']; ?>" target="_blank" class="text-pink-500 hover:underline">
                                                        <?php echo substr($danmaku['article_title'], 0, 20); ?><?php echo strlen($danmaku['article_title']) > 20 ? '...' : ''; ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-gray-400">已删除文章</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                <?php echo $danmaku['username'] ? $danmaku['username'] : '匿名用户'; ?>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex items-center">
                                                    <div class="w-4 h-4 rounded-full" style="background-color: <?php echo $danmaku['color']; ?>"></div>
                                                    <span class="ml-2 text-sm"><?php echo $danmaku['color']; ?></span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-sm"><?php echo $danmaku['size']; ?>px</td>
                                            <td class="py-3 px-4 text-sm">
                                                <?php 
                                                $mode_map = [
                                                    'scroll' => '滚动',
                                                    'top' => '顶部',
                                                    'bottom' => '底部'
                                                ];
                                                echo $mode_map[$danmaku['mode']];
                                                ?>
                                            </td>
                                            <td class="py-3 px-4">
                                                <?php
                                                $status_map = [
                                                    'pending' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">待审核</span>',
                                                    'approved' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">已通过</span>',
                                                    'rejected' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">已拒绝</span>'
                                                ];
                                                echo $status_map[$danmaku['status']];
                                                ?>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-500">
                                                <?php echo date('Y-m-d H:i:s', strtotime($danmaku['created_at'])); ?>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex gap-1">
                                                    <?php if ($danmaku['status'] === 'pending'): ?>
                                                        <form method="POST" class="inline">
                                                            <input type="hidden" name="danmaku_id" value="<?php echo $danmaku['id']; ?>">
                                                            <button type="submit" name="approve_danmaku" class="btn-success px-3 py-1 rounded-lg text-white font-medium text-xs">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" class="inline">
                                                            <input type="hidden" name="danmaku_id" value="<?php echo $danmaku['id']; ?>">
                                                            <button type="submit" name="reject_danmaku" class="btn-warning px-3 py-1 rounded-lg text-white font-medium text-xs">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <form method="POST" class="inline">
                                                        <input type="hidden" name="danmaku_id" value="<?php echo $danmaku['id']; ?>">
                                                        <button type="submit" name="delete_danmaku" class="btn-danger px-3 py-1 rounded-lg text-white font-medium text-xs">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>

<script>
    // 全选功能
    document.getElementById('select_all').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="danmaku_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    document.getElementById('select_all_header').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('input[name="danmaku_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        document.getElementById('select_all').checked = this.checked;
    });
</script>

<?php
// 包含公共尾部
require_once 'footer.php';
