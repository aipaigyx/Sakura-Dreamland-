<?php
/**
 * 后台用户编辑页面
 */

// 包含功能函数
require_once '../functions.php';

// 引入数据库连接
require_once '../db.php';

// 获取用户ID（如果是编辑模式）
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $user_id > 0;

// 如果是编辑模式，获取用户数据
$user = null;
if ($is_edit) {
    $sql = "SELECT * FROM users WHERE id = ?";
    $user = db_query_one($sql, [$user_id]);
    
    if (!$user) {
        $error = '用户不存在';
        $is_edit = false;
    }
}

// 设置页面标题和描述
$page_title = $is_edit ? '编辑用户' : '添加用户';
$page_description = '添加或编辑网站用户';

// 包含公共头部
require_once 'header.php';

// 表单提交处理 - 放在所有输出之后，使用输出缓冲
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // 获取当前用户的头像
    $avatar = $is_edit ? $user['avatar'] : '';
    
    // 处理头像上传或从图库选择
    if (isset($_POST['avatar_from_gallery']) && !empty(trim($_POST['avatar_from_gallery']))) {
        // 从图库选择的头像
        $gallery_avatar = trim($_POST['avatar_from_gallery']);
        
        // 验证图片路径格式
        if (strpos($gallery_avatar, '/uploads/images/') === 0) {
            // 删除旧头像（如果存在且是上传的头像，不是默认头像）
            if ($is_edit && !empty($user['avatar']) && strpos($user['avatar'], '/uploads/avatars/') === 0 && file_exists(__DIR__ . '/..' . $user['avatar'])) {
                unlink(__DIR__ . '/..' . $user['avatar']);
            }
            $avatar = $gallery_avatar;
        }
    } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        // 文件上传的头像
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['avatar']['type'], $allowed_types)) {
            $error = '只允许上传 JPG、PNG 或 GIF 格式的图片';
        } elseif ($_FILES['avatar']['size'] > $max_size) {
            $error = '图片大小不能超过 5MB';
        } else {
            // 创建上传目录
            $upload_dir = __DIR__ . '/../uploads/avatars/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // 生成唯一文件名
            $file_ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $new_filename = 'avatar_' . ($is_edit ? $user_id : time()) . '_' . uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $new_filename;
            
            // 移动上传文件
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
                // 删除旧头像
                if ($is_edit && !empty($user['avatar']) && strpos($user['avatar'], '/uploads/avatars/') === 0 && file_exists(__DIR__ . '/..' . $user['avatar'])) {
                    unlink(__DIR__ . '/..' . $user['avatar']);
                }
                $avatar = '/uploads/avatars/' . $new_filename;
            } else {
                $error = '头像上传失败，请稍后重试';
            }
        }
    }
    
    // 验证表单
    $errors = [];
    
    if (empty($username)) {
        $errors[] = '用户名不能为空';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = '用户名长度必须在3-50个字符之间';
    }
    
    if (empty($email)) {
        $errors[] = '邮箱不能为空';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '请输入有效的邮箱地址';
    }
    
    if (!in_array($role, ['admin', 'editor', 'user'])) {
        $errors[] = '无效的角色';
    }
    
    // 检查用户名是否已存在
    $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $existing_user = db_query_one($sql, [$username, $user_id]);
    if ($existing_user) {
        $errors[] = '用户名已存在';
    }
    
    // 检查邮箱是否已存在
    $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $existing_email = db_query_one($sql, [$email, $user_id]);
    if ($existing_email) {
        $errors[] = '邮箱已被注册';
    }
    
    // 密码验证（仅在添加或修改密码时需要）
    if (!empty($password) || !$is_edit) {
        if (empty($password)) {
            $errors[] = '密码不能为空';
        } elseif (strlen($password) < 6) {
            $errors[] = '密码长度不能少于6个字符';
        } elseif ($password !== $confirm_password) {
            $errors[] = '两次输入的密码不一致';
        }
    }
    
    // 如果没有错误，执行保存操作
    if (empty($errors)) {
        try {
            if ($is_edit) {
                // 更新现有用户
                if (!empty($password)) {
                    // 更新密码
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET username = ?, email = ?, password = ?, role = ?, avatar = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                    $result = db_exec($sql, [$username, $email, $hashed_password, $role, $avatar, $user_id]);
                } else {
                    // 不更新密码
                    $sql = "UPDATE users SET username = ?, email = ?, role = ?, avatar = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                    $result = db_exec($sql, [$username, $email, $role, $avatar, $user_id]);
                }
                
                if ($result !== false) {
                    $success = '用户信息已成功更新';
                    // 刷新用户数据
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $user = db_query_one($sql, [$user_id]);
                } else {
                    $error = '更新用户信息失败';
                }
            } else {
                // 添加新用户
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, email, password, role, avatar, created_at, updated_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                $result = db_exec($sql, [$username, $email, $hashed_password, $role, $avatar]);
                
                if ($result !== false) {
                    $success = '用户已成功添加';
                    // 使用JavaScript重定向，避免header()函数问题
                    echo "<script>window.location.href = 'users.php?success=" . urlencode($success) . "';</script>";
                    exit;
                } else {
                    $error = '添加用户失败';
                }
            }
        } catch (Exception $e) {
            $error = '操作失败: ' . $e->getMessage();
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
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

            <!-- 图片选择模态框 -->
            <div id="imageSelectorModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden" style="display: none !important;">
                <div class="bg-white rounded-xl p-6 w-full max-w-4xl max-h-[80vh] overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold">从图库选择头像</h3>
                        <button id="closeImageSelector" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <!-- 图片搜索 -->
                    <div class="mb-4">
                        <div class="relative">
                            <input type="text" id="imageSearch" placeholder="搜索图片标题..." 
                                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- 图片网格 -->
                    <div class="flex-1 overflow-y-auto" id="imageGrid">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                            <?php 
                            // 获取所有已上传的图片
                            $sql = "SELECT * FROM images ORDER BY created_at DESC"; 
                            $images = db_query($sql);
                            if (!empty($images)): 
                                foreach ($images as $image): 
                            ?>
                                <div class="group relative cursor-pointer hover:ring-2 hover:ring-pink-500 rounded-lg overflow-hidden transition-all duration-200" 
                                     data-image-path="<?php echo $image['file_path']; ?>">
                                    <div class="aspect-square bg-gradient-to-r from-pink-300 to-purple-300">
                                        <img src="<?php echo $image['file_path']; ?>" alt="<?php echo $image['title']; ?>" 
                                             class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-2">
                                        <p class="text-xs text-white truncate"><?php echo $image['title']; ?></p>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                                <div class="col-span-full text-center py-12">
                                    <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500">暂无图片，请先上传图片</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-4 gap-3">
                        <button type="button" id="cancelImageSelection" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                            取消
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- 用户表单 -->
            <div class="card p-6">
                <form method="POST" action="user-edit.php<?php echo $is_edit ? '?id=' . $user_id : ''; ?>" enctype="multipart/form-data">
                    <input type="hidden" name="avatar_from_gallery" id="avatar_from_gallery" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 基本信息 -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-700">基本信息</h3>
                            
                            <!-- 用户头像 -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">用户头像</label>
                                <div class="flex items-center gap-6">
                                    <!-- 头像预览 -->
                                    <div class="relative">
                                        <img src="<?php echo $is_edit && !empty($user['avatar']) ? $user['avatar'] : 'https://i.pravatar.cc/150?img=' . ($is_edit ? $user['id'] : 1); ?>" 
                                             alt="用户头像" 
                                             id="avatarPreview"
                                             class="w-20 h-20 rounded-full object-cover border-4 border-pink-500 shadow-lg">
                                        <!-- 相机图标 - 用于本地文件上传 -->
                                        <div class="absolute bottom-0 right-0 w-8 h-8 z-10">
                                            <label for="avatar" class="w-full h-full p-1 bg-pink-500 text-white rounded-full shadow-lg hover:bg-pink-600 transition-colors duration-200 cursor-pointer flex items-center justify-center">
                                                <i class="fas fa-camera text-sm"></i>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- 本地文件上传输入（隐藏） -->
                                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/gif" 
                                           class="hidden">
                                    
                                    <!-- 头像操作按钮 -->
                                    <div class="space-y-3">
                                        <p class="text-sm text-gray-500">设置用户头像</p>
                                        
                                        <!-- 从图库选择按钮 -->
                                        <button type="button" id="selectFromGallery" 
                                                class="inline-flex items-center justify-center px-5 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 text-sm transition-all duration-200 shadow-md hover:shadow-lg">
                                            <i class="fas fa-images mr-2"></i>从图库选择
                                        </button>
                                        
                                        <!-- 本地上传按钮 -->
                                        <button type="button" id="uploadLocalBtn" 
                                                class="inline-flex items-center justify-center px-5 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 text-sm transition-all duration-200 shadow-md hover:shadow-lg">
                                            <i class="fas fa-upload mr-2"></i>本地上传
                                        </button>
                                        
                                        <p class="text-xs text-gray-400 mt-1">支持 JPG、PNG、GIF 格式，最大 5MB</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 用户名 -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">用户名 <span class="text-red-500">*</span></label>
                                <input type="text" id="username" name="username" required 
                                       value="<?php echo $is_edit ? htmlspecialchars($user['username']) : ''; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                                <p class="mt-1 text-xs text-gray-500">3-50个字符，用于登录和显示</p>
                            </div>
                            
                            <!-- 邮箱 -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">邮箱 <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" required 
                                       value="<?php echo $is_edit ? htmlspecialchars($user['email']) : ''; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                                <p class="mt-1 text-xs text-gray-500">用于找回密码和接收通知</p>
                            </div>
                            
                            <!-- 角色 -->
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">角色 <span class="text-red-500">*</span></label>
                                <select id="role" name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                                    <option value="admin" <?php echo $is_edit && $user['role'] === 'admin' ? 'selected' : ''; ?>>管理员</option>
                                    <option value="editor" <?php echo $is_edit && $user['role'] === 'editor' ? 'selected' : ''; ?>>编辑</option>
                                    <option value="user" <?php echo ($is_edit && $user['role'] === 'user') || !$is_edit ? 'selected' : ''; ?>>普通用户</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">决定用户的权限级别</p>
                            </div>
                        </div>
                        
                        <!-- 密码设置 -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-700">密码设置</h3>
                            
                            <!-- 密码 -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    密码 <?php echo $is_edit ? '' : '<span class="text-red-500">*</span>'; ?>
                                </label>
                                <input type="password" id="password" name="password" 
                                       placeholder="<?php echo $is_edit ? '不填则保持不变' : '至少6个字符'; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                                <p class="mt-1 text-xs text-gray-500">
                                    <?php echo $is_edit ? '修改密码时填写，至少6个字符' : '至少6个字符，用于登录'; ?>
                                </p>
                            </div>
                            
                            <!-- 确认密码 -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    确认密码 <?php echo $is_edit ? '' : '<span class="text-red-500">*</span>'; ?>
                                </label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       placeholder="<?php echo $is_edit ? '不填则保持不变' : '再次输入密码'; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-pink-500 focus:border-transparent transition-all duration-200">
                                <p class="mt-1 text-xs text-gray-500">请再次输入密码</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 提交按钮 -->
                    <div class="mt-8 flex items-center justify-between">
                        <button type="button" onclick="window.history.back()" 
                                class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            取消
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-lg hover:from-pink-600 hover:to-purple-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i> <?php echo $is_edit ? '更新用户' : '添加用户'; ?>
                        </button>
                    </div>
                </form>
            </div>

<?php
// 包含公共底部
require_once 'footer.php';
?>

<script>
// 确保DOM加载完成后执行JavaScript代码
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM content loaded, initializing image selector...');
    
    // 图片选择模态框功能
    const imageSelectorModal = document.getElementById('imageSelectorModal');
    const selectFromGalleryBtn = document.getElementById('selectFromGallery');
    const closeImageSelectorBtn = document.getElementById('closeImageSelector');
    const cancelImageSelectionBtn = document.getElementById('cancelImageSelection');
    const avatarFromGalleryInput = document.getElementById('avatar_from_gallery');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarFileInput = document.getElementById('avatar');
    
    console.log('Elements found:');
    console.log('- Modal:', imageSelectorModal);
    console.log('- Gallery button:', selectFromGalleryBtn);
    console.log('- Close button:', closeImageSelectorBtn);
    console.log('- Cancel button:', cancelImageSelectionBtn);
    console.log('- Avatar preview:', avatarPreview);
    console.log('- File input:', avatarFileInput);
    console.log('- Hidden input:', avatarFromGalleryInput);
    
    // 打开图片选择模态框
    if (selectFromGalleryBtn && imageSelectorModal) {
        selectFromGalleryBtn.addEventListener('click', () => {
            console.log('Opening image selector modal...');
            // 显示模态框
            imageSelectorModal.classList.remove('hidden');
            imageSelectorModal.style.display = 'flex';
        });
    } else {
        console.error('Required elements not found!');
    }
    
    // 关闭图片选择模态框
    const closeImageSelector = () => {
        if (imageSelectorModal) {
            // 隐藏模态框
            imageSelectorModal.classList.add('hidden');
            imageSelectorModal.style.display = 'none';
        }
    };
    
    if (closeImageSelectorBtn) {
        closeImageSelectorBtn.addEventListener('click', closeImageSelector);
    }
    
    if (cancelImageSelectionBtn) {
        cancelImageSelectionBtn.addEventListener('click', closeImageSelector);
    }
    
    // 点击模态框外部关闭
    if (imageSelectorModal) {
        imageSelectorModal.addEventListener('click', (e) => {
            if (e.target === imageSelectorModal) {
                closeImageSelector();
            }
        });
    }
    
    // 图片选择处理 - 直接处理图片网格内的所有点击事件
    const imageGrid = document.getElementById('imageGrid');
    if (imageGrid) {
        imageGrid.addEventListener('click', (e) => {
            // 查找最近的带有data-image-path属性的父元素
            const imageItem = e.target.closest('[data-image-path]');
            if (imageItem) {
                const imagePath = imageItem.dataset.imagePath;
                console.log('Image selected:', imagePath);
                
                // 更新预览
                if (avatarPreview) {
                    avatarPreview.src = imagePath;
                }
                
                // 设置隐藏字段
                if (avatarFromGalleryInput) {
                    avatarFromGalleryInput.value = imagePath;
                }
                
                // 清除文件输入
                if (avatarFileInput) {
                    avatarFileInput.value = '';
                }
                
                // 关闭模态框
                closeImageSelector();
            }
        });
    }
    
    // 添加对本地上传按钮的支持
    const uploadLocalBtn = document.getElementById('uploadLocalBtn');
    if (uploadLocalBtn && avatarFileInput) {
        uploadLocalBtn.addEventListener('click', () => {
            console.log('Triggering local file upload...');
            avatarFileInput.click();
        });
    }
    
    // 相机图标也需要触发文件上传
    const cameraLabel = document.querySelector('label[for="avatar"]');
    if (cameraLabel && avatarFileInput) {
        cameraLabel.addEventListener('click', (e) => {
            // 阻止事件冒泡，确保不会触发其他点击事件
            e.stopPropagation();
            console.log('Camera icon clicked, triggering file upload...');
            avatarFileInput.click();
        });
    }
    
    // 文件上传预览
    if (avatarFileInput && avatarPreview && avatarFromGalleryInput) {
        avatarFileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    avatarPreview.src = event.target.result;
                    // 清除图库选择
                    avatarFromGalleryInput.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // 图片搜索功能
    const imageSearchInput = document.getElementById('imageSearch');
    if (imageSearchInput) {
        imageSearchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const imageGridItems = document.querySelectorAll('#imageGrid [data-image-path]');
            
            imageGridItems.forEach(item => {
                const imageTitle = item.querySelector('p').textContent.toLowerCase();
                if (imageTitle.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    console.log('Image selector initialization complete!');
});
</script>
