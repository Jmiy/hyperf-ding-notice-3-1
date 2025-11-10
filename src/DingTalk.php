<?php

namespace DingNotice;

use DingNotice\Contracts\DingTalkInterface;
use DingNotice\Contracts\HttpClientInterface;
use Hyperf\Context\Context;
use function Hyperf\Support\call;
use function Hyperf\Support\make;

class DingTalk implements DingTalkInterface
{

    /**
     * @var
     */
    protected array $config;
    /**
     * @var string
     */
    protected string $robot = 'default';
    /**
     * @var DingTalkService
     */
    protected $dingTalkService;

    protected $client;

    /**
     * DingTalk constructor.
     * @param $config
     * @param HttpClientInterface $client
     */
    public function __construct($config, $client = null)
    {
        $this->config = $config;
        $this->client = $client;
        $this->with();
    }

    /**
     * @param string|null $robot
     * @return DingTalkService
     */
    public function getDingTalkService(?string $robot = null): DingTalkService
    {
        $_robot = $robot === null ? $this->robot : $robot;

        $key = DingTalkService::class . ':' . $_robot;
        $dingTalkService = Context::get($key);
        if (empty($dingTalkService)) {
            $dingTalkService = make(
                DingTalkService::class,
                [
                    $this->config[$_robot] ?? [],
                    $this->client
                ]
            );
            Context::set($key, $dingTalkService);
        }

        return $dingTalkService;
    }


    /**
     * @param string $robot
     * @return $this
     */
    public function with($robot = 'default')
    {
        $this->robot = $robot;
//        $this->dingTalkService = new DingTalkService($this->config[$robot] ?? [], $this->client);
//        $this->dingTalkService = $this->getDingTalkService($this->robot);
        return $this;
    }


    /**
     * @param string $content
     * @param string|null $robot
     * @return mixed
     */
    public function text(string $content = '', ?string $robot = null): mixed
    {
        return $this->getDingTalkService($robot)
            ->setTextMessage($content)
            ->send();
    }

    /**
     * @param $title
     * @param $text
     * @param string|null $robot
     * @return mixed
     */
    public function action($title, $text, ?string $robot = null): mixed
    {
        return $this->getDingTalkService($robot)
            ->setActionCardMessage($title, $text);
    }

    /**
     * @param array $mobiles
     * @param bool $atAll
     * @param string|null $robot
     * @return $this|DingTalk
     */
    public function at(array $mobiles = [], bool $atAll = false, ?string $robot = null)
    {
        $this->getDingTalkService($robot)
            ->setAt($mobiles, $atAll);
        return $this;
    }

    /**
     * @param string $title
     * @param string $text
     * @param string $url
     * @param string $picUrl
     * @param string|null $robot
     * @return mixed
     */
    public function link(string $title = '', string $text = '', string $url = '', string $picUrl = '', ?string $robot = null): mixed
    {
        return $this->getDingTalkService($robot)
            ->setLinkMessage($title, $text, $url, $picUrl)
            ->send();
    }

    /**
     * @param string $title
     * @param string $markdown
     * @param string|null $robot
     * @return mixed
     */
    public function markdown(string $title = '', string $markdown = '', ?string $robot = null): mixed
    {
        return $this->getDingTalkService($robot)
            ->setMarkdownMessage($title, $markdown)
            ->send();
    }

    /**
     * @param string $title
     * @param string $markdown
     * @param int $hideAvatar
     * @param int $btnOrientation
     * @param string|null $robot
     * @return mixed
     */
    public function actionCard(string $title = '', string $markdown = '', int $hideAvatar = 0, int $btnOrientation = 0, ?string $robot = null): mixed
    {
        return $this->getDingTalkService($robot)
            ->setActionCardMessage($title, $markdown, $hideAvatar, $btnOrientation);
    }

    /**
     * @param string|null $robot
     * @return mixed
     */
    public function feed(?string $robot = null): mixed
    {
        return $this->getDingTalkService($robot)
            ->setFeedCardMessage();
    }

}
