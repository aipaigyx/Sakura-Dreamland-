<?php
/**
 * 重新发送验证邮件页面
 */

// 引入函数文件
require_once __DIR__ . '/functions.php';

// 检查是否已登录
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: /index.php');
    exit;
}

// 处理表单提交
$error = '';
$success = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $error = '请输入您的邮箱地址';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '请输入有效的邮箱地址';
    } else {
        // 发送验证邮件
        $email_sent = resend_verification_email($email);
        
        if ($email_sent) {
            $success = '验证邮件已重新发送，请检查您的邮箱（包括垃圾邮件文件夹）。';
            $email = '';
        } else {
            $error = '发送验证邮件失败，请检查您的邮箱地址是否正确或联系管理员。';
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
                <!-- 重新发送验证邮件表单 -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2">
                        樱花梦境
                    </h1>
                    <p class="text-gray-600">重新发送验证邮件</p>
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
                
                <form method="POST" action="/resend-verification.php" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
                        <input type="email" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($email); ?>"
                               class="form-control focus:ring-pink-500 focus:border-transparent">
                    </div>
                    
                    <button type="submit" class="modern-btn text-white w-full py-3 px-4 font-medium rounded-lg shadow-md">
                        重新发送验证邮件
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">已有账号？ <a href="/login.php" class="text-pink-500 hover:text-pink-600 font-medium">立即登录</a></p>
                    <p class="text-sm text-gray-600 mt-2">还没有账号？ <a href="/register.php" class="text-pink-500 hover:text-pink-600 font-medium">立即注册</a></p>
                </div>
            </div>
        </div>
    </main>
<?php
// 加载页脚
include __DIR__ . '/footer.php';
