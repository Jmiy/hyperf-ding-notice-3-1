<?php
/**
 * Created by PhpStorm.
 * User: Jmiy
 * Date: 2021-09-27
 * Time: 11:02 update
 */

namespace DingNotice;

use DingNotice\Contracts\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use function Hyperf\Config\config;
use function Hyperf\Support\make;

class HttpClient implements HttpClientInterface
{
    protected Client $client;
    protected array $config;
    protected string $robot;
    /**
     * @var string
     */
    protected string $hookUrl = "https://oapi.dingtalk.com/robot/send";

    /**
     * @var string
     */
//    protected string $accessToken = "";

    public function __construct($config, ?string $robot = null)
    {
//        $this->config = $config;
//        $this->setAccessToken();
        $this->robot = $robot;
        $this->client = $this->createClient();
    }

    /**
     *
     */
//    public function setAccessToken()
//    {
//        $this->accessToken = $this->config['token'] ?? '';
//    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig(?string $robot = null): array
    {
        $robot = $robot === null ? $this->robot : $robot;

        return config('ding.' . $robot, []);
    }


    /**
     * create a guzzle client
     * @return Client
     * @author jmiy 2025-11-29 16:19
     */
    protected function createClient(): Client
    {
        $config = $this->getConfig();
//        return new Client([
//            'timeout' => $config['timeout'] ?? 2.0,
//        ]);
        return make(
            Client::class,
            [
                'config' => [
                    'timeout' => $config['timeout'] ?? 2.0,
                ]
            ]
        );
    }

    /**
     * 获取机器人 hookUrl
     * @return string
     */
    public function getRobotUrl(): string
    {
        $config = $this->getConfig();

        $query = [
            'access_token' => $config['token'] ?? '',
        ];
        if (isset($config['secret']) && $secret = $config['secret']) {
            $timestamp = time() . sprintf('%03d', rand(1, 999));
            $sign = hash_hmac('sha256', $timestamp . "\n" . $secret, $secret, true);
            $query['timestamp'] = $timestamp;
            $query['sign'] = base64_encode($sign);
        }
        return $this->hookUrl . "?" . http_build_query($query);
    }

    /**
     * send message
     * @param $url
     * @param $params
     * @return array
     * @author Jmiy 2021-09-27 11:15 update
     */
    public function send($params): array
    {
        $config = $this->getConfig();
        $response = $this->client->post($this->getRobotUrl(), [
//            RequestOptions::BODY => json_encode($params),
//            RequestOptions::HEADERS => [
//                'Content-Type' => 'application/json',
//            ],
//            RequestOptions::VERIFY => $config['ssl_verify'] ?? true,
            RequestOptions::JSON => $params,
            RequestOptions::VERIFY => $config['ssl_verify'] ?? true,
        ]);

        $result = $response->getBody()->getContents();

        return json_decode($result, true) ?? [];
    }
}
