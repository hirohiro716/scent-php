<?php
namespace hirohiro716\Scent\Filesystem;

/**
 * テキストをファイルに書き込むクラス。
 *
 * @author hiro
 *
 */
class TextWriter
{
    
    /**
     * コンストラクタ。
     *
     * @param string $fileLocation
     */
    public function __construct(File $file)
    {
        $this->handle = fopen($file->getLocation(), "w");
    }
    
    private $handle;
    
    /**
     * ファイルに文字列を追記する。
     * 
     * @param string $text
     */
    public function write(string $text)
    {
        fwrite($this->handle, $text);
    }
    
    /**
     * ファイルを閉じてリソースを開放する。
     */
    public function close()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
}
