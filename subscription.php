<?php
/**
 * 订阅内容代理脚本 - PHP 版本
 * 适用于 serv00.net 等共享主机
 */

// 设置响应头
header('Content-Type: text/plain; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// 缓存设置（1小时）
$cacheFile = __DIR__ . '/subscription_cache.txt';
$cacheTime = 3600; // 1小时缓存

/**
 * 获取当前日期
 */
function getFormattedDate() {
    return [
        'year' => date('Y'),
        'month' => date('m'),
        'day' => date('d')
    ];
}

/**
 * 处理节点内容，去除指定文本
 */
function processNodeContent($content) {
    try {
        // 解码 base64
        $decoded = base64_decode($content);
        if ($decoded === false) {
            return $content;
        }
        
        // 按行分割
        $lines = explode("\n", $decoded);
        
        // 处理每一行
        $processedLines = array_map(function($line) {
            $line = trim($line);
            if (empty($line)) {
                return $line;
            }
            
            // 检查是否包含协议
            if (strpos($line, '://') === false) {
                return $line;
            }
            
            // 分离协议和内容
            $parts = explode('://', $line, 2);
            if (count($parts) !== 2) {
                return $line;
            }
            
            $protocol = $parts[0];
            $rest = $parts[1];
            
            // 处理 vmess 协议
            if ($protocol === 'vmess') {
                try {
                    $vmessDecoded = base64_decode($rest);
                    $vmessObj = json_decode($vmessDecoded, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && isset($vmessObj['ps'])) {
                        // 去除节点名称中的指定文本
                        $vmessObj['ps'] = preg_replace(
                            '/\s*\(?\s*mibei77\.com\s*米贝节点分享\s*\)?\s*/ui',
                            '',
                            $vmessObj['ps']
                        );
                        $vmessObj['ps'] = trim($vmessObj['ps']);
                        
                        $newVmess = base64_encode(json_encode($vmessObj, JSON_UNESCAPED_UNICODE));
                        return $protocol . '://' . $newVmess;
                    }
                } catch (Exception $e) {
                    error_log('处理 vmess 节点错误: ' . $e->getMessage());
                }
            }
            
            // 处理其他协议（trojan, vless, ss 等）
            if (strpos($line, '#') !== false) {
                try {
                    $urlParts = explode('#', $line);
                    $nodeName = urldecode($urlParts[count($urlParts) - 1]);
                    
                    // 去除指定文本
                    $cleanedName = preg_replace(
                        '/\s*\(?\s*mibei77\.com\s*米贝节点分享\s*\)?\s*/ui',
                        '',
                        $nodeName
                    );
                    $cleanedName = trim($cleanedName);
                    
                    $urlParts[count($urlParts) - 1] = urlencode($cleanedName);
                    return implode('#', $urlParts);
                } catch (Exception $e) {
                    error_log('处理节点名称错误: ' . $e->getMessage());
                }
            }
            
            return $line;
        }, $lines);
        
        // 重新编码为 base64
        $processed = implode("\n", $processedLines);
        return base64_encode($processed);
        
    } catch (Exception $e) {
        error_log('处理内容错误: ' . $e->getMessage());
        return $content;
    }
}

/**
 * 抓取订阅内容
 */
function fetchSubscriptionContent() {
    $dateInfo = getFormattedDate();
    $year = $dateInfo['year'];
    $month = $dateInfo['month'];
    $day = $dateInfo['day'];
    
    $targetUrl = "https://node.freeclashnode.com/uploads/{$year}/{$month}/1-{$year}{$month}{$day}.txt";
    
    // 使用 curl 抓取（更可靠）
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $targetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $content !== false) {
            return $content;
        }
    }
    
    // 备用方案：使用 file_get_contents
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: Mozilla/5.0\r\n",
            'timeout' => 30
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    
    $content = @file_get_contents($targetUrl, false, $context);
    
    if ($content === false) {
        throw new Exception('无法获取订阅内容');
    }
    
    return $content;
}

/**
 * 主逻辑
 */
try {
    $useCache = false;
    
    // 检查缓存
    if (file_exists($cacheFile)) {
        $cacheAge = time() - filemtime($cacheFile);
        $cacheDate = date('Y-m-d', filemtime($cacheFile));
        $today = date('Y-m-d');
        
        // 如果是今天的缓存且未过期，使用缓存
        if ($cacheDate === $today && $cacheAge < $cacheTime) {
            $useCache = true;
        }
    }
    
    if ($useCache) {
        // 使用缓存
        $content = file_get_contents($cacheFile);
        echo $content;
    } else {
        // 抓取新内容
        $rawContent = fetchSubscriptionContent();
        
        // 处理内容
        $processedContent = processNodeContent($rawContent);
        
        // 保存缓存
        file_put_contents($cacheFile, $processedContent);
        
        // 输出内容
        echo $processedContent;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => '无法获取订阅内容',
        'message' => $e->getMessage()
    ]);
}
?>
