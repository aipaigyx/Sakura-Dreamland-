            </div>
        </main>
    </div>
    
    <!-- 退出登录确认模态框 -->
    <div id="logout-modal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 w-full max-w-sm">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-bold">确认退出？</h3>
                <p class="text-gray-600 mt-2">您确定要退出登录吗？</p>
            </div>
            <div class="flex gap-3">
                <button type="button" id="cancel-logout" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors">取消</button>
                <a href="logout.php" class="flex-1 px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors">确认退出</a>
            </div>
        </div>
    </div>
    
    <script>
        // 退出登录模态框
        const logoutBtn = document.querySelector('[href="logout.php"]');
        const logoutModal = document.getElementById('logout-modal');
        const cancelLogout = document.getElementById('cancel-logout');
        
        logoutBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            logoutModal.classList.remove('hidden');
        });
        
        cancelLogout?.addEventListener('click', () => {
            logoutModal.classList.add('hidden');
        });
    </script>
</body>
</html>