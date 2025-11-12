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
