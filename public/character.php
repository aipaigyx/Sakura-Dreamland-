<?php
/**
 * 角色生成器页面模板
 */

// 加载功能函数
require_once __DIR__ . '/functions.php';

// 加载页面模板
include __DIR__ . '/header.php';
?>
    <!-- 主内容区 -->
    <main class="container mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16">
        <!-- 页面标题 -->
        <section class="mb-12">
            <div class="modern-card p-8 text-center">
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-pink-500 to-purple-600">
                    角色生成器
                </h1>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                    自定义你的二次元角色，调整发型、服装、配饰等属性，创建独一无二的角色形象
                </p>
            </div>
        </section>
        
        <!-- 角色生成器主体 -->
        <section class="mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- 左侧：属性控制面板 -->
                <div class="lg:col-span-1">
                    <div class="modern-card p-6">
                        <h2 class="text-xl font-bold mb-6 text-gray-800">角色属性</h2>
                        
                        <!-- 角色名称 -->
                        <div class="mb-6">
                            <label for="character-name" class="block text-sm font-medium text-gray-700 mb-2">角色名称</label>
                            <input type="text" id="character-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all duration-200" placeholder="请输入角色名称">
                        </div>
                        
                        <!-- 发型选择 -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">发型</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button class="aspect-square bg-pink-100 rounded-lg border-2 border-pink-500 hover:bg-pink-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                            </div>
                        </div>
                        
                        <!-- 服装选择 -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">服装</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button class="aspect-square bg-blue-100 rounded-lg border-2 border-blue-500 hover:bg-blue-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                            </div>
                        </div>
                        
                        <!-- 配饰选择 -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">配饰</label>
                            <div class="grid grid-cols-4 gap-2">
                                <button class="aspect-square bg-green-100 rounded-lg border-2 border-green-500 hover:bg-green-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                                <button class="aspect-square bg-gray-100 rounded-lg border-2 border-transparent hover:bg-gray-200 transition-colors duration-200"></button>
                            </div>
                        </div>
                        
                        <!-- 颜色选择 -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">主题颜色</label>
                            <div class="flex gap-2 flex-wrap">
                                <button class="w-8 h-8 bg-pink-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                                <button class="w-8 h-8 bg-blue-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                                <button class="w-8 h-8 bg-green-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                                <button class="w-8 h-8 bg-purple-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                                <button class="w-8 h-8 bg-yellow-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                                <button class="w-8 h-8 bg-red-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                                <button class="w-8 h-8 bg-indigo-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                                <button class="w-8 h-8 bg-teal-500 rounded-full border-2 border-white shadow-md hover:scale-110 transition-transform duration-200"></button>
                            </div>
                        </div>
                        
                        <!-- 生成按钮 -->
                        <button class="w-full py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium rounded-lg hover:opacity-90 transition-opacity duration-200 flex items-center justify-center">
                            <i class="fas fa-magic mr-2"></i> 生成角色
                        </button>
                    </div>
                </div>
                
                <!-- 右侧：角色预览和操作 -->
                <div class="lg:col-span-2">
                    <!-- 角色预览 -->
                    <div class="modern-card p-8 mb-6">
                        <div class="aspect-square bg-gradient-to-br from-pink-100 to-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-6xl text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- 操作按钮 -->
                    <div class="modern-card p-6">
                        <h2 class="text-xl font-bold mb-6 text-gray-800">操作</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <button class="py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> 保存角色
                            </button>
                            <button class="py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-download mr-2"></i> 下载图片
                            </button>
                            <button class="py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-share-alt mr-2"></i> 分享
                            </button>
                        </div>
                        
                        <!-- 重置按钮 -->
                        <button class="w-full py-2 mt-4 text-gray-500 font-medium hover:text-gray-700 transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-redo-alt mr-2"></i> 重置所有属性
                        </button>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- 最近生成的角色 -->
        <section>
            <div class="modern-card p-6">
                <h2 class="text-xl font-bold mb-6 text-gray-800">最近生成的角色</h2>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <!-- 角色预览 1 -->
                    <div class="aspect-square bg-gradient-to-r from-pink-100 to-purple-100 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300"></div>
                    
                    <!-- 角色预览 2 -->
                    <div class="aspect-square bg-gradient-to-r from-blue-100 to-cyan-100 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300"></div>
                    
                    <!-- 角色预览 3 -->
                    <div class="aspect-square bg-gradient-to-r from-green-100 to-emerald-100 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300"></div>
                    
                    <!-- 角色预览 4 -->
                    <div class="aspect-square bg-gradient-to-r from-yellow-100 to-orange-100 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300"></div>
                    
                    <!-- 角色预览 5 -->
                    <div class="aspect-square bg-gradient-to-r from-purple-100 to-indigo-100 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300"></div>
                    
                    <!-- 角色预览 6 -->
                    <div class="aspect-square bg-gradient-to-r from-red-100 to-pink-100 rounded-lg overflow-hidden hover:scale-105 transition-transform duration-300"></div>
                </div>
            </div>
        </section>
    </main>
<?php
// 加载页脚
include __DIR__ . '/footer.php';

