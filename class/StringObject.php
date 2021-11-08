<?php
namespace hirohiro716\Scent;

use Exception;
use hirohiro716\Scent\Validate\ValueValidator;

/**
 * 文字列のクラス。
 *
 * @author hiro
 */
class StringObject extends AbstractObject
{

    private $value;

    /**
     * コンストラクタ。
     *
     * @param mixed $value
     */
    public function __construct($value = "")
    {
        parent::__construct();
        if ($value === null) {
            $this->value = "";
        } else {
            if ($value === true) {
                $this->value = "true";
            } else if ($value === false) {
                $this->value = "false";
            } else {
                try {
                    $this->value = (string) $value;
                } catch (Exception $exception) {
                    $this->value = Helper::getInstanceId($value);
                }
            }
        }
    }

    /**
     * stringを取得する。
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * stringを取得する。
     *
     * @return string
     */
    public function get(string $fromEncoding = null, string $toEncoding = null): string
    {
        if ($toEncoding === null) {
            $toEncoding = mb_internal_encoding();
        }
        if ($fromEncoding !== null) {
            return mb_convert_encoding($this->value, $toEncoding, $fromEncoding);
        }
        return mb_convert_encoding($this->value, $toEncoding);
    }

    /**
     * 改めて文字列をセットする。
     *
     * @param string $value
     */
    public function set(string $value): void
    {
        $this->value = $value;
    }

    /**
     * 末尾に文字列を追加する。
     *
     * @param string $value
     * @return StringObject このインスタンス
     */
    public function append(string $value): StringObject
    {
        $this->value .= $value;
        return $this;
    }

    /**
     * 文字列の一部を置き換える。
     *
     * @param string $search
     * @param string $replacement
     * @return StringObject このインスタンス
     */
    public function replace(string $search, string $replacement): StringObject
    {
        $this->value = str_replace($search, $replacement, $this->value);
        return $this;
    }

    /**
     * 文字列の左側を指定した文字列で埋める。
     *
     * @param int $length
     * @param string $paddingString
     * @return StringObject このインスタンス
     */
    public function paddingLeft(int $length, string $paddingString): StringObject
    {
        $padding = str_repeat($paddingString, $length);
        $this->value = mb_substr($padding . $this->value, $length * -1);
        return $this;
    }

    /**
     * 文字列の右側を指定した文字列で埋める。
     *
     * @param int $length
     * @param string $paddingString
     * @return StringObject このインスタンス
     */
    public function paddingRight(int $length, string $paddingString): StringObject
    {
        $padding = str_repeat($paddingString, $length);
        $this->value = mb_substr($padding . $this->value, 0, $length);
        return $this;
    }
    
    /**
     * 文字列の最初および最後から空白文字を取り除く。
     *
     * @return StringObject このインスタンス
     */
    public function trim(): StringObject
    {
        return $this->trimLeft()->trimRight();
    }
    
    /**
     * 文字列の最初から空白文字を取り除く。
     *
     * @return StringObject このインスタンス
     */
    public function trimLeft(): StringObject
    {
        $this->value = preg_replace("@^[ 　\t\r\n]{1,}@u", "", $this->value);
        return $this;
    }
    
    /**
     * 文字列の最後から空白文字を取り除く。
     *
     * @return StringObject このインスタンス
     */
    public function trimRight(): StringObject
    {
        $this->value = preg_replace("@[ 　\t\r\n]{1,}$@u", "", $this->value);
        return $this;
    }
    
    /**
     * 文字列をサニタイジングする。
     *
     * @return StringObject このインスタンス
     */
    public function sanitize(): StringObject
    {
        $this->value = htmlspecialchars($this->value);
        return $this;
    }
    
    /**
     * 文字列をURLエンコードする。
     *
     * @return StringObject このインスタンス
     */
    public function urlencode(): StringObject
    {
        $this->value = urlencode($this->value);
        return $this;
    }
    
