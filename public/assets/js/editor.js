// 初始化富文本编辑器
function initEditor() {
    console.log('正在初始化富文本编辑器...');
    
    // 检查content元素是否存在
    const contentEl = document.getElementById('content');
    if (!contentEl) {
        console.error('未找到ID为content的元素，无法初始化编辑器');
        return;
    }
    
    // 检查TinyMCE是否已加载
    if (typeof tinymce !== 'undefined') {
        console.log('TinyMCE已加载，开始初始化...');
        
        try {
            // 简化编辑器配置，减少可能导致错误的插件
            tinymce.init({
                selector: '#content',
                height: 500,
                menubar: true,
                statusbar: true,
                resize: 'vertical',
                branding: false,
                readonly: false,
                mode: 'design',
                language: 'zh_CN', // 设置语言为中文
                plugins: ['lists', 'link', 'image', 'code', 'paste'],
                toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | image link | code',
                content_style: 'body { font-family: Arial, sans-serif; }',
                automatic_uploads: true,
                images_upload_url: '/upload-image.php',
                relative_urls: false,
                convert_urls: true,
                init_instance_callback: function(editor) {
                    console.log('编辑器初始化成功！');
                    // 确保编辑器处于可编辑模式
                    editor.setMode('design');
                    console.log('编辑器模式:', editor.mode.get());
                }
            });
        } catch (error) {
            console.error('编辑器初始化失败:', error);
            // 初始化失败，显示原始文本框
            contentEl.style.display = 'block';
            contentEl.classList.add('rich-text-area');
        }
    } else {
        console.log('TinyMCE未加载，使用简化编辑器...');
        // 简化编辑器：增强的文本框
        contentEl.style.display = 'block';
        contentEl.classList.add('rich-text-area');
    }
}

// 页面加载完成后初始化编辑器
document.addEventListener('DOMContentLoaded', function() {
    initEditor();
    
    // 添加编辑器切换按钮（如果需要）
    const contentTextarea = document.getElementById('content');
    if (contentTextarea) {
        // 监听表单提交事件，确保编辑器内容同步到textarea
        const form = contentTextarea.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                    contentTextarea.value = tinymce.get('content').getContent();
                }
            });
        }
    }
});
