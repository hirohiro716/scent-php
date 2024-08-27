<?php
namespace hirohiro716\Scent\Filesystem;

/**
 * 文字列書き込みの処理インターフェース。
 *
 * @author hiro
 */
interface TextWritingProcess
{
    
    /**
     * ファイルに文字列を書き込む際に呼び出される。
     *
     * @param TextWriter $textWriter
     */
    public function call(TextWriter $textWriter);
}
