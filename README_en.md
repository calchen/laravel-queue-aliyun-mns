<h1 align="center"> laravel-queue-aliyun-mns </h1>

<p align="center"> Aliyun MNS for Laravel/Lumen </p>

<p align="center">
    <a href="https://github.styleci.io/repos/205573394">
        <img alt="Style CI" src="https://github.styleci.io/repos/205573394/shield?style=flat">
    </a>
    <a href="https://travis-ci.com/calchen/laravel-queue-aliyun-mns">
        <img alt="Travis CI" src="https://img.shields.io/travis/com/calchen/laravel-queue-aliyun-mns.svg">
    </a>
    <a href='https://coveralls.io/github/calchen/laravel-queue-aliyun-mns?branch=master'>
        <img alt='Coverage Status' src='https://coveralls.io/repos/github/calchen/laravel-queue-aliyun-mns/.svg?branch=master'/>
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

> [中文](https://github.com/calchen/laravel-queue-aliyun-mns/blob/master/README.md)

This is a queue drive for Laravel/Lumen base on [Aliyun MNS SDK](https://github.com/aliyun/aliyun-mns-php-sdk)

## Installing

```shell
$ composer require calchen/laravel-queue-aliyun-mns
```

### Laravel

For Laravel >=5.5, no need to manually add `AliyunMnsServiceProvider` into config. It uses package auto discovery feature. Skip this if you are on >=5.5, if not: 

Open your `AppServiceProvider` (located in `app/Providers`) and add this line in `register` function
```php
$this->app->register(\Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider::class);
```
or open your `config/app.php` and add this line in `providers` section
```php
Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider::class,
```
### Lumen

Open your `bootstrap/app.php` and add this line
```php
$app->register(Calchen\LaravelQueueAliyunMns\AliyunMnsServiceProvider::class);
```

Copy configuration file from `vendor/laravel/lumen-framework/queue.php` to `config/queue.php`

## Configuration

Open your `config/queue.php` and add these lines in `connections` section
```php
'mns' => [
    'driver' => 'mns',
    'access_key_id' => env('ALIYUN_ACCESS_KEY_ID'),
    'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET'),
    'endpoint' => env('ALIYUN_MNS_ENDPOINT'),
    'queue' => env('ALIYUN_MNS_QUEUE'),
],
```

If you want to use MNS by default, set `QUEUE_CONNECTION=mns` in `.env`

### Details
| key               	| required 	| remarks                      	|
|-------------------	|----------	|------------------------------	|
| driver            	| Y        	| default：mns, Do not change! 	|
| access_id         	| Y        	| See 'Security'               	|
| access_key_secret 	| Y        	| See 'Security'               	|
| endpoint          	| Y        	| See 'Endpoint'               	|
| queue            	    | Y        	| -                            	|

#### Endpoint

Open [Aliyun MNS console](https://mns.console.aliyun.com). Select the region where the queue is located. You will see endpoint if click the button 'Get Endpoint'

It's important to note that you can see endpoint may be such `http(s)://1687399289328741.mns.cn-hangzhou.aliyuncs.com/`, but in fact can only use `https://1687399289328741.mns.cn-hangzhou.aliyuncs.com/` or `http://1687399289328741.mns.cn-hangzhou.aliyuncs.com/`

#### Security

For security you should use AccessKey ID and AccessKey Key Secret of RAM users, and should never use AccessKey ID and AccessKey Key Secret of cloud account 

### Reference for RAM policy 

These MNS API is used by current project: GetQueueAttributes、SendMessage、ReceiveMessage、DeleteMessage、ChangeMessageVisibility。

According to [MNS document](https://www.alibabacloud.com/help/doc-detail/47577.html?spm=a2c5t.11065259.1996646101.searchclickresult.300e5a3egCxUQ5#h2-apis-to-policy-action-mapping4) and implement best security practices, grant minimum permissions to users as needed. Here is a example policy for a queue, named for laravel-queue-aliyun-mns, which created in HangZhou China (cn-hangzhou):
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

## License

[MIT](http://opensource.org/licenses/MIT)