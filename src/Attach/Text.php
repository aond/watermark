<?php
/**
 * Created by PhpStorm.
 * User: liuquan
 * Date: 2018/5/15
 * Time: 10:28
 */

namespace Mark\Attach;


class Text
{
    public $fontPath;

    public $fontSize;

    public $fontColor;

    public $angle;

    public $content;

    public function __construct($content = '', $fontPath = '', $fontSize = 14, $fontColor = array(0, 0, 0, 100), $angle = 0) {
        $this->content = $content;
        $this->fontPath = $fontPath;
        $this->fontSize = $fontSize;
        $this->fontColor = $fontColor;
        $this->angle = $angle;
    }

    /**
     * @return string
     */
    public function getFontPath()
    {
        return $this->fontPath;
    }

    /**
     * @param string $fontPath
     */
    public function setFontPath($fontPath)
    {
        $this->fontPath = $fontPath;
    }

    /**
     * @return int
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @param int $fontSize
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
    }

    /**
     * @return array
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }

    /**
     * @param array $fontColor
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
    }

    /**
     * @return int
     */
    public function getAngle()
    {
        return $this->angle;
    }

    /**
     * @param int $angle
     */
    public function setAngle($angle)
    {
        $this->angle = $angle;
    }

    public function setContent($content) {
        $this->content = $content;
    }

    public function getContent() {
        return $this->content;
    }

    public function __destruct() {}
}