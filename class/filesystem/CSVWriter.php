<?php
namespace hirohiro716\Scent\Filesystem;

use php_user_filter;
use hirohiro716\Scent\Hash;

/**
 * CSVをファイルに書き込むクラス。
 *
 * @author hiro
 */
class CSVWriter
{
    
    /**
     * コンストラクタ。
     *
     * @param string $fileLocation
     */
    public function __construct(File $file, string $delimiter, string $lineSeparator)
    {
        $this->delimiter = $delimiter;
        self::$lineSeparator = $lineSeparator;
        stream_filter_register("LineFeedReplacer", LineFeedReplacer::class);
        $this->handle = fopen($file->getLocation(), "w");
        stream_filter_append($this->handle, "LineFeedReplacer");
    }
    
    private $delimiter;
    
    public static $lineSeparator;
    
    private $handle;
    
    /**
     * ファイルに行を追記する。
     * 
     * @param Hash $hash
     */
    public function write(Hash $hash)
    {
        fputcsv($this->handle, $hash->getValues(), $this->delimiter, "\"", "\\");
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

class LineFeedReplacer extends php_user_filter {
    
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = preg_replace("/(?<!\r)\n/", CSVWriter::$lineSeparator, $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }
}
