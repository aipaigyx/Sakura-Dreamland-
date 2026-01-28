<?php
/**
 * 前台登录页面
 */

// 引入函数文件
require_once __DIR__ . '/functions.php';

// 检查是否已登录
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: /index.php');
    exit;
}

// 登录处理
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // 验证用户
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/functions.php';
    
    // 查找用户
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $user = db_query_one($sql, [$username, $username]);
    
    if ($user && password_verify($password, $user['password'])) {
        // 检查邮箱是否已验证
        if ($user['email_verified'] === false) {
            // 邮箱未验证，不允许登录
            $error = '您的邮箱尚未验证，请先检查邮箱完成验证后再登录。';
        } else {
            // 登录成功
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['avatar'] ?? '';
            
            header('Location: /index.php');
            exit;
        }
    } else {
        $error = '用户名或密码错误';
    }
}

// 加载页面模板
include __DIR__ . '/header.php';
?>
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <div class="flex justify-center items-center min-h-[70vh]">
            <div class="modern-card w-full max-w-md p-8 bg-white rounded-3xl shadow-lg overflow-hidden border-2 border-gray-100">
                <!-- 卡通头像 -->
                <div class="flex justify-center mt-4">
                    <div class="w-24 h-24 bg-white rounded-full border-4 border-white shadow-lg overflow-hidden">
                        <img src="https://picsum.photos/seed/avatar/200/200" alt="卡通头像" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <!-- 登录表单 -->
                <div class="text-center mt-4 mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">HI! 请登录</h1>
                </div>
                
                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/login.php" class="space-y-4">
                    <div>
                        <input type="text" id="username" name="username" required 
                               placeholder="用户名/邮箱" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <input type="password" id="password" name="password" required 
                               placeholder="密码" 
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input type="checkbox" id="remember" name="remember" 
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="remember" class="ml-2 block text-sm text-gray-600">记住我</label>
                            </div>
                            <a href="/forgot-password.php" class="text-sm text-blue-500 hover:text-blue-600">忘记密码？</a>
                        </div>
                    
                    <button type="submit" class="w-full py-3 px-4 bg-blue-500 text-white font-medium rounded-xl shadow-md hover:bg-blue-600 transition-all">
                        登录
                    </button>
                    
                    <button type="button" onclick="location.href='/register.php'" 
                            class="w-full py-3 px-4 bg-orange-500 text-white font-medium rounded-xl shadow-md hover:bg-orange-600 transition-all">
                        注册
                    </button>
                </form>
                
                <!-- 社交账号登录 -->
                <div class="mt-6">
                    <div class="text-center mb-4">
                        <p class="text-sm text-gray-600">社交账号登录</p>
                    </div>
                    
                    <div class="flex justify-center gap-4">
                        <!-- 社交登录按钮 -->
                        <button type="button" class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 transition-all">
                            <i class="fas fa-bell"></i>
                        </button>
                        <button type="button" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition-all">
                            <i class="fab fa-weixin"></i>
                        </button>
                        <button type="button" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition-all">
                            <i class="fab fa-baidu"></i>
                        </button>
                        <button type="button" class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 transition-all">
                            <i class="fab fa-alipay"></i>
                        </button>
                        <button type="button" class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 transition-all">
                            <i class="fab fa-qq"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php
// 加载页脚
include __DIR__ . '/footer.php';
