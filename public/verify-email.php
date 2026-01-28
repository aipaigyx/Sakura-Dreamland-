<?php
/**
 * 邮箱验证页面
 */

// 引入函数文件
require_once __DIR__ . '/functions.php';

// 验证结果
$success = '';
$error = '';

// 检查是否有验证令牌
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // 验证令牌
    $user = verify_email_token($token);
    
    if ($user) {
        // 更新邮箱验证状态
        $verification_success = update_email_verification_status($user['id']);
        
        if ($verification_success) {
            $success = '邮箱验证成功！您的账号已激活，可以登录了。';
        } else {
            $error = '邮箱验证失败，请稍后重试或联系管理员。';
        }
    } else {
        $error = '无效的验证链接或链接已过期，请重新注册或申请重新发送验证邮件。';
    }
} else {
    $error = '缺少验证令牌，请检查您的验证邮件。';
}

// 加载页面模板
include __DIR__ . '/header.php';
?>
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <div class="flex justify-center items-center min-h-[70vh]">
            <div class="modern-card w-full max-w-md p-8 bg-gradient-to-br from-pink-50 via-purple-50 to-pink-50 border-2 border-pink-200 rounded-3xl">
                <!-- 验证结果 -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 via-purple-500 to-blue-500 mb-2">
                        樱花梦境
                    </h1>
                    <p class="text-gray-600">邮箱验证</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-5 rounded-lg mb-6 flex flex-col items-center text-center">
                        <i class="fas fa-times-circle text-4xl mb-3"></i>
                        <h3 class="font-medium text-lg mb-2">验证失败</h3>
                        <p><?php echo $error; ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="bg-green-50 border border-green-200 text-green-600 px-4 py-5 rounded-lg mb-6 flex flex-col items-center text-center">
                        <i class="fas fa-check-circle text-4xl mb-3"></i>
                        <h3 class="font-medium text-lg mb-2">验证成功</h3>
                        <p><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- 操作按钮 -->
                <div class="flex flex-col gap-4">
                    <a href="/login.php" class="modern-btn text-white w-full py-3 px-4 font-medium rounded-lg shadow-md text-center">
                        前往登录
                    </a>
                    
                    <div class="text-center">
                        <p class="text-sm text-gray-600">需要重新发送验证邮件？ <a href="/resend-verification.php" class="text-pink-500 hover:text-pink-600 font-medium">点击这里</a></p>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php
// 加载页脚
include __DIR__ . '/footer.php';
