<?php
/**
 * 邮箱发送功能
 * 支持 PHP 内置 mail() 函数和 SMTP 发送
 */

class EmailSender {
    /**
     * 发送邮件
     * 
     * @param string $to 收件人邮箱
     * @param string $subject 邮件主题
     * @param string $message 邮件内容
     * @param array $headers 邮件头部
     * @return bool 发送结果
     */
    private static function sendEmail($to, $subject, $message, $headers = []) {
        $settings = get_settings();
        
        // 检查是否启用 SMTP
        if ($settings['email_smtp_enable'] === 1) {
            return self::sendEmailViaSMTP($to, $subject, $message, $headers);
        } else {
            // 使用 PHP 内置 mail() 函数
            return self::sendEmailViaMail($to, $subject, $message, $headers);
        }
    }
    
    /**
     * 使用 PHP 内置 mail() 函数发送邮件
     * 
     * @param string $to 收件人邮箱
     * @param string $subject 邮件主题
     * @param string $message 邮件内容
     * @param array $headers 邮件头部
     * @return bool 发送结果
     */
    private static function sendEmailViaMail($to, $subject, $message, $headers = []) {
        // 转换数组头部为字符串
        if (is_array($headers)) {
            $headers_str = '';
            foreach ($headers as $name => $value) {
                $headers_str .= "{$name}: {$value}\r\n";
            }
        } else {
            $headers_str = $headers;
        }
        
        // 发送邮件
        return mail($to, $subject, $message, $headers_str);
    }
    
