# 七牛云存储插件

Fresns 官方开发的「七牛云」存储服务插件。请通过[点击此链接](https://s.qiniu.com/YNJrYv)注册七牛云账号，不仅是对 Fresns 研发支持，注册后凭关联账号还能获得额外优惠和服务支持。

- 七牛云账号注册链接：[https://s.qiniu.com/YNJrYv](https://s.qiniu.com/YNJrYv)

## 插件安装

- 使用标识名安装: `QiNiu`
- 使用指令安装: `php artisan market:require QiNiu`

## 插件配置

- 在「控制面板 > 系统 > 存储设置」配置存储服务商参数。
- 确保你的服务器安全组“出站规则”开放了 TCP:443 端口，否则需要从服务器转存到七牛云的文件无法出站上传到七牛。

| 存储设置 | 介绍 |
| --- | --- |
| Secret ID | [七牛云 AK(AccessKey)](https://portal.qiniu.com/user/key) |
| Secret Key | [七牛云 SK(SecretKey)](https://portal.qiniu.com/user/key) |
| 存储配置名称 | 空间名称 |
| 存储配置地域 | 华东-浙江 `z0`<br>华东-浙江2 `cn-east-2`<br>华北-河北 `z1`<br>华南-广东 `z2`<br>北美-洛杉矶 `na0`<br>亚太-新加坡 `as0`<br>亚太-首尔 `ap-northeast-1` |
| 存储配置域名 | 空间绑定的域名 |
| 防盗链功能 | 七牛云采用 AK 和 SK 生成防盗链接，防盗链 Key 留空即可<br>七牛平台配置：`空间设置->访问控制->修改为私有空间` |

**图片处理功能配置**

| 功能配置 | 介绍 |
| --- | --- |
| 图片处理位置 | `path-end` |
| 图片处理功能配置 | 图片样式名包含样式分隔符 |

**视频处理功能配置**

| 功能配置 | 介绍 |
| --- | --- |
| 视频转码参数 | 示例 `avthumb/mp4/ab/128k/ar/22050/acodec/libfaac/r/30/vb/300k/vcodec/libx264/s/320x240/autoscale/1/stripmeta/0` |
| 视频转码参数处理位置 | `不使用` |
| 视频封面图参数 | 示例 `?vframe/jpg/offset/2` 或 `?vframe/jpg/offset/2/w/480/h/360`<br>在七牛参数的基础上，开头要加一个英文问号 |
| 视频封面图参数处理位置 | `path-end` |

**音频处理功能配置**

| 功能配置 | 介绍 |
| --- | --- |
| 音频转码参数 | 示例 `avthumb/mp3/ab/128k/ar/44100/acodec/libmp3lame` |
| 音频转码参数处理位置 | `不使用` |

- [查看音视频转码样式](https://developer.qiniu.com/kodo/kb/5858/the-instructions-on-the-storage-space-of-transcoding-style)
- [查看音视频转码参数介绍](https://developer.qiniu.com/dora/1248/audio-and-video-transcoding-avthumb)

## 开发说明

- 配置信息: [https://docs.fresns.cn/database/keyname/storage.html](https://docs.fresns.cn/database/keyname/storage.html)
- 存储服务开发：[https://docs.fresns.cn/extensions/plugin/storage.html](https://docs.fresns.cn/extensions/plugin/storage.html)
- 七牛存储区域: [https://developer.qiniu.com/kodo/1671/region-endpoint-fq](https://developer.qiniu.com/kodo/1671/region-endpoint-fq)

### 安装和卸载

- 安装时，请求订阅命令字，新增订阅。

```php
\FresnsCmdWord::plugin('Fresns')->addSubscribeItem([
    'type' => 1,
    'fskey' => 'QiNiu',
    'cmdWord' => 'qiniuTranscoding'
    'subject' => 'file_usages'
])
```

- 卸载时再次请求取消订阅。

```php
\FresnsCmdWord::plugin('Fresns')->removeSubscribeItem([
    'type' => 1,
    'fskey' => 'QiNiu',
    'cmdWord' => 'qiniuTranscoding'
    'subject' => 'file_usages'
])
```
