🎯 功能特点

完全动态配置 - 代码中不需要预设任何SOCKS5信息
URL参数控制 - 通过URL参数实时指定代理服务器
支持多种格式 - 无认证和带认证的SOCKS5都支持
可视化界面 - 提供友好的Web界面生成配置URL

📝 使用方法
方式1: 直接访问带参数的URL
# 无认证代理
https://你的域名/?proxyip=socks5://proxy.example.com:1080

# 带认证的代理
https://你的域名/?proxyip=socks5://username:password@proxy.example.com:1080

# 包含其他参数
https://你的域名/?ed=2560&proxyip=socks5://user:pass@host:port
方式2: 使用Web界面生成

访问 https://你的域名/
在表单中填写SOCKS5服务器信息
点击"生成访问链接"
复制生成的URL或VLESS链接

方式3: 在代理客户端中使用
在V2rayN、Clash等客户端中，将路径(path)设置为：
/?proxyip=socks5://user:pass@host:port
🔑 关键特性

✅ 无需修改代码 - 所有配置通过URL传递
✅ 即时切换 - 更换URL参数即可切换代理
✅ 支持URL编码 - 自动处理特殊字符
✅ 兼容性好 - 支持 socks:// 和 socks5:// 协议
✅ 后备机制 - SOCKS5失败时自动尝试NAT64和直连

🚀 部署到Cloudflare

复制完整代码
在Cloudflare Workers创建新Worker
粘贴代码并部署
设置环境变量 UUID（可选，不设置则使用默认值）

这样你就可以随时通过修改URL参数来使用不同的SOCKS5代理了！





---------------------------------------------------------------
将这个 PHP 文件保存为 subscription.php，上传到：
/usr/home/hbppp/domains/hbppp.serv00.net/public_html/
2️⃣ 设置权限
bashchmod 644 subscription.php
chmod 666 subscription_cache.txt  # 如果需要缓存功能
```

### 3️⃣ **访问订阅**
```
https://hbppp.serv00.net/subscription.php
✨ 功能特点：

✅ 自动缓存 - 同一天内只抓取一次，减少服务器压力
✅ 去除节点名称 - 自动清理 "(mibei77.com 米贝节点分享)"
✅ 支持多协议 - vmess, trojan, vless, ss 等
✅ 错误处理 - 抓取失败时返回友好错误信息
✅ 双重抓取 - curl 优先，失败时使用 file_get_contents

🎯 与 Node.js 版本功能完全相同！
这个 PHP 版本可以直接在 serv00.net 上运行，不需要监听端口！
