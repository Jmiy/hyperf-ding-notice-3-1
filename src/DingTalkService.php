<?php

namespace DingNotice;

use DingNotice\Messages\ActionCard;
use DingNotice\Messages\FeedCard;
use DingNotice\Messages\Link;
use DingNotice\Messages\Markdown;
use DingNotice\Messages\Message;
use DingNotice\Messages\Text;
use DingNotice\Contracts\HttpClientInterface;
use function Hyperf\Config\config;
use function Hyperf\Support\make;

class DingTalkService
{

    protected array $config;
    protected string $robot;

    /**
     * @var Message
     */
    protected $message;
    /**
     * @var array
     */
    protected $mobiles = [];
    /**
     * @var bool
     */
    protected $atAll = false;

    /**
     * @var HttpClientInterface
     */
    protected HttpClientInterface $client;

    /**
     * DingTalkService constructor.
     * @param $config
     * @param HttpClientInterface|null $client
     * @param string|null $robot
     */
    public function __construct($config, HttpClientInterface $client = null, ?string $robot = null)
    {
        $this->config = $config;
        $this->robot = $robot;
        $this->setTextMessage('null');

        if ($client != null) {
            $this->client = $client;
            return;
        }

        $this->client = $this->createClient($config, $robot);

    }

    /**
     * create a guzzle client
     * @param $config
     * @param string|null $robot
     * @return HttpClient
     * @author jmiy 2025-11-29 16:50
     */
    protected function createClient($config, ?string $robot = null): HttpClient
    {
//        return new HttpClient($config, $robot);

        return make(
            HttpClient::class,
            [
//                $config, $robot
                'config' => $config,
                'robot' => $robot
            ]
        );
    }

    /**
     * @param Message $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getMessage()
    {
        return $this->message->getMessage();
    }

    /**
     * @param array $mobiles
     * @param bool $atAll
     */
    public function setAt($mobiles = [], $atAll = false)
    {
        $this->mobiles = $mobiles;
        $this->atAll = $atAll;
        if ($this->message) {
            $this->message->sendAt($mobiles, $atAll);
        }
    }

    /**
     * @param $content
     * @return $this
     */
    public function setTextMessage($content)
    {
        $this->message = new Text($content);
        $this->message->sendAt($this->mobiles, $this->atAll);
        return $this;
    }

    /**
     * @param $title
     * @param $text
     * @param $messageUrl
     * @param string $picUrl
     * @return $this
     */
    public function setLinkMessage($title, $text, $messageUrl, string $picUrl = '')
    {
        $this->message = new Link($title, $text, $messageUrl, $picUrl);
        $this->message->sendAt($this->mobiles, $this->atAll);
        return $this;
    }

    /**
     * @param $title
     * @param $text
     * @return $this
     */
    public function setMarkdownMessage($title, $markdown)
    {
        $this->message = new Markdown($title, $markdown);
        $this->message->sendAt($this->mobiles, $this->atAll);
        return $this;
    }


    /**
     * @param $title
     * @param $text
     * @param int $hideAvatar
     * @param int $btnOrientation
     * @return ActionCard|Message
     */
    public function setActionCardMessage($title, $markdown, $hideAvatar = 0, $btnOrientation = 0)
    {
        $this->message = new ActionCard($this, $title, $markdown, $hideAvatar, $btnOrientation);
        $this->message->sendAt($this->mobiles, $this->atAll);
        return $this->message;
    }

    /**
     * @return FeedCard|Message
     */
    public function setFeedCardMessage()
    {
        $this->message = new FeedCard($this);
        $this->message->sendAt($this->mobiles, $this->atAll);
        return $this->message;
    }

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
     * @return bool|array
     */
    public function send(?string $robot = null)
    {
        $config = $this->getConfig($robot);

        if (!isset($config['enabled']) || !$config['enabled']) {
            return false;
        }

        return $this->client->send($this->message->getBody());
    }

}
