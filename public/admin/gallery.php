<?php
/**
 * 图片管理页面
 */

// 包含认证中间件
require_once 'auth.php';

// 检查权限
require_permission('manage_images');

// 设置页面标题和描述
$page_title = '图片管理';
$page_description = '管理网站的所有图片';

// 包含公共头部
require_once 'header.php';

// 包含功能函数
require_once '../functions.php';

// 获取所有图片
$images = get_all_images();
$categories = aniblog_get_categories();
?>
            <!-- 图片管理 -->
            <div class="card p-6">
                <!-- 上传图片模态框 -->
                <div id="uploadModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-xl p-6 w-full max-w-md">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold">上传图片</h3>
                            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <form id="uploadForm" enctype="multipart/form-data" method="POST" action="/upload-image.php">
                            <div class="space-y-4">
                                <div>
                                    <label for="imageFile" class="block text-sm font-medium text-gray-700 mb-1">选择图片</label>
                                    <input type="file" id="imageFile" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                                <div>
                                    <label for="imageTitle" class="block text-sm font-medium text-gray-700 mb-1">图片标题</label>
                                    <input type="text" id="imageTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="输入图片标题">
                                </div>
                                <div>
                                    <label for="imageDescription" class="block text-sm font-medium text-gray-700 mb-1">图片描述</label>
                                    <textarea id="imageDescription" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500" placeholder="输入图片描述"></textarea>
                                </div>
                                <div>
                                    <label for="imageCategory" class="block text-sm font-medium text-gray-700 mb-1">分类</label>
                                    <select id="imageCategory" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <option value="">未分类</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex gap-3">
                                    <button type="submit" class="flex-1 btn-primary px-4 py-2 rounded-lg text-white hover:bg-pink-600 transition-colors duration-200">
                                        <i class="fas fa-upload mr-2"></i>上传图片
                                    </button>
                                    <button type="button" id="cancelUpload" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        取消
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- 编辑图片模态框 -->
                <div id="editModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-xl p-6 w-full max-w-md">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold">编辑图片</h3>
                            <button id="closeEditModal" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <form id="editForm">
                            <input type="hidden" id="editImageId" name="image_id">
                            <div class="space-y-4">
                                <div>
                                    <label for="editTitle" class="block text-sm font-medium text-gray-700 mb-1">图片标题</label>
                                    <input type="text" id="editTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                                </div>
                                <div>
                                    <label for="editDescription" class="block text-sm font-medium text-gray-700 mb-1">图片描述</label>
                                    <textarea id="editDescription" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500"></textarea>
                                </div>
                                <div>
                                    <label for="editCategory" class="block text-sm font-medium text-gray-700 mb-1">分类</label>
                                    <select id="editCategory" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                                        <option value="">未分类</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="flex gap-3">
                                    <button type="submit" class="flex-1 btn-primary px-4 py-2 rounded-lg text-white hover:bg-pink-600 transition-colors duration-200">
                                        <i class="fas fa-save mr-2"></i>保存修改
                                    </button>
                                    <button type="button" id="cancelEdit" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        取消
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- 删除确认模态框 -->
                <div id="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-xl p-6 w-full max-w-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold">确认删除</h3>
                            <button id="closeDeleteModal" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <div class="mb-6">
                            <p class="text-gray-600">您确定要删除图片 <span id="deleteImageTitle" class="font-medium text-pink-500"></span> 吗？</p>
                            <p class="text-xs text-gray-500 mt-2">此操作不可恢复，请谨慎操作。</p>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" id="confirmDelete" class="flex-1 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>删除
                            </button>
                            <button type="button" id="cancelDelete" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                取消
                            </button>
                        </div>
                        <input type="hidden" id="deleteImageId">
                    </div>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold">图片列表</h3>
                    <button type="button" id="openUploadModal" class="btn-primary px-4 py-2 rounded-lg text-white hover:bg-pink-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>上传图片
                    </button>
                </div>
                
                <!-- 筛选和搜索 -->
                <div class="flex flex-wrap items-center gap-4 mb-6">
                    <div class="flex items-center gap-2">
                        <label for="image-category-filter" class="text-sm font-medium text-gray-700">分类：</label>
                        <select id="image-category-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <option value="">全部</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" placeholder="搜索图片标题..." 
                                   class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                
                <!-- 图片网格 -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $image): ?>
                            <div class="group relative" data-image-id="<?php echo $image['id']; ?>">
                                <div class="aspect-square bg-gradient-to-r from-pink-300 to-purple-300 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                    <img src="<?php echo $image['file_path']; ?>" alt="<?php echo $image['title']; ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs text-white font-medium truncate mb-1"><?php echo $image['title']; ?></h4>
                                        <p class="text-xs text-pink-200 truncate"><?php echo $image['category_name'] ?? '未分类'; ?></p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button class="p-2 bg-white/20 rounded-full hover:bg-white/40 transition-colors duration-200 edit-image-btn" 
                                                data-image-id="<?php echo $image['id']; ?>" 
                                                data-title="<?php echo htmlspecialchars($image['title']); ?>" 
                                                data-description="<?php echo htmlspecialchars($image['description']); ?>" 
                                                data-category="<?php echo $image['category_id']; ?>">
                                            <i class="fas fa-edit text-white text-xs"></i>
                                        </button>
                                        <button class="p-2 bg-white/20 rounded-full hover:bg-white/40 transition-colors duration-200 delete-image-btn" 
                                                data-image-id="<?php echo $image['id']; ?>" 
                                                data-image-title="<?php echo htmlspecialchars($image['title']); ?>">
                                            <i class="fas fa-trash text-white text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-images text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">暂无图片</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- 分页 -->
                <div class="flex items-center justify-center mt-8">
                    <nav class="inline-flex items-center space-x-2">
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white">
                            1
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            2
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            3
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            ...
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            10
                        </button>
                        <button class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </nav>
                </div>
            </div>

