<?php

namespace DingNotice\Contracts;

interface DingTalkInterface
{

    /**
     * @param string $robot
     * @return $this
     */
    public function with($robot = 'default');

    /**
     * @param string $content
     * @param string|null $robot
     * @return mixed
     */
    public function text(string $content = '', ?string $robot = null): mixed;

    /**
     * @param $title
     * @param $text
     * @param string|null $robot
     * @return mixed
     */
    public function action($title, $text, ?string $robot = null): mixed;

    /**
     * @param array $mobiles
     * @param bool $atAll
     * @param string|null $robot
     * @return $this
     */
    public function at(array $mobiles = [], bool $atAll = false, ?string $robot = null);

    /**
     * @param string $title
     * @param string $text
     * @param string $url
     * @param string $picUrl
     * @param string|null $robot
     * @return mixed
     */
    public function link(string $title = '', string $text = '', string $url = '', string $picUrl = '', ?string $robot = null): mixed;

    /**
     * @param string $title
     * @param string $markdown
     * @param string|null $robot
     * @return mixed
     */
    public function markdown(string $title = '', string $markdown = '', ?string $robot = null): mixed;

    /**
     * @param string $title
     * @param string $markdown
     * @param int $hideAvatar
     * @param int $btnOrientation
     * @param string|null $robot
     * @return mixed
     */
    public function actionCard(string $title = '', string $markdown = '', int $hideAvatar = 0, int $btnOrientation = 0, ?string $robot = null): mixed;

    /**
     * @param string|null $robot
     * @return mixed
     */
    public function feed(?string $robot = null): mixed;
}
