<?php
/**
 * 后台用户管理页面
 */

// 设置页面标题和描述
$page_title = '用户管理';
$page_description = '管理网站注册用户';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';

// 引入数据库连接
require_once '../db.php';

// 处理用户删除请求
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    
    // 不能删除自己
    if ($user_id === $_SESSION['admin_id']) {
        $error = '不能删除当前登录的用户';
    } else {
        // 执行删除操作
        $sql = "DELETE FROM users WHERE id = ?";
        if (db_exec($sql, [$user_id])) {
            $success = '用户已成功删除';
        } else {
            $error = '删除用户失败';
        }
    }
}

// 获取用户列表
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;

// 构建查询条件
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(username LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role)) {
    $where[] = "role = ?";
    $params[] = $role;
}

$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// 获取总用户数
$count_sql = "SELECT COUNT(*) as total FROM users $where_sql";
$total = db_query_one($count_sql, $params)['total'];

// 计算分页
$total_pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

// 获取用户列表
$users_sql = "SELECT * FROM users $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$users = db_query($users_sql, $params);
?>
            <!-- 页面标题 -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500">
                        <?php echo $page_title; ?>
                    </h1>
                    <p class="text-gray-500 mt-1"><?php echo $page_description; ?></p>
                </div>
                <a href="user-edit.php" class="px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-lg hover:from-pink-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                    <i class="fas fa-plus mr-2"></i> 添加用户
                </a>
            </div>

            <!-- 消息提示 -->
            <?php if (isset($success)): ?>
                <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- 搜索和筛选 -->
            <div class="card p-6 mb-6">
                <form method="GET" action="users.php" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">搜索用户</label>
                        <div class="relative">
                            <input type="text" id="search" name="search" placeholder="用户名或邮箱" 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="w-40">
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">角色筛选</label>
                        <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                            <option value="">所有角色</option>
                            <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>管理员</option>
                            <option value="editor" <?php echo $role === 'editor' ? 'selected' : ''; ?>>编辑</option>
                            <option value="user" <?php echo $role === 'user' ? 'selected' : ''; ?>>普通用户</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-lg hover:from-pink-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            <i class="fas fa-filter mr-2"></i> 筛选
                        </button>
                    </div>
                </form>
            </div>

            <!-- 用户列表 -->
            <div class="card p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-pink-50 via-purple-50 to-pink-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    用户名
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    邮箱
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    角色
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    注册时间
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    状态
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fas fa-users text-4xl mb-2"></i>
                                        <p>暂无用户数据</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-100">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo $user['id']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <img class="h-8 w-8 rounded-full" 
                                                         src="<?php echo !empty($user['avatar']) ? $user['avatar'] : 'https://i.pravatar.cc/150?img=' . $user['id']; ?>" 
                                                         alt="<?php echo $user['username']; ?>">
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo $user['username']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo $user['email']; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 
                                                         ($user['role'] === 'editor' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                                <?php echo $user['role'] === 'admin' ? '管理员' : 
                                                     ($user['role'] === 'editor' ? '编辑' : '普通用户'); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                活跃
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="user-edit.php?id=<?php echo $user['id']; ?>" 
                                               class="text-pink-500 hover:text-pink-600 mr-4 transition-colors duration-200">
                                                <i class="fas fa-edit"></i> 编辑
                                            </a>
                                            <?php if ($user['id'] !== $_SESSION['admin_id']): ?>
                                                <button type="button" class="text-red-500 hover:text-red-600 transition-colors duration-200 delete-user-btn" 
                                                        data-user-id="<?php echo $user['id']; ?>" data-username="<?php echo $user['username']; ?>">
                                                    <i class="fas fa-trash"></i> 删除
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 分页 -->
                <?php if ($total_pages > 1): ?>
                    <div class="flex items-center justify-between mt-6">
                        <div class="text-sm text-gray-500">
                            显示 <?php echo count($users); ?> 条，共 <?php echo $total; ?> 条
                        </div>
                        <nav class="flex items-center space-x-1">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role) ? '&role=' . urlencode($role) : ''; ?>" 
                                   class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role) ? '&role=' . urlencode($role) : ''; ?>" 
                                   class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200 <?php echo $i === $page ? 'bg-gradient-to-r from-pink-500 to-purple-600 text-white border-transparent' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($role) ? '&role=' . urlencode($role) : ''; ?>" 
                                   class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 删除确认模态框 -->
            <div id="delete-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <div class="text-center mb-4">
                            <i class="fas fa-exclamation-triangle text-4xl text-yellow-500"></i>
                            <h3 class="text-xl font-bold mt-2">确认删除</h3>
                        </div>
                        <p class="text-center text-gray-600 mb-6">
                            您确定要删除用户 <span id="delete-username" class="font-medium"></span> 吗？
                            <br>
                            此操作不可恢复。
                        </p>
                        <form id="delete-form" method="POST" action="users.php" class="flex gap-3">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" id="delete-user-id">
                            <button type="button" id="cancel-delete" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                取消
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200">
                                删除
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- JavaScript -->
            <script>
                // 删除确认
                document.addEventListener('DOMContentLoaded', function() {
                    const deleteButtons = document.querySelectorAll('.delete-user-btn');
                    const deleteModal = document.getElementById('delete-modal');
                    const cancelDeleteBtn = document.getElementById('cancel-delete');
                    const deleteForm = document.getElementById('delete-form');
                    const deleteUsername = document.getElementById('delete-username');
                    const deleteUserId = document.getElementById('delete-user-id');

                    deleteButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const userId = this.getAttribute('data-user-id');
                            const username = this.getAttribute('data-username');
                            
                            deleteUserId.value = userId;
                            deleteUsername.textContent = username;
                            deleteModal.classList.remove('hidden');
                        });
                    });

                    cancelDeleteBtn.addEventListener('click', function() {
                        deleteModal.classList.add('hidden');
                    });

                    // 点击模态框外部关闭
                    window.addEventListener('click', function(e) {
                        if (e.target === deleteModal) {
                            deleteModal.classList.add('hidden');
                        }
                    });
                });
            </script>

<?php
// 包含公共底部
require_once 'footer.php';
