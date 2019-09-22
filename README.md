<h1 align="center"> laravel-queue-aliyun-mns </h1>

<p align="center"> 阿里云消息服务（MNS） Laravel/Lumen 扩展包 </p>

<p align="center">
    <a href="https://github.styleci.io/repos/205573394">
        <img alt="Style CI" src="https://github.styleci.io/repos/205573394/shield?style=flat">
    </a>
    <a href="https://travis-ci.com/calchen/laravel-queue-aliyun-mns">
        <img alt="Travis CI" src="https://img.shields.io/travis/com/calchen/laravel-queue-aliyun-mns.svg">
    </a>
    <a href='https://coveralls.io/github/calchen/laravel-queue-aliyun-mns?branch=master'>
        <img alt='Coverage Status' src='https://coveralls.io/repos/github/calchen/laravel-queue-aliyun-mns/badge.svg?branch=master'/>
    </a>
    <a href="https://packagist.org/packages/calchen/laravel-queue-aliyun-mns">
        <img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/calchen/laravel-queue-aliyun-mns.svg">
    </a>
    <a href="https://packagist.org/packages/calchen/laravel-queue-aliyun-mns">
        <img alt="Total Downloads" src="https://img.shields.io/packagist/dt/calchen/laravel-queue-aliyun-mns.svg">
    </a>
    <a href="https://github.com/calchen/laravel-queue-aliyun-mns/blob/master/LICENSE">
        <img alt="License" src="https://img.shields.io/github/license/calchen/laravel-queue-aliyun-mns.svg">
    </a>
</p>

> [English](https://github.com/calchen/laravel-queue-aliyun-mns/blob/master/README_en.md)

这是一个基于[阿里云 MNS SDK](https://github.com/aliyun/aliyun-mns-php-sdk)的 Laravel/Lumen 队列驱动扩展包 

## 安装

Laravel/Lumen 5.5 ~ 5.6 [Docs](https://github.com/calchen/laravel-queue-aliyun-mns/blob/1.0/README.md)

```shell
$ composer require calchen/laravel-queue-aliyun-mns:^1.0
```

Laravel/Lumen 5.7+

```shell
$ composer require calchen/laravel-queue-aliyun-mns:^2.0
```

### Laravel

如果您的 Laravel 版本为 5.5 及以上，您不需要手动的配置文件中添加 `AliyunMnsServiceProvider` Laravel 自带的扩展包发现机制会处理好一切。如是小于 5.5 版本那么需要您进行如下操作: 

打开位于 `app/Providers` 的 `AppServiceProvider.php` 文件并在 `register` 函数中添加如下内容：
```php
$this->app->register(\Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider::class);
```
您也可以在配置文件 `config/app.php` 中的 `providers` 中添加如下内容：
```php
Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider::class,
```
只需选择以上操作中的一种，即可加载本扩招包。

### Lumen

Lumen 并未移植扩展包自动发现机制，所以需要手动加载扩展包并复制配置文件。

打开配置文件 `bootstrap/app.php` 并在大约 81 行左右添加如下内容：
```php
$app->register(Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider::class);
```

将文件系统配置文件从 `vendor/laravel/lumen-framework/queue.php` 复制到 `config/queue.php`

## 配置

打开配置文件 `config/queue.php` 并在 `connections` 中添加如下内容：
```php
'mns' => [
    'driver' => 'mns',
    'access_key_id' => env('ALIYUN_ACCESS_KEY_ID'),
    'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET'),
    'endpoint' => env('ALIYUN_MNS_ENDPOINT'),
    'queue' => env('ALIYUN_MNS_QUEUE'),
],
```

如果您想将阿里云 MNS 作为默认的队列，那么可以在 `.env` 文件中设置配置项 `QUEUE_CONNECTION=mns`

### 配置说明
| 配置项                	| 必须 	| 说明                                 	| 备注                  	|
|-------------------	|------	|--------------------------------------	|-----------------------	|
| driver            	| 是   	| 驱动名称                             	| 默认值：mns，不可修改   	|
| access_id         	| 是   	| 用于身份验证的 AccessKey ID          	| 见下文“安全提醒”         	|
| access_key_secret 	| 是   	| 用于身份验证的  AccessKey Key Secret 	| 见下文“安全提醒”      	    |
| endpoint          	| 是   	| 地域节点                             	| 见下文“地域节点”      	    |
| queue          	    | 是   	| 队列名称                             	| -                   	    |
| wait_seconds          | 否   	| 即长轮询时长                            | [消费消息](https://help.aliyun.com/document_detail/35136.html?spm=a2c4g.11186623.6.675.37e35c40Hzv2FW#h2-request)|

#### 地域节点（endpoint）

在[阿里云 MNS 控制台](https://mns.console.aliyun.com)选择正确的区域后，点击“获取 Endpoint”按钮查看对应的地域节点。

需要注意的是您看到的地域节点可能是这样的 `http(s)://1687399289328741.mns.cn-hangzhou.aliyuncs.com/`，但实际上只能使用 `https://1687399289328741.mns.cn-hangzhou.aliyuncs.com/` 或 `http://1687399289328741.mns.cn-hangzhou.aliyuncs.com/`

#### 安全提醒

为了安全，请使用子账户的 AccessKey ID 和 AccessKey Key Secret，请务必不要使用主账户的 AccessKey ID 和 AccessKey Key Secret

### RAM 访问控制权限策略参考

本项目使用了阿里云 MNS 的这些方法：GetQueueAttributes、SendMessage、ReceiveMessage、DeleteMessage、ChangeMessageVisibility。

根据[阿里云 MNS 文档](https://help.aliyun.com/document_detail/27448.html?spm=a2c4g.11186623.6.597.27f31b2dQ1x64i)并践行最佳安全实践，为 RAM 用户授予最小权限。这里以杭州区（cn-hangzhou）名称为 laravel-queue-aliyun-mns 的队列为例：
```json
{
    "Version": "1",
    "Statement": [
        {
            "Action": "mns:GetQueueAttributes",
            "Resource": [
                "acs:mns:cn-hangzhou:*:/queues/laravel-queue-aliyun-mns"
            ],
            "Effect": "Allow"
        },
        {
            "Action": [
                "mns:SendMessage",
                "mns:ReceiveMessage",
                "mns:DeleteMessage",
                "mns:ChangeMessageVisibility"
            ],
            "Resource": [
                "acs:mns:cn-hangzhou:*:/queues/laravel-queue-aliyun-mns/messages"
            ],
            "Effect": "Allow"
        }
    ]
}
```

## 开源协议

[MIT](http://opensource.org/licenses/MIT)
