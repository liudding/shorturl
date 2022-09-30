<?php

return [

    /**
     * 短链接的 base url
     * 如：https://shorturl.test
     */
    'base_url' => getenv('BASE_URL') ?: 'localhost',

    /**
     * 生成短链接码 code，所使用的字符集
     */
    'charset' => getenv('CHARSET') ?: '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ,

    'code_length' => 4,

    /**
     * 短链接码是否大小写敏感
     * 如果不敏感，则 charset 中不要同时包含大小写字母
     */
    'case_sensitive' => getenv('CASE_SENSITIVE') ?? false,

    /**
     * 如果出现 code 碰撞，则将此后缀拼接到原 URL 后，再此生成 code
     */
    'duplicate_suffix' => getenv('DUPLICATE_SUFFIX') ?: 'DUPLICATE',

    /**
     * 允许 code 碰撞的次数
     */
    'duplicate_tries' => getenv('DUPLICATE_TRIES') ?: 3,

    /**
     * 当短链接无效时，系统行为
     * redirect: 直接重定向到 redirect_url
     * error_page: 展示错误页面
     * redirect_in_error_page: 先展示错误页面，然后重定向到 redirect_url
     */
    'invalid_code_action' => getenv('INVALID_CODE_ACTION') ?: 'error_page', // redirect, error_page, redirect_in_error_page 

    'redirect_url' => getenv('REDIRECT_URL') ?: '/',

    /**
     * 数据库配置
     */
    'db' => [
        'driver' => getenv('DB_DRIVER') ?: 'mysql',
        'dsn' => getenv('DB_DSN'),
        'host' => getenv('DB_HOST') ?: 'localhost',
        'database' => getenv('DB_DATABASE') ?: 'shorturl',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
    ],

    /**
     * 表名配置
     */
    'tables' => [
        'shorturl_urls' => 'shorturl_urls',
        'shorturl_visits' => 'shorturl_visits',
    ]
];