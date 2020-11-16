<?php

function db()
{
    static $db;

    if ($db) {
        return $db;
    }

    $driver = config('db.driver');
    $host = config('db.host');
    $dbname = config('db.database');

    $dsn = "{$driver}:host={$host};dbname={$dbname}";

    try {
        $db = new PDO($dsn, config('db.user'), config('db.password'));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        // throw new \Exception('数据库连接失败: ' . $e->getMessage());
        throw $e;
    }

    return $db;
}

function find_shorturl($code)
{
    if (config('case_sensitive')) {
        $sql = 'SELECT code, url  FROM `short_urls` WHERE binary code = :code';
    } else {
        $sql = 'SELECT code, url  FROM `short_urls` WHERE code = :code';
    }

    $stmt = db()->prepare($sql);
    $stmt->execute([':code' => $code]);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $row = $stmt->fetch();

    return $row;
}

function save_shorturl($url, $code)
{
    $sql = "INSERT INTO `short_urls` (url, code) VALUES (:url, :code)";

    $stmt = db()->prepare($sql);

    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':code', $code);

    try {
        $result = $stmt->execute();
    } catch (PDOException $e) {
        if (strstr($e->getMessage(), 'Duplicate')) {
            throw new DuplicateCodeException();
        }
    }

    if (!$result) {
        return false;
    }

    return [
        'url' => $url,
        'code' => $code,
    ];
}

function increase_visits($code)
{
    $stmt = db()->prepare('update short_urls set visits = visits + 1 where code = :code');

    $stmt->execute([':code' => $code]);
}

function save_visit($shortUrl, $data)
{
    $sql = "INSERT INTO `visits`(`short_url`, `ip_address`, `device`, `browser`, `os`, `user_agent`, `referer`) values
                                (:short_url, :ip_address, :device, :browser, :os, :user_agent, :referer);";
    $stmt = db()->prepare($sql);

    $stmt->execute([
        ':short_url' => $shortUrl['code'],
        ':ip_address' => $data['ip_address'] ?? '',
        ':device' => $data['device'] ?? '',
        ':browser' => $data['browser'] ?? '',
        ':os' => $data['os'] ?? '',
        ':user_agent' => $data['user_agent'] ?? '',
        ':referer' => $data['referer'] ?? '',
    ]);
}
