# 🌸 樱花梦境博客系统
![GitHub](https://img.shields.io/github/license/sakuradream/aniblog)
![GitHub stars](https://img.shields.io/github/stars/sakuradream/aniblog?style=social)
![GitHub forks](https://img.shields.io/github/forks/sakuradream/aniblog?style=social)
![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-%2338B2AC)

一款专为二次元爱好者打造的现代化博客系统，聚焦动漫、游戏及二次元文化内容分享，兼顾美观度与实用性，开箱即用且易于拓展。

## ✨ 核心特性
- **🎨 二次元响应式设计**：基于Tailwind CSS构建，适配全设备，支持主题自定义
- **📦 全量内容模块**：文章/图片画廊/角色生成器/资讯/视频卡片，满足多元展示需求
- **💬 沉浸式互动**：评论、点赞、收藏、实时弹幕，打造专属二次元社区
- **👤 完整用户体系**：注册/登录/个人中心，账号安全与个性化管理兼备
- **🛠️ 高效后台**：一站式内容/用户/系统管理，零基础也能轻松运维

## 🛠️ 技术栈
| 分类       | 技术清单                                  |
|------------|-------------------------------------------|
| 前端       | HTML5 / CSS3 / JS(ES6+) / Tailwind CSS / GSAP |
| 后端       | PHP 8.x / MySQL 5.7+ / Redis 6.0+（可选） |
| 部署环境   | Apache 2.4+ / Nginx 1.18+ / 宝塔面板      |
| 架构模式   | 模块化设计 + 模板驱动                     |

## 📂 核心目录结构
```bash
├── public/                # 主网站访问目录
│   ├── admin/            # 管理后台
│   ├── assets/           # 静态资源（css/images/js）
│   ├── uploads/          # 文件上传目录
│   └── user/             # 用户中心
├── app/                  # 核心业务逻辑
├── tests/                # 测试文件
└── vendor/               # Composer依赖
```

## 🚀 快速安装
### 环境要求
- PHP 8.0+（开启fileinfo扩展）、MySQL 5.7+
- Apache/Nginx、Redis 6.0+（可选）

### 普通服务器安装
```bash
# 1. 克隆项目
git clone https://github.com/aipaigyx/Sakura-Dreamland-.git
cd Sakura-Dreamland-

# 2. 创建数据库并导入初始数据
# 3. 配置数据库信息（修改public/db.php）

# 4. 设置目录权限
chmod -R 755 public/uploads/ public/cache/ public/sessions/

# 5. 安装依赖
composer install

# 6. 访问网站（浏览器打开域名/服务器IP）
```

### 宝塔面板安装（新手推荐）
1. 宝塔面板 → 网站 → 添加站点（选择PHP 8.0+）
2. 安装扩展：fileinfo、redis（如需缓存）
3. 网站根目录 → 远程下载/上传解压项目
4. 配置数据库信息，设置目录权限为755
5. 访问域名即可使用

#### 初始管理员账号
```
账号：admin@example.com
密码：admin123
```

## 📌 核心功能
| 模块         | 核心能力                                  |
|--------------|-------------------------------------------|
| 内容管理     | 富文本文章/瀑布流画廊/角色生成器/资讯/视频卡片 |
| 用户管理     | 邮箱注册/验证登录/密码重置/个人中心       |
| 互动系统     | 评论回复/点赞收藏/实时弹幕                |
| 后台管理     | 内容管控/用户权限/系统配置/数据备份        |

## ❓ 常见问题
<details>
<summary>无法上传图片</summary>
1. 检查uploads目录权限是否为755；2. 调整PHP上传文件大小限制；3. 确认图片格式为jpg/png/gif。
</details>

<details>
<summary>网站加载缓慢</summary>
1. 启用Redis缓存；2. 压缩图片资源；3. 检查服务器带宽/CPU占用；4. 开启CDN加速。
</details>

<details>
<summary>后台登录失败</summary>
1. 核对账号密码；2. 检查数据库连接配置；3. 清除浏览器缓存/Cookie。
</details>

## 🤝 贡献指南
1. Fork本仓库 → 克隆到本地 → 创建功能分支（feature/xxx）
2. 开发功能/修复Bug → 提交代码（git commit -m "feat: 新增XXX功能"）
3. 推送分支 → 创建Pull Request → 等待审核合并

## 📞 联系我们
- 邮箱：2208850891@qq.com
- 官网：https://www.sakuradream.com
- GitHub：https://github.com/aipaigyx/Sakura-Dreamland-
- B站：https://space.bilibili.com/12644772
- QQ群：1081909009

## 📜 许可证
本项目基于 MIT 协议开源，可自由使用、修改、分发（需保留原版权声明）。

---
<div align="center">© 2026 樱花梦境 · 二次元博客系统</div>
