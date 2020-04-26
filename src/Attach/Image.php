<?php
/**
 * Created by PhpStorm.
 * User: liuquan
 * Date: 2018/5/15
 * Time: 10:24
 */

namespace Mark\Attach;


class Image
{
    private $_path; // 图片路径
    private $_width; // 图片宽度
    private $_height; // 图片高度
    private $_mime; // mime type
    private $_im; // 图片句柄

    /**
     * 构造函数
     * @param $path string the image's path
     */
    public function __construct($path) {
        if (file_exists($path)) {
            $this->_path = $path;
            $size = getimagesize($path);
            $this->_width = $size[0];
            $this->_height = $size[1];
            $this->_mime = $size["mime"];
            $this->createImage($this->_mime);
        } else {
            die("the ${path} not found.");
        }
    }

    private function createImage($mimeType) {
        switch ($mimeType) {
            case 'image/gif':
                $res = imagecreatefromgif($this->_path);
                break;
            case 'image/jpeg':
                $res = imagecreatefromjpeg($this->_path);
                break;
            case 'image/png':
                $res = imagecreatefrompng($this->_path);
                break;
            default:
                die("the $mimeType not supported");
        }
        if (!$res) {
            die("cannot create the image.");
        }
        $this->_im = $res;
    }

    /**
     * 缩放图片 php >=5.5.0
     * @param $width int 缩放后宽度
     * @param $height int 缩放后高度
     * @return
     */
    public function resize($width = 200, $height = 200) {
        $tmp = imagescale($this->_im, $width, $height);
        if (!$tmp) {
            die("resize image failed");
        } else {
            imagedestroy($this->_im);
            $this->_im = $tmp;
            $this->_width = $width;
            $this->_height = $height;
        }
        return $this;
    }

    /**
     * @param $cover Image
     */
    public function attachCoverImg($cover, $alpha = 60) {
        if ($cover->_mime== "image/png") {
            //imagepalettetotruecolor($cover->_im);
            $this->imageCoverCopy($this->_im, $cover->_im, $this->_width, $this->_height, $cover->_width, $cover->_height);
        } else {
            $this->imageCoverCopy($this->_im, $cover->_im, $this->_width, $this->_height, $cover->_width, $cover->_height, $alpha);
        }

        return $this;
    }

    /**
     * @param resource $dst_im <p>
     * Destination image link resource.
     * </p>
     * @param resource $src_im <p>
     * Source image link resource.
     * </p>
     * @param int $dst_w <p>
     * x-coordinate of destination point.
     * </p>
     * @param int $dst_h <p>
     * y-coordinate of destination point.
     * </p>
     * @param int $src_w <p>
     * Source width.
     * </p>
     * @param int $src_h <p>
     * Source height.
     * </p>
     */
    private function imageCoverCopy($dst_im, $src_im, $dst_w, $dst_h, $src_w, $src_h, $alpha = -1) {
        $start = array(0, 0);
        $end = array(0, 0);

        do {
            $end[0] = $start[0] + $src_w;
            $end[1] = $start[1] + $src_h;
            // 横向扩展
            if ($end[0] <= $dst_w && $end[1] <= $dst_h) {
                if ($alpha < 0) {
                    imagecopy($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w, $src_h);
                } else {
                    imagecopymerge($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w, $src_h, $alpha);
                }

                $start[0] += $src_w;
            } else if ($end[0] > $dst_w && $end[1] <= $dst_h) {
                if ($alpha < 0) {
                    imagecopy($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w - ($end[0] - $dst_w), $src_h);
                } else {
                    imagecopymerge($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w - ($end[0] - $dst_w), $src_h, $alpha);
                }
                // 向下扩展
                $start[0] = 0;
                $start[1] += $src_h;
            } else if ($end[0] <= $dst_w && $end[1] > $dst_h) {
                if ($alpha < 0) {
                    imagecopy($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w, $src_h - ($end[1] - $dst_h));
                } else {
                    imagecopymerge($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w , $src_h - ($end[1] - $dst_h), $alpha);
                }
                //
                $start[0] += $src_w;
            } else {
                // 长宽都不够时
                if ($alpha < 0) {
                    imagecopy($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w - ($end[0] - $dst_w), $src_h - ($end[1] - $dst_h));
                } else {
                    imagecopymerge($dst_im, $src_im, $start[0], $start[1], 0, 0, $src_w - ($end[0] - $dst_w) , $src_h - ($end[1] - $dst_h), $alpha);
                }
                //
                $start[0] = $dst_w;
                $start[1] = $dst_h;
            }
        } while($end[0] <= $dst_w || $end[1] <= $dst_h);
    }

    /**
     * @param $text Text
     */
    public function attachText($text) {
        $color = imagecolorallocatealpha($this->_im, $text->fontColor[0], $text->fontColor[1], $text->fontColor[2], $text->fontColor[3]);
        imagefttext($this->_im, $text->fontSize, $text->angle, 0, ($this->_height - $text->fontSize - 20), $color, $text->fontPath, $text->content);
        return $this;
    }

    /**
     * 保存新图片到源图片所在目录
     */
    public function save() {
        $tmpFileName = $this->renameFile($this->_path);

        imagealphablending($this->_im,false);
        imagesavealpha($this->_im, true);
        switch ($this->_mime) {
            case 'image/gif':
                $res = imagegif($this->_im, $tmpFileName);
                break;
            case 'image/jpeg':
                $res = imagejpeg($this->_im, $tmpFileName);
                break;
            case 'image/png':
                $res = imagepng($this->_im, $tmpFileName);
                break;
            default:
                $res = false;
                die("$this->_mime are not supported");
        }
        if (!$res) {
            die("can not save the file.");
        }

        return $tmpFileName;
    }

    /**
     * rename file
     * @param $filename string
     * @return string new name
     */
    private function renameFile($filename) {
        $timer = (string)time();

        if (preg_match('/\.\w+$/', $filename)) {
            return preg_replace("/(.*)(\.\w+)$/", "$1-${timer}$2", $filename);
        }

        return $filename . $timer;
    }

    public function __destruct() {
        if (is_resource($this->_im)) {
            imagedestroy($this->_im);
        }
    }
}