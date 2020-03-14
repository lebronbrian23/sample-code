<?php
/**
 * Created by PhpStorm.
 * User: Lebron Brian Cowen
 * Date: 4/28/2018
 * Time: 11:34 AM
 */

namespace SampleProject\Channel;


class AfricasTalkingMessage
{
    /** @var string */
    public $content;
    /**
     * @param string $content
     *
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }
    /**
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }
    /**
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;
        return $this;
    }
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}