<?php
/**
 * 前台注册页面
 */

// 引入函数文件
require_once __DIR__ . '/functions.php';

// 检查是否已登录
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: /index.php');
    exit;
}

// 注册处理
$error = '';
$success = '';
$username = $email = $password = $confirm_password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // 验证表单
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = '请填写所有必填字段';
    } elseif ($password !== $confirm_password) {
        $error = '两次输入的密码不一致';
    } elseif (strlen($password) < 6) {
        $error = '密码长度不能少于6个字符';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址';
    } else {
        // 验证通过，创建用户
        require_once __DIR__ . '/db.php';
        require_once __DIR__ . '/functions.php';
        
        // 检查用户名是否已存在
        $check_username_sql = "SELECT * FROM users WHERE username = ?";
        $check_username = db_query_one($check_username_sql, [$username]);
        if ($check_username) {
            $error = '用户名已存在';
        } else {
            // 检查邮箱是否已存在
            $check_email_sql = "SELECT * FROM users WHERE email = ?";
            $check_email = db_query_one($check_email_sql, [$email]);
            if ($check_email) {
                $error = '邮箱已被注册';
            } else {
                // 创建用户
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
                $result = db_exec($sql, [$username, $email, $hashed_password]);
                
                if ($result) {
                // 获取刚创建的用户ID
                $user_id = db_927378776;
                
                // 发送验证邮件
                $email_sent = send_verification_email($user_id, $email, $username);
                
                // 清空表单
                $username = $email = $password = $confirm_password = '';
                
                if ($email_sent) {
                    $success = '注册成功！我们已向您的邮箱发送了验证链接，请在24小时内点击链接完成邮箱验证。';
                } else {
                    $success = '注册成功！但发送验证邮件失败，请稍后重试或联系管理员。';
                }
            } else {
                $error = '注册失败，请稍后重试';
            }
            }
        }
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
                
                <!-- 注册表单 -->
                <div class="text-center mt-4 mb-8">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">HI! 请注册</h1>
                </div>
                
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
                
                <form method="POST" action="/register.php" class="space-y-4">
                    <div>
                        <input type="text" id="username" name="username" required 
                               placeholder="用户名"
                               value="<?php echo htmlspecialchars($username); ?>"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <input type="email" id="email" name="email" required 
                               placeholder="邮箱"
                               value="<?php echo htmlspecialchars($email); ?>"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <input type="password" id="password" name="password" required 
                               placeholder="密码"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               placeholder="确认密码"
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>
                    
                    <div class="flex items-start">
                            <input type="checkbox" id="agree" name="agree" required 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                            <label for="agree" class="ml-2 block text-sm text-gray-600">
                                我同意<a href="/terms.php" class="text-blue-500 hover:text-blue-600">服务条款</a>和<a href="/privacy.php" class="text-blue-500 hover:text-blue-600">隐私政策</a>
                            </label>
                        </div>
                    
                    <button type="submit" class="w-full py-3 px-4 bg-orange-500 text-white font-medium rounded-xl shadow-md hover:bg-orange-600 transition-all">
                        注册
                    </button>
                    
                    <button type="button" onclick="location.href='/login.php'" 
                            class="w-full py-3 px-4 bg-blue-500 text-white font-medium rounded-xl shadow-md hover:bg-blue-600 transition-all">
                        登录
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
