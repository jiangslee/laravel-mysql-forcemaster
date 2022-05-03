<?php

namespace Jiangslee\LaravelMySqlForceMasterPackage;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
    }

    public function register()
    {
        // 如何给所有的select语句增加Hint语句，如：“/*FORCE_MASTER*/”
        // https://learnku.com/laravel/t/66526?#reply222886
        Builder::macro('forceMaster', function () {
            $this->force_master = true;
            return $this;
        });

        Builder::macro('isForceMaster', function () {
            return $this->force_master ?? false;
        });

        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            $connection = new MySqlConnection($connection, $database, $prefix, $config);
            $connection->setQueryGrammar(new class () extends MySqlGrammar {
                protected function compileColumns(Builder $query, $columns)
                {
                    $sql = parent::compileColumns($query, $columns);

                    if (
                        // 事务处理中，添加/*FORCE_MASTER*/
                        $query->connection->transactionLevel() > 0 ||
                        // 写库的情况下，添加/*FORCE_MASTER*/
                        $query->useWritePdo ||
                        // query()->isForceMaster()的情况下，添加/*FORCE_MASTER*/
                        $query->isForceMaster()
                    ) {
                        $sql = '/*FORCE_MASTER*/' . $sql;
                    }

                    return $sql;
                }
            });

            return $connection;
        });

        // 用例：$sql = DB::table('users')->select('*')->forceMaster()->toSql();
        // /*FORCE_MASTER*/SELECT * FROM `users`
    }
}
