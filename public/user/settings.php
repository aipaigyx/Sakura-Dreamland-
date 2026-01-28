<?php
/**
 * 用户设置页面
 */

// 引入函数文件，会自动处理会话启动
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../db.php';

// 检查是否已登录
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // 检查是否是管理员登录
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        // 管理员自动登录到用户账户
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = 1; // 假设管理员的用户ID是1
        $_SESSION['username'] = $_SESSION['admin_username'];
        $_SESSION['user_role'] = 'admin';
    } else {
        header('Location: /login.php');
        exit;
    }
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$user = db_query_one($sql, [$user_id]);

$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $birthday = $_POST['birthday'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $avatar = $user['avatar'] ?? '';
    
    // 处理头像上传或从图库选择
    if (isset($_POST['avatar_from_gallery']) && !empty(trim($_POST['avatar_from_gallery']))) {
        // 从图库选择的头像
        $gallery_avatar = trim($_POST['avatar_from_gallery']);
        
        // 验证图片路径格式
        if (strpos($gallery_avatar, '/uploads/images/') === 0) {
            // 删除旧头像（如果存在且是上传的头像）
            if (!empty($user['avatar']) && strpos($user['avatar'], '/uploads/avatars/') === 0 && file_exists(__DIR__ . '/../' . $user['avatar'])) {
                unlink(__DIR__ . '/../' . $user['avatar']);
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
            $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_ext;
            $file_path = $upload_dir . $new_filename;
            
            // 移动上传文件
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
                $avatar = '/uploads/avatars/' . $new_filename;
                // 删除旧头像
                if (!empty($user['avatar']) && strpos($user['avatar'], '/uploads/avatars/') === 0 && file_exists(__DIR__ . '/../' . $user['avatar'])) {
                    unlink(__DIR__ . '/../' . $user['avatar']);
                }
            } else {
                $error = '头像上传失败，请稍后重试';
            }
        }
    }
    
    // 验证表单
    if (empty($username) || empty($email)) {
        $error = '请填写所有必填字段';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址';
    } else {
        // 检查用户名是否已被其他用户使用
        $check_username_sql = "SELECT * FROM users WHERE username = ? AND id != ?";
        $check_username = db_query_one($check_username_sql, [$username, $user_id]);
        if ($check_username) {
            $error = '用户名已被使用';
        } else {
            // 检查邮箱是否已被其他用户使用
            $check_email_sql = "SELECT * FROM users WHERE email = ? AND id != ?";
            $check_email = db_query_one($check_email_sql, [$email, $user_id]);
            if ($check_email) {
                $error = '邮箱已被使用';
            } else {
                // 更新用户信息
                $update_sql = "UPDATE users SET username = ?, email = ?, gender = ?, birthday = ?, bio = ?, avatar = ? WHERE id = ?";
                $result = db_exec($update_sql, [$username, $email, $gender, $birthday, $bio, $avatar, $user_id]);
                
                if ($result !== false) {
                    // 更新会话中的用户信息
                    $_SESSION['username'] = $username;
                    
                    // 刷新用户信息
                    $user = db_query_one("SELECT id, username, email, avatar, role FROM users WHERE id = ?", [$user_id]);
                    
                    // 如果获取到新的用户信息，更新会话
                    if ($user) {
                        $_SESSION['user_avatar'] = $user['avatar'];
                        $success = '个人信息更新成功';
                    } else {
                        $success = '个人信息更新成功，但刷新用户信息失败';
                    }
                } else {
                    $error = '更新失败，请稍后重试';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>设置 - 樱花梦境</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef2f2 0%, #f3e8ff 50%, #e0f2fe 100%);
            background-attachment: fixed;
        }
        .glass-morphism {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        }
        .form-control {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid rgba(255, 107, 139, 0.2);
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: #FF6B8B;
            box-shadow: 0 0 0 3px rgba(255, 107, 139, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- 包含头部 -->
    <?php require_once __DIR__ . '/../header.php'; ?>
    
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- 页面标题 -->
        <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6">
            <i class="fas fa-cog mr-2"></i>设置
        </h1>
        
        <!-- 返回按钮 -->
        <a href="profile.php" class="inline-flex items-center text-pink-500 hover:text-pink-600 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>返回个人中心
        </a>
        
        <!-- 内容区域 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 侧边菜单 -->
            <div class="lg:col-span-1">
                <div class="glass-morphism rounded-2xl p-6">
                    <h3 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-4">
                        设置菜单
                    </h3>
                    <nav class="space-y-2">
                        <a href="settings.php" class="block px-4 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg font-medium">
                            <i class="fas fa-user-cog mr-2"></i>个人资料
                        </a>
                        <a href="password.php" class="block px-4 py-3 bg-white/50 text-gray-700 rounded-lg hover:bg-white hover:text-pink-500 transition-colors duration-200 font-medium">
                            <i class="fas fa-lock mr-2"></i>修改密码
                        </a>
                        <a href="privacy.php" class="block px-4 py-3 bg-white/50 text-gray-700 rounded-lg hover:bg-white hover:text-pink-500 transition-colors duration-200 font-medium">
                            <i class="fas fa-shield-alt mr-2"></i>隐私设置
                        </a>
                        <a href="notifications.php" class="block px-4 py-3 bg-white/50 text-gray-700 rounded-lg hover:bg-white hover:text-pink-500 transition-colors duration-200 font-medium">
                            <i class="fas fa-bell mr-2"></i>通知设置
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- 主要内容 -->
            <div class="lg:col-span-2">
                <div class="glass-morphism rounded-2xl p-6">
                    <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-6">
                        个人资料设置
                    </h2>
                    
                    <?php if ($error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?php echo $success; ?>
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
                    
                    <!-- 个人资料表单 -->
                    <form method="POST" action="settings.php" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="avatar_from_gallery" id="avatar_from_gallery" value="">
                        <!-- 用户头像 -->
                        <div class="flex items-center gap-6">
                            <div class="relative">
                                <img src="<?php echo $user['avatar'] ?: 'https://picsum.photos/id/1005/100/100'; ?>" alt="用户头像" id="avatarPreview" class="w-20 h-20 rounded-full object-cover border-4 border-pink-500 shadow-lg">
                                
                                <!-- 相机图标 - 用于本地文件上传 -->
                                <div class="absolute bottom-0 right-0 w-8 h-8 z-10">
                                    <label for="avatar-upload" class="w-full h-full p-1 bg-pink-500 text-white rounded-full shadow-lg hover:bg-pink-600 transition-colors duration-200 cursor-pointer flex items-center justify-center">
                                        <i class="fas fa-camera text-sm"></i>
                                    </label>
                                    <input type="file" id="avatar-upload" name="avatar" accept="image/jpeg,image/png,image/gif" 
                                           class="hidden">
                                </div>
                            </div>
                            <div class="space-y-3">
                                <p class="text-sm text-gray-500">设置您的头像</p>
                                
                                <!-- 从图库选择按钮 -->
                                <button type="button" id="selectFromGallery" 
                                        class="inline-flex items-center justify-center px-5 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 text-sm transition-all duration-200 shadow-md">
                                    <i class="fas fa-images mr-2"></i>从图库选择
                                </button>
                                
                                <!-- 本地上传按钮 -->
                                <button type="button" id="uploadLocalBtn" 
                                        class="inline-flex items-center justify-center px-5 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 text-sm transition-all duration-200 shadow-md">
                                    <i class="fas fa-upload mr-2"></i>本地上传
                                </button>
                                
                                <p class="text-xs text-gray-400">支持 JPG、PNG、GIF 格式，最大 5MB</p>
                            </div>
                        </div>
                        
                        <!-- 用户名 -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">用户名</label>
                            <input type="text" id="username" name="username" required 
                                   value="<?php echo htmlspecialchars($user['username']); ?>"
                                   class="form-control">
                        </div>
                        
                        <!-- 邮箱 -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
                            <input type="email" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($user['email']); ?>"
                                   class="form-control">
                        </div>
                        
                        <!-- 性别 -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">性别</label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="male" class="text-pink-500 focus:ring-pink-500 border-gray-300" <?php echo (($user['gender'] ?? '') === 'male') ? 'checked' : ''; ?>>
                                    <span>男</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="female" class="text-pink-500 focus:ring-pink-500 border-gray-300" <?php echo (($user['gender'] ?? '') === 'female') ? 'checked' : ''; ?>>
                                    <span>女</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="other" class="text-pink-500 focus:ring-pink-500 border-gray-300" <?php echo (($user['gender'] ?? '') === 'other') ? 'checked' : ''; ?>>
                                    <span>其他</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- 生日 -->
                        <div>
                            <label for="birthday" class="block text-sm font-medium text-gray-700 mb-1">生日</label>
                            <input type="date" id="birthday" name="birthday" 
                                   value="<?php echo $user['birthday'] ?? ''; ?>"
                                   class="form-control">
                        </div>
                        
                        <!-- 个人简介 -->
                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">个人简介</label>
                            <textarea id="bio" name="bio" rows="4" 
                                      class="form-control" placeholder="介绍一下自己吧..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <!-- 保存按钮 -->
                        <div class="flex items-center gap-4">
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-lg hover:from-pink-600 hover:to-purple-700 transition-colors duration-200 font-medium shadow-md">
                                保存更改
                            </button>
                            <button type="reset" class="px-6 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200 font-medium border border-gray-300">
                                重置
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    
    <!-- 包含底部 -->
    <?php require_once __DIR__ . '/../footer.php'; ?>
    
    <!-- 图片选择功能JavaScript -->
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
    const avatarFileInput = document.getElementById('avatar-upload');
    
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
    const cameraLabel = document.querySelector('label[for="avatar-upload"]');
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
</body>
</html>