<?php

return [

    'base_url' => '',

    /**
     * 生成短链接码 code，所使使用的字符集
     */
    'charset' => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',

    /**
     * 短链接码是否大小写敏感
     * 如果敏感，则 charset 中不能同时包含大小写字母
     */
    'case_sensitive' => true,

    'size' => 6,

    /**
     * 如果出现 code 碰撞，则将此后缀拼接到原 URL 后，再此生成 code
     */

    'duplicate_suffix' => 'DUPLICATE',

    /**
     * 允许 code 碰撞的次数
     */
    'duplicate_tries' => 3,

    'db' => [
        'driver' => 'mysql',
        'host' => '',
        'database' => '',
        'user' => '',
        'password' => ''
    ]
];