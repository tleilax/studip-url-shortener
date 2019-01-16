<?php
namespace URLShortener;

use Exception;

class YourlsAPI
{
    private static $actions = [
        'shorturl' => [
            'url'     => true,
            'keyword' => false,
            'title'   => false,
        ],
        'expand' => [
            'shorturl' => true,
        ],
        'url-stats' => [
            'shorturl' => true,
        ],
        'stats' => [
            'filter' => ['top', 'bottom', 'rand', 'last'],
            'limit'  => true,
        ],
        'db-stats' => [],
        'delete' => [
            'shorturl' => true,
        ],
    ];

    private static $formats = [
        'jsonp'  => false,
        'json'   => 'self::jsonHandler',
        'xml'    => false,
        'simple' => false,
    ];

    private static $instances = [];

    public static function configureInstance($name, $endpoint, array $credentials = null)
    {
        if (func_num_args() === 2 && is_array($endpoint)) {
            $credentials = $endpoint;
            $endpoint    = $name;
            $name        = 'default';
        }

        if (!filter_var($endpoint, FILTER_VALIDATE_URL)) {
            throw new Exception('Given endpoint is not a valid url');
        }

        if (!isset($credentials['username'], $credentials['password'])
            && !isset($credentials['signature']))
        {
            throw new Exception('No valid credentials found');
        }

        return self::$instances[$name] = new self($endpoint, $credentials);
    }

    public static function getInstance($name = 'default')
    {
        if (!isset(self::$instances[$name])) {
            throw new Exception("API instance {$name} has not been configured");
        }

        return self::$instances[$name];
    }

    private $endpoint;
    private $credentials;
    private $format = 'json';

    protected function __construct(string $endpoint, array $credentials)
    {
        $this->endpoint    = $endpoint;
        $this->credentials = $credentials;
    }

    public function setFormat(string $format)
    {
        if (!isset(self::$formats[$format])) {
            throw new Exception("Unknown format {$format}");
        }
        if (self::$formats[$format] === false) {
            throw new Exception("Unsupported format {$format}");
        }

        $this->format = $format;
    }

    public function shorten(string $url, string $keyword = null, string $title = null)
    {
        return $this->request('shorturl', compact('url', 'keyword', 'title'));
    }

    public function expand(string $shorturl)
    {
        return $this->request('expand', compact('shorturl'));
    }

    public function urlStats(string $shorturl)
    {
        return $this->request('url-stats', compact('shorturl'));
    }

    public function stats(string $filter, int $limit = 20)
    {
        return $this->request('stats', compact('filter', 'limit'));
    }

    public function dbStats()
    {
        return $this->request('db-stats');
    }

    public function delete(string $shorturl)
    {
        return $this->request('delete', compact('shorturl'));
    }

    private function request(string $action, array $data = [])
    {
        if (!isset(self::$actions[$action])) {
            throw new Exception("Unknown action {$action}");
        }

        // Remove null values form data array
        $data = array_filter($data, function ($value) {
            return isset($value);
        });

        // Check data array
        $errors = [];
        foreach ($actions[$action] as $var_name => $config) {
            if ($config !== false && !isset($data[$var_name])) {
                $errors[] = "Missing parameter {$var_name}";
            } elseif (is_array($config) && !in_array($data[$var_name], $config)) {
                $errors[] = "Invalid value {$data[$var_name]} for parameter {$var_name}";
            }
        }
        if (count($errors) > 0) {
            throw new Exception('Invalid data: ' . implode(', ', $errors));
        }

        // Actual request
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->endpoint,
            CURLOPT_HEADER         => 0,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => array_merge(
                compact('action'),
                $this->credentials,
                ['format' => $this->format],
                $data
            ),
        ]);
        $data = curl_exec($ch);
        curl_close($ch);

        return call_user_func(self::$formats[$this->format], $data);

    }

    protected static function jsonHandler(string $input)
    {
        return json_decode($input, true);
    }
}
