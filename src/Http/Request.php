<?php
/**
 * [Request.php name]
 * @author wong <[842687571@qq.com]>
 * Date: 02/12/17
 * Time: 下午6:25
 * @return    [type]    PhpStorm  frame
 */

namespace Core\Http;

use Core\Http\Message\ServerRequest;
use Core\Http\Message\Stream;
use Core\Http\Message\UploadFile;
use Core\Http\Message\Uri;
use Core\Utility\Validate\Validate;
use Core\Http\Session\Request as SessionRequest;

class Request extends ServerRequest
{
    private static $instance;
    private $swooleHttpRequest = null;
    private $session;

    static function getInstance(\swooleHttpRequest $request = null)
    {
        if ($request !== null) {
            self::$instance = new Request($request);
        }
        return self::$instance;
    }

    function __construct(\swooleHttpRequest $request)
    {
        $this->swooleHttpRequest = $request;
        $this->initHeaders();
        $protocol = str_replace('HTTP/', '', $this->swooleHttpRequest->server['server_protocol']);
        $body = new Stream($this->swooleHttpRequest->rawContent());
        $uri = $this->initUri();
        $files = $this->initFiles();
        $method = $this->swooleHttpRequest->server['request_method'];
        parent::__construct($method, $uri, null, $body, $protocol, $this->swooleHttpRequest->server);
        $this->withCookieParams($this->initCookie())->withQueryParams($this->initGet())->withParsedBody($this->initPost())->withUploadedFiles($files);
    }


    function getRequestParam($keyOrKeys = null, $default = null)
    {
        if ($keyOrKeys !== null) {
            if (is_string($keyOrKeys)) {
                $ret = $this->getParsedBody($keyOrKeys);
                if ($ret === null) {
                    $ret = $this->getQueryParam($keyOrKeys);
                    if ($ret === null) {
                        if ($default !== null) {
                            $ret = $default;
                        }
                    }
                }
                return $ret;
            } else if (is_array($keyOrKeys)) {
                if (!is_array($default)) {
                    $default = array();
                }
                $data = $this->getRequestParam();
                $keysNull = array_fill_keys(array_values($keyOrKeys), null);
                if ($keysNull === null) {
                    $keysNull = [];
                }
                $all = array_merge($keysNull, $default, $data);
                $all = array_intersect_key($all, $keysNull);
                return $all;
            } else {
                return null;
            }
        } else {
            return array_merge($this->getParsedBody(), $this->getQueryParams());
        }
    }

    /**
     *[initHeaders void]
     * @author  Wongzx <[842687571@qq.com]>
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    private function initHeaders()
    {
        $headers = $this->swooleHttpRequest->header;
        foreach ($headers as $header => $val) {
            $this->withAddedHeader($header, $val);
        }
    }

    /**
     *[initUri Uri]
     * @author  Wongzx <[842687571@qq.com]>
     * @copyright Copyright (c)
     * @return    [type]        [description]
     */
    private function initUri()
    {
        $uri = new Uri();
        $uri->withScheme("http");
        $uri->withPath($this->swooleHttpRequest->server['path_info']);
        $query = isset($this->swooleHttpRequest->server['query_string']) ? $this->swooleHttpRequest->server['query_string'] : '';
        $uri->withQuery($query);
        $host = $this->swooleHttpRequest->header['host'];
        $host = explode(":", $host);
        $uri->withHost($host[0]);
        $port = isset($host[1]) ? $host[1] : 80;
        $uri->withPort($port);
        return $uri;
    }

    private function initFiles()
    {
        if (isset($this->swooleHttpRequest->files)) {
            $normalized = [];
            foreach ($this->swooleHttpRequest->files as $key => $value) {
                $normalized[$key] = new UploadFile(
                    $value['tmp_name'],
                    (int)$value['size'],
                    (int)$value['error'],
                    $value['name'],
                    $value['type']
                );
            }
            return $normalized;
        } else {
            return [];
        }
    }

    private function initCookie()
    {
        return isset($this->swooleHttpRequest->cookie) ? $this->swooleHttpRequest->cookie : array();
    }

    private function initPost()
    {
        return isset($this->swooleHttpRequest->post) ? $this->swooleHttpRequest->post : array();
    }

    private function initGet()
    {
        return isset($this->swooleHttpRequest->get) ? $this->swooleHttpRequest->get : array();
    }
}