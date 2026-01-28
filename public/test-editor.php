<?php
/**
 * 测试TinyMCE编辑器
 */


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>测试TinyMCE编辑器</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-pink-50 to-purple-50 min-h-screen">
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-center mb-8 text-pink-600">测试TinyMCE编辑器</h1>
        
        <!-- 编辑器容器 -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">编辑器测试</h2>
            <!-- 编辑器文本域 -->
            <textarea id="content" name="content" rows="10" class="w-full border border-gray-300 rounded-lg p-3"></textarea>
            
            <!-- 测试按钮 -->
            <div class="mt-4 flex gap-4">
                <button id="get-content-btn" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-check mr-2"></i>获取内容
                </button>
                <button id="clear-content-btn" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    <i class="fas fa-undo mr-2"></i>清空内容
                </button>
            </div>
        </div>
        
        <!-- 内容显示区域 -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">编辑器内容</h2>
            <div id="content-output" class="border border-gray-300 rounded-lg p-3 min-h-32"></div>
        </div>
    </div>
    
    <!-- 加载核心样式和脚本 -->
    <link rel="stylesheet" href="/assets/css/unified.css">
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/interactivity.js"></script>
    
    <!-- TinyMCE编辑器 -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    <!-- 加载中文语言包 -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/langs/zh_CN.min.js"></script>
    
    <script>
        // 简单的编辑器初始化
        tinymce.init({
            selector: '#content',
            height: 400,
            menubar: true,
            statusbar: true,
            resize: 'vertical',
            branding: false,
            readonly: false,
            language: 'zh_CN',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount',
                'directionality', 'emoticons'
            ],
            toolbar: 'undo redo | styles | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media emoticons | table | code fullscreen preview | searchreplace | help',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.6; max-width: 100%; }',
            init_instance_callback: function(editor) {
                console.log('TinyMCE编辑器初始化成功！');
            },
            error_callback: function(error) {
                console.error('TinyMCE编辑器错误:', error);
            }
        });
    </script>
    <script>
        // 测试按钮事件
        document.addEventListener('DOMContentLoaded', function() {
            // 获取内容按钮
            const getContentBtn = document.getElementById('get-content-btn');
            if (getContentBtn) {
                getContentBtn.addEventListener('click', function() {
                    const content = tinymce.get('content').getContent();
                    const output = document.getElementById('content-output');
                    output.innerHTML = content || '<p class="text-gray-500 italic">编辑器内容为空</p>';
                    console.log('获取编辑器内容:', content);
                });
            }
            
            // 清空内容按钮
            const clearContentBtn = document.getElementById('clear-content-btn');
            if (clearContentBtn) {
                clearContentBtn.addEventListener('click', function() {
                    tinymce.get('content').setContent('');
                    const output = document.getElementById('content-output');
                    output.innerHTML = '<p class="text-gray-500 italic">编辑器内容为空</p>';
                    console.log('清空编辑器内容');
                });
            }
        });
    </script>
</body>
</html>