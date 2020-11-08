<?php

$config = require_once './config.php';

function sanitize_url(string $url)
{
    $url = trim($url);

    // TODO: sanitize


    return $url;
}

function validate_url($url)
{
    $url = strtolower(trim($url));

    if (empty($url)) {
        return false;
    }

    // TODO: 处理中文转义

    return !!preg_match('/^(((ht|f)tps?):\/\/)?[\w-]+(\.[\w-]+)+([\w\-.,@?^=%&:/~+#]*[\w\-@?^=%&/~+#])?$/', $url);
}

function validate_code($code)
{
    global $config;

    for ($i = 0; $i < strlen($code); $i++) {
        if (strpos($config['charset'], $code[$i]) === false) {
            return false;
        }
    }

    return true;
}

function generate_code($url)
{

    global $config;

    $charset = $config['charset'];

    $hashInt = murmur_hash3($url);

    return int2string($hashInt, $charset);
}

/**
 * MurMur Hash
 * @param  string $key   Text to hash.
 * @param  number $seed  Positive integer only
 * @return number 32-bit positive integer hash
 */
function murmur_hash3(string $key, int $seed = 0): int
{
    $key = array_values(unpack('C*', $key));
    $klen = count($key);
    $h1 = $seed < 0 ? -$seed : $seed;
    $remainder = $i = 0;
    for ($bytes = $klen - ($remainder = $klen & 3); $i < $bytes;) {
        $k1 = $key[$i]
         | ($key[++$i] << 8)
         | ($key[++$i] << 16)
         | ($key[++$i] << 24);
        ++$i;
        $k1 = (((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16))) & 0xffffffff;
        $k1 = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
        $k1 = (((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16))) & 0xffffffff;
        $h1 ^= $k1;
        $h1 = $h1 << 13 | ($h1 >= 0 ? $h1 >> 19 : (($h1 & 0x7fffffff) >> 19) | 0x1000);
        $h1b = (((($h1 & 0xffff) * 5) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 5) & 0xffff) << 16))) & 0xffffffff;
        $h1 = ((($h1b & 0xffff) + 0x6b64) + ((((($h1b >= 0 ? $h1b >> 16 : (($h1b & 0x7fffffff) >> 16) | 0x8000)) + 0xe654) & 0xffff) << 16));
    }
    $k1 = 0;
    switch ($remainder) {
        case 3:$k1 ^= $key[$i + 2] << 16;
        case 2:$k1 ^= $key[$i + 1] << 8;
        case 1:$k1 ^= $key[$i];
            $k1 = ((($k1 & 0xffff) * 0xcc9e2d51) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0xcc9e2d51) & 0xffff) << 16)) & 0xffffffff;
            $k1 = $k1 << 15 | ($k1 >= 0 ? $k1 >> 17 : (($k1 & 0x7fffffff) >> 17) | 0x4000);
            $k1 = ((($k1 & 0xffff) * 0x1b873593) + ((((($k1 >= 0 ? $k1 >> 16 : (($k1 & 0x7fffffff) >> 16) | 0x8000)) * 0x1b873593) & 0xffff) << 16)) & 0xffffffff;
            $h1 ^= $k1;
    }
    $h1 ^= $klen;
    $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);
    $h1 = ((($h1 & 0xffff) * 0x85ebca6b) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
    $h1 ^= ($h1 >= 0 ? $h1 >> 13 : (($h1 & 0x7fffffff) >> 13) | 0x40000);
    $h1 = (((($h1 & 0xffff) * 0xc2b2ae35) + ((((($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000)) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
    $h1 ^= ($h1 >= 0 ? $h1 >> 16 : (($h1 & 0x7fffffff) >> 16) | 0x8000);
    return $h1;
}

function int2string($num, string $chars): string
{
    $str = '';
    $len = strlen($chars);
    while ($num >= $len) {
        $mod = bcmod($num, $len);
        $num = bcdiv($num, $len);
        $str = $chars[$mod] . $str;
    }
    $str = $chars[intval($num)] . $str;

    return $str;
}


function render_template($template, $data) 
{

    ob_start();

    include($template);

    $content = ob_get_contents();

    ob_end_clean();

    return $content;

    // $data = $data ?? [];

    // foreach ($data as $key => $value) {
    //     $pattern = "/\{\{\s*$key\s*\}\}/";

    //     $template = preg_replace($pattern, $value ?? '', $template);
    // }

    // return $template;
}


function detectDevice()
{
    
}


function detectOs()
{

}

function detectBrowser()
{
}