<?php

$config = require __DIR__ . '/config.php';

function db()
{
    static $db;
    global $config;

    if ($db) {
        return $db;
    }

    $dsn = "{$config['db']['driver']}:host={$config['db']['host']};dbname={$config['db']['database']}";

    try {
        $db = new PDO($dsn, $config['db']['user'], $config['db']['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        throw new \Exception('数据库连接失败: ' . $e->getMessage());
    }

    return $db;
}

function find_shorturl($code)
{
    global $config;

    if ($config['case_sensitive']) {
        $sql = 'SELECT code, url  FROM `short_urls` WHERE binary code= :code';
    } else {
        $sql = 'SELECT code, url  FROM `short_urls` WHERE code= :code';
    }
    

    $stmt = db()->prepare($sql);
    $stmt->execute([":code" => $code]);

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

    $result = $stmt->execute();

    return [
        'url' => $url,
        'code' => $code,
    ];
}

function log_visit($short_url, $request)
{
    save_visit($short_url, $request);
    
    increase_visits($short_url['code']);
}

function increase_visits($code)
{
    $stmt = db()->prepare('update short_urls set visits = visits + 1 where code = :code');

    $stmt->execute(['code' => $code]);
}

function save_visit($shortUrl, $request)
{
    $headers = $request->getHeaders();
    $userAgent = isset($headers['User-Agent']) ? $headers['User-Agent'][0] : '';
    $referer = isset($headers['Referer']) ? $headers['Referer'] : '';
    $referer = is_array($referer) ? json_encode($referer) : $referer;

    $clientIP = $request->getAttribute('clientIP');

    $device = '';
    $os = '';
    $browser = '';

    $sql = "INSERT INTO `visits`(`shorturl_id`, `ip_address`, `device`, `user_agent`, `referer`) values
                                (:shorturl_id,:ip_address,:device,:browser,:os,:user_agent,:referer);";
    $stmt = db()->prepare($sql);

    $stmt->bindParam(':short_url', $shortUrl['code']);
    $stmt->bindParam(':ip_address', $clientIP);
    $stmt->bindParam(':device', $device);
    $stmt->bindParam(':browser', $browser);
    $stmt->bindParam(':os', $os);
    $stmt->bindParam(':user_agent', $userAgent);
    $stmt->bindParam(':referer', $referer);

    $result = $stmt->execute();
}
