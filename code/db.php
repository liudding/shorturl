<?php

function db()
{
    static $db;

    if ($db) {
        return $db;
    }

    if (!empty(config('db.dsn'))) {
        $dsn = config('db.dsn');
    } else {
        $driver = config('db.driver');
        $host = config('db.host');
        $dbname = config('db.database');
        $dsn = "{$driver}:host={$host};dbname={$dbname}";
    }

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
    $table = config('tables.shorturl_urls');

    if (config('case_sensitive')) {
        $sql = "SELECT code, url  FROM `$table` WHERE binary code = :code";
    } else {
        $sql = "SELECT code, url  FROM `$table` WHERE code = :code";
    }

    $stmt = db()->prepare($sql);
    $stmt->execute([':code' => $code]);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $row = $stmt->fetch();

    return $row;
}

function save_shorturl($url, $code)
{
    $table = config('tables.shorturl_urls');
    $sql = "INSERT INTO `$table` (url, code) VALUES (:url, :code)";

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
    $table = config('tables.shorturl_urls');
    $stmt = db()->prepare("update $table set visits = visits + 1 where code = :code");

    $stmt->execute([':code' => $code]);
}

function save_visit($shortUrl, $data)
{
    $table = config('tables.shorturl_visits');
    $sql = "INSERT INTO `$table`(`short_url`, `ip_address`, `device`, `browser`, `os`, `user_agent`, `referer`) values
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


function visits_traffic_last_days($short_urls, $days=30)
{
    if (is_array($short_urls)) {
        $where = 'IN (:short_urls)';
        $binds = [
            ':short_urls' => $short_urls
        ];
    } else {
        $where = ' = :short_url';
        $binds = [
            ':short_url' => $short_urls
        ];
    }

    $table = config('tables.shorturl_visits');
    $sql = "SELECT 
        DATE_FORMAT(`visited_at`, '%Y') as `year`, 
        DATE_FORMAT(`visited_at`, '%m') as `month`,
        DATE_FORMAT(`visited_at`, '%d') as `day`,
        count(*) as `count`
        FROM `$table`
        WHERE `short_url` $where
        GROUP BY `year`, `month`, `day`;";

    $stmt = db()->prepare($sql);
    $stmt->execute($binds);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetch();
}

function visits_traffic_last_hours($short_urls, $hours=24)
{
    if (is_array($short_urls)) {
        $where = 'IN (:short_urls)';
        $binds = [
            ':short_urls' => $short_urls
        ];
    } else {
        $where = ' = :short_url';
        $binds = [
            ':short_url' => $short_urls
        ];
    }

    $table = config('tables.shorturl_visits');
    $sql = "SELECT
        DATE_FORMAT(`visited_at`, '%H') as `hour`,
        count(*) as `count`
        FROM `$table`
        WHERE `short_url` $where
        AND `visited_at` >= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL - $hours HOUR)
        GROUP BY `hour`;";


    $stmt = db()->prepare($sql);
    $stmt->execute($binds);

    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    return $stmt->fetch();
}

