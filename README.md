# Laravel Mysql Force Master Package
---

# RDS MySQL读写分离如何确保数据读取的时效性 

>阿里云内部网络会确保同步日志在主实例和只读实例间的实时传输，正常情况下只读实例不会有延迟产生。但受限于MySQL本身的复制机制，若同步日志的应用时间较久，会产生数据同步的延迟，这个是MySQL尚无法在技术手段上规避的问题。为减小延迟，建议您的只读实例规格不小于主实例，从而确保有足够高的性能来应用同步日志。
RDS支持用户设置延迟阈值，当某个只读实例的延迟超过该阈值时，系统会不再转发任何请求至该实例。当所有只读实例均超过延迟阈值时，请求直接路由到主库，不管主库的读权重是否开启。
在使用读写分离过程中，若您需要某些查询语句获取实时性的数据，可通过Hint格式将这些查询语句强制转发至主实例执行。RDS读写分离支持的Hint格式为`/*FORCE_MASTER*/`，指定后续SQL到主实例执行。示例如下：

```sql
/*FORCE_MASTER*/ SELECT * FROM table_name;
```

相关链接：
https://help.aliyun.com/document_detail/52221.html

https://learnku.com/laravel/t/66526?#reply222886

## Installing

```shell
composer require jiangslee/laravel-mysql-forcemaster -vvv
```

## Usage

## 使用`->forceMaster()`手动增加`/*FORCE_MASTER*/`
```php
$sql = DB::table('users')->select('*')->forceMaster()->toSql();
// /*FORCE_MASTER*/SELECT * FROM `users`


$sql = DB::table('users')->select('*')->toSql();
// SELECT * FROM `users`
```

## 在事务中自动增加`/*FORCE_MASTER*/`
```php
DB::beginTransaction();
$users = User::first();
try {
    $users->name = 'test';
    $users->save();
    dump($users->toSql());
    // "/*FORCE_MASTER*/select * from `users`"

    $customers = CrmCustomer::first();
    dump($customers->toSql());
    // /*FORCE_MASTER*/select * from `crm_customers`

    $contacts = CrmContact::query()->first();
    dump($contacts->toSql());
    // /*FORCE_MASTER*/select * from `crm_contacts`

    // throw new Exception('test');
    DB::commit();
} catch (\Throwable $th) {
    dump($users->toSql());
    DB::rollBack();
}

dump($users->toSql());
// select * from `users`
```


## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/jiangslee/laravel-mysql-forcemaster/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/jiangslee/laravel-mysql-forcemaster/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._


## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