    /**
     * 使用 SMTP 发送邮件
     * 
     * @param string $to 收件人邮箱
     * @param string $subject 邮件主题
     * @param string $message 邮件内容
     * @param array $headers 邮件头部
     * @return bool 发送结果
     */
    private static function sendEmailViaSMTP($to, $subject, $message, $headers = []) {
        $settings = get_settings();
        
        // 获取 SMTP 设置
        $smtp_host = $settings['email_smtp_host'] ?? 'smtp.example.com';
        $smtp_port = $settings['email_smtp_port'] ?? 587;
        $smtp_security = $settings['email_smtp_security'] ?? 'tls';
        $smtp_username = $settings['email_smtp_username'] ?? '';
        $smtp_password = $settings['email_smtp_password'] ?? '';
        $smtp_auth = $settings['email_smtp_auth'] ?? 1;
        
        // 创建套接字连接
        $socket = fsockopen(
            $smtp_host, 
            $smtp_port, 
            $errno, 
            $errstr, 
            30
        );
        
        if (!$socket) {
            return false;
        }
        
        // SMTP 通信函数
        $smtp_send = function($command) use ($socket) {
            fwrite($socket, $command . "\r\n");
            return fgets($socket, 4096);
        };
        
        // 忽略初始响应
        $smtp_send("EHLO " . parse_url(ANIBLOG_BASE_URL, PHP_URL_HOST));
        
        // 开始 TLS 加密（如果需要）
        if ($smtp_security === 'tls') {
            $smtp_send("STARTTLS");
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $smtp_send("EHLO " . parse_url(ANIBLOG_BASE_URL, PHP_URL_HOST));
        }
        
        // 登录（如果需要）
        if ($smtp_auth === 1 && !empty($smtp_username) && !empty($smtp_password)) {
            $smtp_send("AUTH LOGIN");
            $smtp_send(base64_encode($smtp_username));
            $smtp_send(base64_encode($smtp_password));
        }
        
        // 设置发件人和收件人
        $from_email = $settings['email_verification_from_email'] ?? 'noreply@' . parse_url(ANIBLOG_BASE_URL, PHP_URL_HOST);
        $smtp_send("MAIL FROM:<{$from_email}>");
        $smtp_send("RCPT TO:<{$to}>");
        
        // 开始邮件数据
        $smtp_send("DATA");
        
        // 构建完整邮件
        $full_message = "{$subject}\r\n";
        
        // 添加头部
        $full_message .= "From: {$settings['email_verification_from_name'] ?? '樱花梦境'} <{$from_email}>\r\n";
        $full_message .= "Reply-To: {$from_email}\r\n";
        $full_message .= "MIME-Version: 1.0\r\n";
        $full_message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $full_message .= "Content-Transfer-Encoding: 8bit\r\n";
        $full_message .= "Date: " . date('r') . "\r\n";
        $full_message .= "Message-ID: <" . md5(uniqid()) . "@" . parse_url(ANIBLOG_BASE_URL, PHP_URL_HOST) . ">\r\n";
        
        // 添加自定义头部
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                $full_message .= "{$name}: {$value}\r\n";
            }
        }
        
        // 添加空行分隔头部和内容
        $full_message .= "\r\n";
        $full_message .= $message;
        $full_message .= "\r\n.\r\n";
        
        // 发送邮件内容
        $smtp_send($full_message);
        
        // 结束连接
        $smtp_send("QUIT");
        
        // 关闭套接字
        fclose($socket);
        
        return true;
    }
    /**
     * 发送验证邮件
     * 
     * @param string $to 收件人邮箱
     * @param string $username 收件人用户名
     * @param string $verification_link 验证链接
     * @return bool 发送结果
     */
    public static function sendVerificationEmail($to, $username, $verification_link) {
        // 获取设置值
        $settings = get_settings();
        
        $site_name = $settings['site_name'] ?? '樱花梦境';
        $site_url = ANIBLOG_BASE_URL;
        $from_name = $settings['email_verification_from_name'] ?? $site_name;
        $from_email = $settings['email_verification_from_email'] ?? 'noreply@' . parse_url($site_url, PHP_URL_HOST);
        
        // 邮件主题
        $subject = '[' . $site_name . '] 邮箱验证通知';
        
        // 邮件内容
        $message = self::getVerificationEmailTemplate($username, $verification_link, $site_name, $site_url);
        
        // 发送邮件
        return self::sendEmail($to, $subject, $message);
    }
    
    /**
     * 获取验证邮件模板
     * 
     * @param string $username 用户名
     * @param string $verification_link 验证链接
     * @param string $site_name 网站名称
     * @param string $site_url 网站URL
     * @return string 邮件HTML内容
     */
    private static function getVerificationEmailTemplate($username, $verification_link, $site_name, $site_url) {
        return <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>邮箱验证</title>
    <style>
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #ff6b8b, #a855f7);
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            color: #666;
        }
        .verification-btn {
            display: block;
            width: 250px;
            margin: 0 auto 20px;
            padding: 15px 0;
            background: linear-gradient(135deg, #ff6b8b, #a855f7);
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
        }
        .verification-btn:hover {
            background: linear-gradient(135deg, #ff5270, #9333ea);
        }
        .alternative-link {
            text-align: center;
            margin: 20px 0;
            color: #999;
            font-size: 14px;
        }
        .alternative-link a {
            color: #ff6b8b;
            text-decoration: none;
        }
        .note {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 14px;
            color: #666;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #ff6b8b;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 邮件头部 -->
        <div class="header">
            <h1>🌸 {$site_name} 🌸</h1>
        </div>
        
        <!-- 邮件内容 -->
        <div class="content">
            <div class="greeting">
                亲爱的 {$username}：
            </div>
            
            <div class="message">
                感谢您注册 {$site_name}！为了确保您的账号安全，我们需要验证您的邮箱地址。
            </div>
            
            <div class="note">
                请在 24 小时内点击下方按钮完成邮箱验证，否则您的注册信息将失效。
            </div>
            
            <!-- 验证按钮 -->
            <a href="{$verification_link}" class="verification-btn">
                立即验证邮箱
            </a>
            
            <!-- 备用链接 -->
            <div class="alternative-link">
                如果上述按钮无法点击，请复制以下链接到浏览器地址栏访问：<br>
                <a href="{$verification_link}">{$verification_link}</a>
            </div>
            
            <div class="message">
                如果您没有注册过 {$site_name}，请忽略此邮件。
            </div>
        </div>
        
        <!-- 邮件底部 -->
        <div class="footer">
            <p>此邮件由 {$site_name} 自动发送，请勿直接回复。</p>
            <p>如有疑问，请访问 <a href="{$site_url}">{$site_name}</a> 联系我们。</p>
            <p>&copy; " . date('Y') . " {$site_name}. 保留所有权利。</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * 发送密码重置邮件
     * 
     * @param string $to 收件人邮箱
     * @param string $username 收件人用户名
     * @param string $reset_link 重置链接
     * @return bool 发送结果
     */
    public static function sendPasswordResetEmail($to, $username, $reset_link) {
        // 获取设置值
        $settings = get_settings();
        
        $site_name = $settings['site_name'] ?? '樱花梦境';
        $site_url = ANIBLOG_BASE_URL;
        $from_name = $settings['email_verification_from_name'] ?? $site_name;
        $from_email = $settings['email_verification_from_email'] ?? 'noreply@' . parse_url($site_url, PHP_URL_HOST);
        
        // 邮件主题
        $subject = '[' . $site_name . '] 密码重置请求';
        
        // 邮件内容
        $message = self::getPasswordResetEmailTemplate($username, $reset_link, $site_name, $site_url);
        
        // 发送邮件
        return self::sendEmail($to, $subject, $message);
    }
    
    /**
     * 获取密码重置邮件模板
     * 
     * @param string $username 用户名
     * @param string $reset_link 重置链接
     * @param string $site_name 网站名称
     * @param string $site_url 网站URL
     * @return string 邮件HTML内容
     */
    private static function getPasswordResetEmailTemplate($username, $reset_link, $site_name, $site_url) {
        return <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>密码重置</title>
    <style>
        /* 样式与验证邮件模板相同 */
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #ff6b8b, #a855f7);
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 30px;
            color: #666;
        }
        .verification-btn {
            display: block;
            width: 250px;
            margin: 0 auto 20px;
            padding: 15px 0;
            background: linear-gradient(135deg, #ff6b8b, #a855f7);
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 30px;
            font-size: 16px;
            font-weight: bold;
        }
        .verification-btn:hover {
            background: linear-gradient(135deg, #ff5270, #9333ea);
        }
        .alternative-link {
            text-align: center;
            margin: 20px 0;
            color: #999;
            font-size: 14px;
        }
        .alternative-link a {
            color: #ff6b8b;
            text-decoration: none;
        }
        .note {
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 14px;
            color: #666;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #999;
            border-top: 1px solid #eee;
        }
        .footer a {
            color: #ff6b8b;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 邮件头部 -->
        <div class="header">
            <h1>🌸 {$site_name} 🌸</h1>
        </div>
        
        <!-- 邮件内容 -->
        <div class="content">
            <div class="greeting">
                亲爱的 {$username}：
            </div>
            
            <div class="message">
                我们收到了您的密码重置请求。如果这不是您本人操作，请忽略此邮件。
            </div>
            
            <div class="note">
                请在 1 小时内点击下方按钮重置密码，否则链接将失效。
            </div>
            
            <!-- 重置按钮 -->
            <a href="{$reset_link}" class="verification-btn">
                立即重置密码
            </a>
            
            <!-- 备用链接 -->
            <div class="alternative-link">
                如果上述按钮无法点击，请复制以下链接到浏览器地址栏访问：<br>
                <a href="{$reset_link}">{$reset_link}</a>
            </div>
            
            <div class="message">
                此邮件由 {$site_name} 自动发送，请勿直接回复。
            </div>
        </div>
        
        <!-- 邮件底部 -->
        <div class="footer">
            <p>此邮件由 {$site_name} 自动发送，请勿直接回复。</p>
            <p>如有疑问，请访问 <a href="{$site_url}">{$site_name}</a> 联系我们。</p>
            <p>&copy; " . date('Y') . " {$site_name}. 保留所有权利。</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
