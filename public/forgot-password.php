<?php
/**
 * 忘记密码页面
 */



// 检查是否已登录
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: /index.php');
    exit;
}

// 处理密码重置请求
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    // 验证邮箱
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址';
    } else {
        // 检查邮箱是否存在
        require_once __DIR__ . '/db.php';
        require_once __DIR__ . '/functions.php';
        
        // 查找用户
        $sql = "SELECT * FROM users WHERE email = ?";
        $user = db_query_one($sql, [$email]);
        
        if ($user) {
            // 生成密码重置令牌
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // 检查是否已存在重置请求
            $sql = "SELECT id FROM password_resets WHERE user_id = ?";
            $existing = db_query_one($sql, [$user['id']]);
            
            if ($existing) {
                // 更新现有请求
                $sql = "UPDATE password_resets SET token = ?, expiry = ? WHERE user_id = ?";
                db_exec($sql, [$token, $expiry, $user['id']]);
            } else {
                // 创建新请求
                $sql = "INSERT INTO password_resets (user_id, email, token, expiry) VALUES (?, ?, ?, ?)";
                db_exec($sql, [$user['id'], $email, $token, $expiry]);
            }
            
            // 这里应该发送邮件，现在只是模拟
            $success = '密码重置链接已发送到您的邮箱，请在1小时内点击链接重置密码';
        } else {
            // 为了安全，即使邮箱不存在也显示相同的成功消息
            $success = '密码重置链接已发送到您的邮箱，请在1小时内点击链接重置密码';
        }
    }
}

// 加载页面模板
include __DIR__ . '/header.php';
?>
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <div class="flex justify-center items-center min-h-[70vh]">
            <div class="modern-card w-full max-w-md p-8 bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
                <!-- 忘记密码表单 -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2">
                        樱花梦境
                    </h1>
                    <p class="text-gray-600">忘记密码</p>
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
                
                <form method="POST" action="/forgot-password.php" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">邮箱地址</label>
                        <input type="email" id="email" name="email" required 
                               class="form-control focus:ring-pink-500 focus:border-transparent" placeholder="your@email.com">
                    </div>
                    
                    <button type="submit" class="modern-btn text-white w-full py-3 px-4 font-medium rounded-lg shadow-md">
                        发送重置链接
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        <a href="/login.php" class="text-pink-500 hover:text-pink-600 font-medium">返回登录</a> | 
                        <a href="/register.php" class="text-pink-500 hover:text-pink-600 font-medium">创建账号</a>
                    </p>
                </div>
            </div>
        </div>
    </main>
<?php
// 加载页脚
include __DIR__ . '/footer.php';
