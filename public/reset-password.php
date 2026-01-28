<?php
/**
 * 密码重置页面
 */



// 检查是否已登录
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: /index.php');
    exit;
}

// 检查令牌是否存在
$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$token_valid = false;

// 验证令牌
if (!empty($token)) {
    require_once __DIR__ . '/db.php';
    require_once __DIR__ . '/functions.php';
    
    // 检查令牌是否有效
    $sql = "SELECT * FROM password_resets WHERE token = ? AND expiry > NOW()";
    $reset_request = db_query_one($sql, [$token]);
    
    if ($reset_request) {
        $token_valid = true;
    } else {
        $error = '无效或已过期的重置链接';
    }
} else {
    $error = '重置链接无效';
}

// 处理密码重置
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valid) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // 验证密码
    if (empty($password) || empty($confirm_password)) {
        $error = '请输入密码和确认密码';
    } elseif ($password !== $confirm_password) {
        $error = '两次输入的密码不一致';
    } elseif (strlen($password) < 6) {
        $error = '密码长度不能少于6个字符';
    } else {
        // 更新用户密码
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 更新用户表
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $result = db_exec($sql, [$hashed_password, $reset_request['user_id']]);
        
        if ($result > 0) {
            // 删除重置请求
            $sql = "DELETE FROM password_resets WHERE token = ?";
            db_exec($sql, [$token]);
            
            $success = '密码重置成功，请登录';
        } else {
            $error = '密码重置失败，请稍后重试';
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
                <!-- 密码重置表单 -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2">
                        樱花梦境
                    </h1>
                    <p class="text-gray-600">重置密码</p>
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
                    <div class="text-center">
                        <a href="/login.php" class="modern-btn text-white py-3 px-6 font-medium rounded-lg shadow-md inline-block">
                            前往登录
                        </a>
                    </div>
                <?php elseif ($token_valid): ?>
                    <form method="POST" action="/reset-password.php?token=<?php echo htmlspecialchars($token); ?>" class="space-y-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">新密码</label>
                            <input type="password" id="password" name="password" required 
                                   class="form-control focus:ring-pink-500 focus:border-transparent" placeholder="请输入新密码">
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">确认新密码</label>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   class="form-control focus:ring-pink-500 focus:border-transparent" placeholder="请再次输入新密码">
                        </div>
                        
                        <button type="submit" class="modern-btn text-white w-full py-3 px-4 font-medium rounded-lg shadow-md">
                            重置密码
                        </button>
                    </form>
                <?php endif; ?>
                
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
