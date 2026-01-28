<?php
/**
 * 后台登录页面
 */

// 引入函数文件
require_once __DIR__ . '/../functions.php';

// 检查是否已登录
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// 登录处理
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 从数据库验证用户
    require_once __DIR__ . '/../db.php';
    require_once __DIR__ . '/../functions.php';
    
    // 查找用户
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $user = db_query_one($sql, [$username, $username]);
    
    if ($user && password_verify($password, $user['password'])) {
        // 登录成功，检查是否为管理员角色
        if ($user['role'] === 'admin' || $user['role'] === 'editor') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_role'] = $user['role'];
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_avatar'] = $user['avatar'] ?? '';
            header('Location: dashboard.php');
            exit;
        } else {
            $error = '您没有权限访问后台';
        }
    } else {
        $error = '用户名或密码错误';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台登录 - 樱花梦境</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #fef2f2 0%, #f3e8ff 50%, #e0f2fe 100%);
            background-attachment: fixed;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(255, 107, 139, 0.1);
            border-color: #FF6B8B;
        }
        .btn-primary {
            background: linear-gradient(135deg, #FF6B8B, #8B5CF6);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 139, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="login-card w-full max-w-md p-8">
        <!-- 登录表单 -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2">
                樱花梦境
            </h1>
            <p class="text-gray-600">后台管理系统</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">用户名</label>
                <input type="text" id="username" name="username" required 
                       class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">密码</label>
                <input type="password" id="password" name="password" required 
                       class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" 
                           class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-600">记住我</label>
                </div>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3 px-4 text-white font-medium rounded-lg shadow-md">
                登录
            </button>
        </form>
        
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>&copy; 2026 樱花梦境. 保留所有权利.</p>
        </div>
    </div>
</body>
</html>