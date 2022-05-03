Laravel Mysql Force Master Package
---

## Installing

```shell
composer require jiangslee/laravel-mysql-forcemaster -vvv
```

## Usage

## 使用->forceMaster()手动增加/*FORCE_MASTER*/
```php
$sql = DB::table('users')->select('*')->forceMaster()->toSql();
// /*FORCE_MASTER*/SELECT * FROM `users`


$sql = DB::table('users')->select('*')->toSql();
// SELECT * FROM `users`
```

## 在事务中自动增加/*FORCE_MASTER*/
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
