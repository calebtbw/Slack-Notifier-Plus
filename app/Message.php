<?php

namespace SS\SlackNotifierPlus\App;

/**
 * Class Message
 * @package SS\SlackNotifierPlus\App;
 */
class Message
{
    /**
     * @var string $text
     */
    public $text = "";

    /**
     * @var bool $asUser
     */
    public $asUser = true;

    /**
     * @var string $username
     */
    public $username = "";

    /**
     * Message Text
     *
     * @param $text
     * @return $this
     */
    public function text($text)
    {
        $this->text = trim($text);
        return $this;
    }

    /**
     * Message username
     *
     * @param $username
     * @return $this
     */
    public function username($username)
    {
        $this->asUser = false;
        $this->username = trim($username);
        return $this;
    }

    /**
     * collect all data and send it as array
     *
     * @return array
     */
    public function toArray()
    {
        return array("text" => $this->text,  "username" => $this->username);

    }
}
