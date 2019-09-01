<h1 align="center"> laravel-queue-aliyun-mns </h1>

<p align="center"> Aliyun MNS for Laravel/Lumen </p>

<p align="center">
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

## License

[MIT](http://opensource.org/licenses/MIT)