<?php
namespace hirohiro716\Scent\Image;

use hirohiro716\Scent\Filesystem\File;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use hirohiro716\Scent\FloatObject;

/**
 * QRコードを作成するクラス.
 *
 * @author hiro
 */
class QRCodeCreator
{

    private $scale = 1;

    /**
     * QRコードの大きさのスケールを指定する.
     *
     * @param float $scale
     *            デフォルトは1
     */
    public function setScale(float $scale)
    {
        $this->scale = $scale;
    }

    /**
     * QRコードの画像を作成する.
     *
     * @param string $data
     *            内容
     * @param File $file
     *            保存場所
     */
    public function create(string $data, File $file)
    {
        $options = new QROptions();
        $floatObject = new FloatObject($this->scale * 5);
        $options->scale = $floatObject->round()->toInteger();
        $instance = new QRCode($options);
        $instance->render($data, $file->getLocation());
    }
}