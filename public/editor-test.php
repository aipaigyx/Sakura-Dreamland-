<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>编辑器测试页面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .editor-container {
            margin-top: 20px;
        }
        .log {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 14px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>编辑器测试页面</h1>
        
        <div class="editor-container">
            <h2>测试编辑器</h2>
            <textarea id="content" name="content" rows="10" cols="50"></textarea>
        </div>
        
        <div id="log" class="log">
            日志输出将显示在这里...
        </div>
    </div>

    <!-- TinyMCE编辑器 -->
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" onload="log('TinyMCE核心文件加载成功')" onerror="log('TinyMCE核心文件加载失败')"></script>
    <!-- 加载中文语言包 - 本地资源 -->
    <script src="/assets/tinymce/langs/zh_CN.min.js" onload="log('中文语言包加载成功')" onerror="log('中文语言包加载失败')"></script>
    
    <script>
        // 日志函数
        function log(message) {
            const logEl = document.getElementById('log');
            const timestamp = new Date().toLocaleTimeString();
            logEl.innerHTML += `[${timestamp}] ${message}\n`;
            logEl.scrollTop = logEl.scrollHeight;
        }
        
        log('页面加载完成，准备初始化编辑器...');
        
        // 检查content元素是否存在
        const contentEl = document.getElementById('content');
        if (contentEl) {
            log('找到content元素，准备初始化编辑器');
        } else {
            log('错误：未找到content元素');
        }
        
        // 添加浏览器环境信息
        log('浏览器环境信息：');
        log('User Agent: ' + navigator.userAgent);
        log('URL: ' + window.location.href);
        log('Document Ready State: ' + document.readyState);
        
        // 检查DOM中是否存在content元素
        const contentEl = document.getElementById('content');
        log('Content元素存在: ' + (contentEl !== null));
        if (contentEl) {
            log('Content元素ID: ' + contentEl.id);
            log('Content元素类型: ' + contentEl.tagName);
        }
        
        // 检查TinyMCE是否已加载
        log('TinyMCE是否已加载: ' + (typeof tinymce !== 'undefined'));
        if (typeof tinymce !== 'undefined') {
            log('TinyMCE版本: ' + (tinymce.majorVersion || '未知'));
            log('TinyMCE对象: ' + typeof tinymce);
            log('TinyMCE已加载，开始初始化...');
            
            try {
                log('调用tinymce.init()...');
                tinymce.init({
                    selector: '#content',
                    height: 300,
                    menubar: true,
                    statusbar: true,
                    branding: false,
                    readonly: false,
                    mode: 'design',
                    language: 'zh_CN',
                    plugins: ['lists', 'link', 'image', 'code', 'paste'],
                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | image link | code',
                    init_instance_callback: function(editor) {
                        log('编辑器初始化成功！');
                        log('编辑器版本：' + tinymce.majorVersion + '.' + tinymce.minorVersion);
                        log('编辑器模式：' + editor.mode.get());
                    },
                    error_callback: function(error) {
                        log('编辑器初始化错误：' + JSON.stringify(error));
                    },
                    setup: function(editor) {
                        log('编辑器setup函数被调用');
                        
                        editor.on('init', function() {
                            log('编辑器init事件触发');
                        });
                        
                        editor.on('keydown', function() {
                            log('编辑器按键事件');
                        });
                    }
                });
            } catch (error) {
                log('初始化异常：' + error.message);
                log('异常堆栈：' + error.stack);
            }
        } else {
            log('错误：TinyMCE未加载');
        }
    </script>
</body>
</html>