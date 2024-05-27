<?php

namespace App\Core;

use InvalidArgumentException;

class Request
{
    private array $query = [];

    private array $headers = [];

    private mixed $data;

    private string $uri = "";

    private string|bool|null $content_type;

    private mixed $method;

    public function __construct()
    {
        $this->data = $_REQUEST['POST'] ?? null;
        $this->uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->content_type = $this->setContentType();
        $this->query = $this->setQuery();
        $this->headers = getallheaders();
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function method()
    {
        return $this->method;
    }

    public function setContentType(): bool|string|null
    {
        $type = env('CONTENT_TYPE');
        if ($type) {
            return $type;
        }
        return env('HTTP_CONTENT_TYPE');
    }

    /**
     * @return array|string|null
     */
    public function setQuery(): array|string|null
    {
        return explode('/', trim($_SERVER['QUERY_STRING'], '/'));

    }

    /**
     * @param $name
     * @return array|string|null
     */
    public function query($name = null): array|string|null
    {
        if (is_null($name)) {
            return $this->query[$name] ?? null;
        } else {
            return $this->query;
        }
    }

    public function headers(): bool|array
    {
        return $this->headers;
    }

    public function header($header): bool|array
    {
        return $this->headers[$header];
    }

    public function data(): bool|array
    {
        return $this->data;
    }

    public function is_ajax(): bool
    {
        return $_SERVER['X-Requested-With'] === 'XMLHttpRequest';
    }


    /**
     * Check whether a Request is a certain type.
     *
     * Uses the built-in detection rules as well as additional rules
     * defined with CakeRequest::addDetector(). Any detector can be called
     * as `is($type)` or `is$Type()`.
     *
     * @param array|string $type The type of request you want to check. If an array
     *   this method will return true if the request matches any type.
     * @return bool Whether the request is the type you are checking.
     */
    public function is(array|string $type): bool
    {
        if (is_array($type)) {
            $result = array_map(array($this, 'is'), $type);
            return count(array_filter($result)) > 0;
        }
        $type = strtolower($type);
        if (!isset($this->_detectors[$type])) {
            return false;
        }
        $detect = $this->_detectors[$type];
        if (isset($detect['env']) && $this->_environmentDetector($detect)) {
            return true;
        }
        if (isset($detect['header']) && $this->_headerDetector($detect)) {
            return true;
        }
        if (isset($detect['accept']) && $this->_acceptHeaderDetector($detect)) {
            return true;
        }
        if (isset($detect['param']) && $this->_paramDetector($detect)) {
            return true;
        }
        if (isset($detect['callback']) && is_callable($detect['callback'])) {
            return call_user_func($detect['callback'], $this);
        }
        return false;
    }

    /**
     * Detects if a specific accept header is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether the request is the type you are checking.
     */
    protected function _acceptHeaderDetector(array $detect): bool
    {
        $acceptHeaders = explode(',', (string)env('HTTP_ACCEPT'));
        foreach ($detect['accept'] as $header) {
            if (in_array($header, $acceptHeaders)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Detects if a specific header is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether the request is the type you are checking.
     */
    protected function _headerDetector(array $detect): bool
    {
        foreach ($detect['header'] as $header => $value) {
            $header = env('HTTP_' . strtoupper($header));
            if (!is_null($header)) {
                if (!is_string($value) && !is_bool($value) && is_callable($value)) {
                    return call_user_func($value, $header);
                }
                return ($header === $value);
            }
        }
        return false;
    }

    /**
     * Detects if a specific request parameter is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether the request is the type you are checking.
     */
    protected function _paramDetector(array $detect): bool
    {
        $key = $detect['param'];
        if (isset($detect['value'])) {
            $value = $detect['value'];
            return isset($this->params[$key]) && $this->params[$key] == $value;
        }
        if (isset($detect['options'])) {
            return isset($this->params[$key]) && in_array($this->params[$key], $detect['options']);
        }
        return false;
    }

    /**
     * Detects if a specific environment variable is present.
     *
     * @param array $detect Detector options array.
     * @return bool Whether the request is the type you are checking.
     */
    protected function _environmentDetector(array $detect): bool
    {
        if (isset($detect['env'])) {
            if (isset($detect['value'])) {
                return env($detect['env']) == $detect['value'];
            }
            if (isset($detect['pattern'])) {
                return (bool)preg_match($detect['pattern'], env($detect['env']));
            }
            if (isset($detect['options'])) {
                $pattern = '/' . implode('|', $detect['options']) . '/i';
                return (bool)preg_match($pattern, env($detect['env']));
            }
        }
        return false;
    }
}
