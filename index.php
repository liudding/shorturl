<?php

use RingCentral\Psr7\Response;

require_once './db.php';

function initializer($context)
{
    echo 'initializing' . PHP_EOL;
}

function handler($request, $context): Response
{
    $uri = $request->getAttribute('requestURI');
    $uri = trim($uri, '/');

    $method = $request->getMethod();

    // TODO: throttle

    if ($uri === 'admin') {
        // return
    }

    if ($uri === 'view/shortener') {
        // if ($method === 'GET') {
        //     if ()
        // }

    }

    if ($uri === 'api/make') {
        $data = $request->getQueryParams();

        $code = make($data['url'], $data['code'] ?? null);

        if (wants_json()) {
            return respond_json($code);
        } else {
            return respond_view($code);
        }
    }

    // 访问 short url
    return visit($uri, $request);
}

function visit($code, $request)
{
    if (empty($code)) {
        return respond_invalid_view();
    }

    $short = find_shorturl($code);

    if (!$short) {
        return respond_invalid_view();
    }

    log_visit($short, $request);

    return redirect($short['url']);
}

/**
 * 生成并存储 short url
 *
 * @param string $url 原始链接
 * @param string $code 自定义的 code
 * @return void
 */
function make($url, $code = null)
{
    $url = sanitize_url();

    if (!validate_url($url)) {
        return;
    }

    if (!empty($code = trim($code)) && !validate_code($code)) {
        return;
    }

    if (!empty($code)) { // 使用自定义的 code
        return $code;
    }

    global $config;

    $tried = 0;
    $result = null;

    $key = $url;

    while ($config['duplicate_tries'] > $tried) {
        $code = generate_code($key);

        try {
            $result = save_shorturl($url, $code);

            break;
        } catch (Exception $e) {
            $tried++;

            $key .= $config['duplicate_suffix'];
        }
    }

    return $result;
}

function shortener_view()
{
    return respond_view(render_template(view_shortener_page(), [
        'url' => '',
        'result' => '',
    ]));
}

function shortener_submit($request)
{
    $body = $request->getBody()->getContents();

    parse_str(trim($body), $data);

    if (!validate_url($data['url'])) {
        // todo: error message

        return respond_view(render_template(view_shortener_page(), [
            'result' => '请输入有效的链接',
            'url' => $data['url'],
            'errmsg' => '请输入有效的链接',
        ]));
    }

    $shortUrl = make($data['url']);

    return respond_view(render_template(view_shortener_page(), [
        'result' => $shortUrl['code'],
        'url' => $data['url'],
    ]));

}

function respond_json($data)
{
    return new Response(200, ['content-type' => 'application/json;charset=UTF-8'], json_encode($data));
}

function respond_view($html)
{
    return new Response(200, ['Content-Type' => 'text/html;charset=UTF-8'], $html);
}

function redirect($url)
{
    return new Response(302, ['location' => $url], '');
}

function wants_json($request)
{

}

function isValidUrl($str)
{
    return filter_var($str, FILTER_VALIDATE_URL);
}