    /**
     * 文字列の一部を抽出した結果の新しいインスタンスを取得する。
     *
     * @param int $start
     * @param int $length
     * @param string $encoding
     * @return StringObject
     */
    public function subString(int $start, int $length = null, string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_substr($this->value, $start, $length, $encoding));
    }
    
    /**
     * アルファベットを小文字に変換した結果の新しいインスタンスを取得する。
     *
     * @param string $encoding
     * @return StringObject
     */
    public function toLower(string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_strtolower($this->value, $encoding));
    }

    /**
     * アルファベットを大文字に変換した結果の新しいインスタンスを取得する。
     *
     * @param string $encoding
     * @return StringObject
     */
    public function toUpper(string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_strtoupper($this->value, $encoding));
    }

    /**
     * 文字列を半角に変換した結果の新しいインスタンスを取得する。
     *
     * @param string $encoding
     * @return StringObject
     */
    public function toHalf(string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_convert_kana($this->value, "as", $encoding));
    }

    /**
     * 文字列を全角に変換した結果の新しいインスタンスを取得する。
     *
     * @param string $encoding
     * @return StringObject
     */
    public function toWide(string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_convert_kana($this->value, "ASK", $encoding));
    }
    
    /**
     * 文字列内のカタカナをひらがなに変換した結果の新しいインスタンスを取得する。
     *
     * @param string $encoding
     * @return StringObject
     */
    public function toHiragana(string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_convert_kana($this->value, "Hc", $encoding));
    }
    
    /**
     * 文字列内のひらがなをカタカナに変換した結果の新しいインスタンスを取得する。
     *
     * @param string $encoding
     * @return StringObject
     */
    public function toKatakana(string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_convert_kana($this->value, "KC", $encoding));
    }
    
    /**
     * 内部の値を整数に変換する。変換できなかった場合はnullを返す。
     * 
     * @return int|null
     */
    public function toInteger()
    {
        if (ValueValidator::isInteger($this->value)) {
            return (int) $this->value;
        }
        return null;
    }
    
    /**
     * 内部の値を整数または少数に変換する。変換できなかった場合はnullを返す。
     *
     * @return float|null
     */
    public function toFloat()
    {
        if (ValueValidator::isDecimal($this->value)) {
            return (float) $this->value;
        }
        return null;
    }
    
    /**
     * 内部の値をUNIXタイムスタンプに変換する。変換できなかった場合はnullを返す。
     * 
     * @return int|null
     */
    public function toTimestamp()
    {
        return Datetime::stringToTimestamp($this->value);
    }
    
    /**
     * 文字列を区切り文字で分割して配列を取得する。
     *
     * @param string $delimiter
     * @param int $limit
     * @return array
     */
    public function split(string $delimiter, int $limit = PHP_INT_MAX): array
    {
        return explode($delimiter, $this->value, $limit);
    }

    /**
     * 文字数を取得する。
     *
     * @param string $encoding
     * @return int
     */
    public function length(string $encoding = null): int
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return mb_strlen($this->value, $encoding);
    }

    /**
     * 引数の文字列が最初に見つかった位置を取得する。
     *
     * @param string $needle
     * @param string $encoding
     * @return int 見つからなかった場合は-1
     */
    public function indexOf(string $search, string $encoding = null): int
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        $index = mb_strpos($this->value, $search, null, $encoding);
        if ($index === false) {
            return -1;
        }
        return $index;
    }

    /**
     * 引数の文字列が最後に見つかった位置を取得する。
     *
     * @param string $needle
     * @param string $encoding
     * @return int 見つからなかった場合は-1
     */
    public function lastIndexOf(string $search, string $encoding = null): int
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        $index = mb_strrpos($this->value, $search, null, $encoding);
        if ($index === false) {
            return -1;
        }
        return $index;
    }
    
    /**
     * 文字列が等しい場合はtrueを返す。
     *
     * @param string $compare 比較対象
     * @return bool
     */
    public function equals(string $compare): bool
    {
        return strcmp($this->value, $compare) == 0;
    }
    
    /**
     * 正規表現に一致する場合はtrueを返す。
     * 
     * @param string $regexPattern 正規表現のパターン
     * @return bool
     */
    public function isRegexMatch(string $regexPattern): bool
    {
        return mb_ereg_match($regexPattern, $this->value);
    }
    
    public const RANDOM_STRING_BASE = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789";
    
    /**
     * ランダムな英数字で構成された文字列を作成する。
     * 
     * @param int $length 文字数
     * @param string $baseString 使用する文字列
     * @return StringObject
     */
    public static function createRandomString(int $length, string $baseString = self::RANDOM_STRING_BASE): StringObject
    {
        $base = new StringObject($baseString);
        $baseLength = $base->length();
        $value = new StringObject();
        for ($i = 0; $i < $length; $i++) {
            $value->append($base->subString(random_int(0, $baseLength - 1), 1));
        }
        return $value;
    }
    
}
