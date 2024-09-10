<?php

return [
    "paths" => [
        "migrations" => "database/migrations",
        "seeds"      => "database/seeds"
    ],
    "environments" => [
        "default_migration_table" => "wa_phinxlog",
        "default_database"        => "dev",
        "default_environment"     => "dev",
        "dev" => [
            "adapter" => "mysql",
            "host"    => "mysql",
            "name"    => "webman",
            "user"    => "root",
            "pass"    => "123456",
            "port"    => "3306",
            "charset" => "utf8"
        ]
    ]
];