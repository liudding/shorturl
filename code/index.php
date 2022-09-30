<?php

require_once __DIR__ . "/vendor/autoload.php";

use RingCentral\Psr7\Response;
use DeviceDetector\DeviceDetector;

require_once __DIR__ . '/DuplicateCodeException.php';

$config = require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';


function initializer($context)
{
    echo 'initializing' . PHP_EOL;
}

function handler($request, $context): Response
{
    try {
        return handle($request, $context);
    } catch (PDOException $e) {
        return respond_json([
            'errmsg' => '服务器出错了' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        return respond_json($e);
    }
}

function handle($request, $context): Response
{
    $uri = $request->getAttribute('requestURI');
    $method = $request->getMethod();

    // FOR DEBUG
    $uri = str_replace('2016-08-15/proxy/shorturl/shorturl', '', $uri);

    // TODO: throttle

    $routes = get_routes();

    $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) use ($routes) {
        foreach ($routes as $route => $handler) {
            $parts = explode(' ', $route);
            $u = $parts[1] ?? $parts[0];

            if (count($parts) == 2) {
                $u = $parts[1];
                $m = $parts[0];
            } else {
                $u = $parts[0];
            }
            $u = trim($u);

            $r->addRoute($m ?? 'GET', $u, $handler);
        }
    });

    $routeInfo = $dispatcher->dispatch($method, $uri);
    switch ($routeInfo[0]) {
        case \FastRoute\Dispatcher::NOT_FOUND:
            throw new Exception('Hanlder not exist', 404);
            break;
        case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            throw new Exception('Method not allowed', 405);
            break;
        case \FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];

            if (!function_exists($handler)) {
                throw new Exception('Hanlder not exist', 500);
            }

            $resp = call_user_func($handler, $request, ...array_values($vars));

            if (is_array($resp)) {
                return respond_json($resp);
            }

            if ($resp instanceof Response) {
                return $resp;
            }

            return $resp;

            break;
    }


    throw new Exception('route not found', 404);

   

    // if ($uri === '/admin') {
    //     return handle_admin($request);
    // }

    // if ($uri === '/view/shortener') {
    //     if ($method === 'GET') {
    //         return respond_shortener_page();
    //     } else if ($method === 'POST') {
    // 		return shortener_submit($request);
    // 	}
    // }

    // if ($uri === '/api/make') {
    //     $data = $request->getQueryParams();

    //     $code = make($data['url'], $data['code'] ?? null);

    //     return respond_json($code);
    // }

    // $code = trim($uri, '/');

    // // 访问 short url
    // return visit_shorturl($code, $request);
}

function get_routes()
{
    return [
        'GET /view/shortener' => 'respond_shortener_page',
        'POST /view/shortener' => 'shortener_submit',
        'GET /admin' => 'handle_admin',
        // 'api/make' => '',

        'GET /{code:\w{4,6}}' => 'visit_shorturl'
    ];
}


function visit_shorturl($request, $code)
{
    if (empty($code)) {
        return respond_invalid_code($code);
    }

    $short = find_shorturl($code);

    if (!$short) {
        return respond_invalid_code($code);
    }

    log_visit($short, $request);

    return redirect($short['url']);
}

function log_visit($short_url, $request)
{
    $headers    = $request->getHeaders();

    $userAgent = isset($headers['User-Agent']) ? $headers['User-Agent'][0] : '';
    $userAgent = substr($userAgent, 0, 255);

    $dd = new DeviceDetector($userAgent);
    $dd->parse();

    if ($dd->isBot()) {
        // handle bots,spiders,crawlers,...
        $botInfo = $dd->getBot();

        return;
    }


    $referer = isset($headers['Referer']) ? $headers['Referer'] : '';
    $referer = is_array($referer) ? $referer[0] : $referer;

    $clientIP = $request->getAttribute('clientIP');

    save_visit($short_url, [
        'short_url' => $short_url['code'],
        'ip_address' => $clientIP,
        'device' => $dd->getDeviceName(),
        'browser' => $dd->getClient()['name'],
        'os' => $dd->getOs()['name'],
        'user_agent' => $userAgent,
        'referer' => $referer,
    ]);

    increase_visits($short_url['code']);
}


/**
 * 生成并存储 short url
 *
 * @param string $url 原始链接
 * @param string $duplicate 是否允许重复生成短链接
 * @param string $code 自定义的 code
 */
function make($url, $duplicate = false, $code = null)
{
    $url = sanitize_url($url);

    if (!validate_url($url)) {
        return false;
    }

    if (!empty($code = trim($code)) && !validate_code($code)) {
        return false;
    }

    if (!empty($code)) { // 使用自定义的 code

        return save_shorturl($url, $code);
    }

    $tried = 0;
    $result = null;

    $key = $url;

    while (config('duplicate_tries') > $tried) {
        $code = generate_code($key);

        try {
            $result = save_shorturl($url, $code);

            break;
        } catch (DuplicateCodeException $e) {
            if ($duplicate) {
                $tried++;

                $key .= config('duplicate_suffix');
            } else {
                $shortUrl = find_shorturl($code);
                if ($shortUrl['url'] !== $url) {
                    $tried++;
                    $key .= config('duplicate_suffix');
                } else {
                    $result = [
                        'code' => $code,
                        'url' => $url
                    ];
                    break;
                }
            }
        }
    }

    return $result;
}

function shortener_submit($request)
{
    $body = $request->getBody()->getContents();

    parse_str(trim($body), $data);

    if (!validate_url($data['url'] ?? null)) {

        return respond_json([
            'errmsg' => '无效的链接'
        ]);
    }


    $shortUrl = make($data['url']);

    if (!$shortUrl) {
        return respond_json([
            'errmsg' => '生成失败'
        ]);
    }

    return respond_json([
        'code' => $shortUrl['code'],
        'shorturl' => get_shorturl($shortUrl['code']),
        'url' => $data['url'],
    ]);
}

function handle_admin($request)
{
    return respond_view(render_template('/views/view_admin.php'));
}

function get_shorturl($code)
{
    return rtrim(config('base_url'), '/') . DIRECTORY_SEPARATOR . $code;
}


function respond_invalid_code($code)
{
    if (config('invalid_code_action') === 'redirect') {
        return redirect(config('redirect_url'));
    }

    if (config('invalid_code_action') === 'error_page' || config('invalid_code_action') === 'redirect_in_error_page') {
        return respond_view(render_template('/views/view_invalid_code.php'));
    }
}


function respond_shortener_page($data = [])
{
    return respond_view(render_template('/views/view_shortener.php', $data));
}