<script>
    // 图片上传模态框
    const uploadModal = document.getElementById('uploadModal');
    const openUploadModalBtn = document.getElementById('openUploadModal');
    const closeModalBtn = document.getElementById('closeModal');
    const cancelUploadBtn = document.getElementById('cancelUpload');
    const uploadForm = document.getElementById('uploadForm');
    
    // 打开模态框
    openUploadModalBtn.addEventListener('click', () => {
        uploadModal.classList.remove('hidden');
    });
    
    // 关闭模态框
    const closeModal = () => {
        uploadModal.classList.add('hidden');
        uploadForm.reset();
    };
    
    closeModalBtn.addEventListener('click', closeModal);
    cancelUploadBtn.addEventListener('click', closeModal);
    
    // 点击模态框外部关闭
    uploadModal.addEventListener('click', (e) => {
        if (e.target === uploadModal) {
            closeModal();
        }
    });
    
    // 表单提交处理
    uploadForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData(uploadForm);
        
        // 显示加载状态
        const submitBtn = uploadForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>上传中...';
        submitBtn.disabled = true;
        
        // 发送AJAX请求
        fetch('/upload-image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            // 检查响应状态
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            // 检查响应类型
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error('Invalid response type. Expected JSON, got: ' + text);
                });
            }
            return response.json();
        })
        .then(data => {
            // 恢复按钮状态
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (data.success) {
                // 显示成功消息
                alert('图片上传成功！');
                closeModal();
                // 刷新页面以显示新图片
                location.reload();
            } else {
                // 显示错误消息
                alert('上传失败：' + data.message);
            }
        })
        .catch(error => {
            // 恢复按钮状态
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            console.error('上传请求失败:', error);
            alert('上传失败，请稍后重试：' + error.message);
        });
    });
    
    // 编辑图片功能
    const editModal = document.getElementById('editModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEdit');
    const editForm = document.getElementById('editForm');
    const editImageId = document.getElementById('editImageId');
    const editTitle = document.getElementById('editTitle');
    const editDescription = document.getElementById('editDescription');
    const editCategory = document.getElementById('editCategory');
    
    // 打开编辑模态框
    const editImageBtns = document.querySelectorAll('.edit-image-btn');
    editImageBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const imageId = btn.dataset.imageId;
            const title = btn.dataset.title;
            const description = btn.dataset.description;
            const category = btn.dataset.category;
            
            // 填充表单数据
            editImageId.value = imageId;
            editTitle.value = title;
            editDescription.value = description;
            editCategory.value = category;
            
            // 显示模态框
            editModal.classList.remove('hidden');
        });
    });
    
    // 关闭编辑模态框
    const closeEditModal = () => {
        editModal.classList.add('hidden');
        editForm.reset();
    };
    
    closeEditModalBtn.addEventListener('click', closeEditModal);
    cancelEditBtn.addEventListener('click', closeEditModal);
    
    // 点击模态框外部关闭
    editModal.addEventListener('click', (e) => {
        if (e.target === editModal) {
            closeEditModal();
        }
    });
    
    // 编辑表单提交处理
    editForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData(editForm);
        const urlEncodedData = new URLSearchParams(formData).toString();
        
        // 显示加载状态
        const submitBtn = editForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>保存中...';
        submitBtn.disabled = true;
        
        // 发送AJAX请求
        fetch('/admin/image-edit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: urlEncodedData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // 恢复按钮状态
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            if (data.success) {
                // 显示成功消息
                alert('图片信息已成功更新！');
                closeEditModal();
                // 刷新页面以显示更新后的图片
                location.reload();
            } else {
                // 显示错误消息
                alert('更新失败：' + data.message);
            }
        })
        .catch(error => {
            // 恢复按钮状态
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            console.error('更新请求失败:', error);
            alert('更新失败，请稍后重试：' + error.message);
        });
    });
    
    // 删除图片功能
    const deleteModal = document.getElementById('deleteModal');
    const closeDeleteModalBtn = document.getElementById('closeDeleteModal');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const deleteImageTitle = document.getElementById('deleteImageTitle');
    const deleteImageId = document.getElementById('deleteImageId');
    
    // 打开删除确认模态框
    const deleteImageBtns = document.querySelectorAll('.delete-image-btn');
    deleteImageBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const imageId = btn.dataset.imageId;
            const imageTitle = btn.dataset.imageTitle;
            
            // 设置删除信息
            deleteImageId.value = imageId;
            deleteImageTitle.textContent = imageTitle;
            
            // 显示模态框
            deleteModal.classList.remove('hidden');
        });
    });
    
    // 关闭删除模态框
    const closeDeleteModal = () => {
        deleteModal.classList.add('hidden');
    };
    
    closeDeleteModalBtn.addEventListener('click', closeDeleteModal);
    cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    
    // 点击模态框外部关闭
    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            closeDeleteModal();
        }
    });
    
    // 确认删除
    confirmDeleteBtn.addEventListener('click', () => {
        const imageId = deleteImageId.value;
        
        // 显示加载状态
        const originalText = confirmDeleteBtn.innerHTML;
        confirmDeleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>删除中...';
        confirmDeleteBtn.disabled = true;
        
        // 发送AJAX请求
        fetch('/admin/image-delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                'image_id': imageId
            }).toString()
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // 恢复按钮状态
            confirmDeleteBtn.innerHTML = originalText;
            confirmDeleteBtn.disabled = false;
            
            if (data.success) {
                // 显示成功消息
                alert('图片已成功删除！');
                closeDeleteModal();
                // 刷新页面以显示更新后的图片列表
                location.reload();
            } else {
                // 显示错误消息
                alert('删除失败：' + data.message);
            }
        })
        .catch(error => {
            // 恢复按钮状态
            confirmDeleteBtn.innerHTML = originalText;
            confirmDeleteBtn.disabled = false;
            
            console.error('删除请求失败:', error);
            alert('删除失败，请稍后重试：' + error.message);
        });
    });
</script>
<?php
// 包含公共尾部
require_once 'footer.php';
