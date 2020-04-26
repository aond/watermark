<?php
/**
 * Created by PhpStorm.
 * User: liuquan
 * Date: 2018/5/15
 * Time: 10:13
 */

namespace Mark;

use Mark\Attach\Image;
use Mark\Attach\Text;

class WaterMark
{
    private static $_instance;
    /**
     * @var $_src Image
     */
    private $_src;

    private function __construct() {}

    // singleton
    public static function instance() {
        if (!self::$_instance) {
            self::$_instance = new WaterMark();
        }
        return self::$_instance;
    }

    /**
     * set the source image
     * @param $path string
     * @return WaterMark
     */
    public function setSrc($path = '') {
        if (file_exists($path)) {
            $this->_src = new Image($path);
        }
        return $this;
    }

    /**
     * attachCoverImg
     * @param $path string
     * @return WaterMark
     */
    public function attachCoverImg($path = '') {
        $cover = new Image($path);
        $this->_src->attachCoverImg($cover);
        return $this;
    }

    /**
     * TODO: set the text position.
     * set the  text
     * @param $text Text
     */
    public function attachText($text) {
        $this->_src->attachText($text);

        return $this;
    }

    /**
     * @return string the new file path.
     */
    public function save() {
        return $this->_src->save();
    }

    public function __destruct() {}
}