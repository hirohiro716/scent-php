<?php
namespace hirohiro716\Scent;

use Exception;
use hirohiro716\Scent\Validate\ValueValidator;

/**
 * 文字列のクラス.
 *
 * @author hiro
 */
class StringObject extends AbstractObject
{

    private $value;

    /**
     * コンストラクタ.
     *
     * @param mixed $value
     */
    public function __construct($value = "")
    {
        parent::__construct();
        if ($value === null) {
            $value = "";
        }
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

    /**
     * stringを取得する.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * stringを取得する.
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
     * 改めて文字列をセットする.
     *
     * @param string $value
     */
    public function set(string $value): void
    {
        $this->value = $value;
    }

    /**
     * 末尾に文字列を追加する.
     *
     * @param string $value
     */
    public function append(string $value): void
    {
        $this->value .= $value;
    }

    /**
     * 文字列の一部を抽出した結果を取得する.
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
     * 文字列の一部を置き換えた結果を取得する.
     *
     * @param string $search
     * @param string $replacement
     * @return StringObject
     */
    public function replace(string $search, string $replacement): StringObject
    {
        return new StringObject(str_replace($search, $replacement, $this->value));
    }

    /**
     * 文字列の左側を指定した文字列で埋めた結果を取得する.
     *
     * @param int $length
     * @param string $paddingString
     * @return StringObject
     */
    public function paddingLeft(int $length, string $paddingString): StringObject
    {
        $padding = str_repeat($paddingString, $length);
        $stringObject = new StringObject($padding . $this->value);
        return $stringObject->subString($length * - 1);
    }

    /**
     * 文字列の右側を指定した文字列で埋めた結果を取得する.
     *
     * @param int $length
     * @param string $paddingString
     * @return StringObject
     */
    public function paddingRight(int $length, string $paddingString): StringObject
    {
        $padding = str_repeat($paddingString, $length);
        $stringObject = new StringObject($this->value . $padding);
        return $stringObject->subString(0, $length);
    }

    /**
     * 文字列の最初および最後から空白文字を取り除いた結果を取得する.
     *
     * @return StringObject
     */
    public function trim(): StringObject
    {
        return new StringObject(trim($this->value, " 　\t\n\r\0\x0B"));
    }

    /**
     * 文字列の最初から空白文字を取り除いた結果を取得する.
     *
     * @return StringObject
     */
    public function trimLeft(): StringObject
    {
        return new StringObject(ltrim($this->value, " 　\t\n\r\0\x0B"));
    }

    /**
     * 文字列の最後から空白文字を取り除いた結果を取得する.
     *
     * @return StringObject
     */
    public function trimRight(): StringObject
    {
        return new StringObject(rtrim($this->value, " 　\t\n\r\0\x0B"));
    }

    /**
     * アルファベットを小文字に変換した結果を取得する.
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
     * アルファベットを大文字に変換した結果を取得する.
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
     * 文字列を半角に変換した結果を取得する.
     *
     * @param string $encoding
     * @return StringObject
     */
    public function toHalf(string $encoding = null): StringObject
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }
        return new StringObject(mb_convert_kana($this->value, "ask", $encoding));
    }

    /**
     * 文字列を全角に変換した結果を取得する.
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
     * 文字列内のカタカナをひらがなに変換した結果を取得する.
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
     * 文字列内のひらがなをカタカナに変換した結果を取得する.
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
     * 文字列をサニタイジングした結果を取得する.
     * 
     * @return StringObject
     */
    public function sanitize(): StringObject
    {
        return new StringObject(htmlspecialchars($this->value));
    }
    
    /**
     * 文字列をURLエンコードした結果を取得する.
     * 
     * @return StringObject
     */
    public function urlencode(): StringObject
    {
        return new StringObject(urlencode($this->value));
    }
    
    /**
     * 内部の値を整数に変換する. 変換できなかった場合はnullを返す.
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
     * 内部の値を整数または少数に変換する. 変換できなかった場合はnullを返す.
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
     * 内部の値をUNIXタイムスタンプに変換する. 変換できなかった場合はnullを返す.
     * 
     * @return int|null
     */
    public function toTimestamp()
    {
        return Datetime::stringToTimestamp($this->value);
    }
    
    /**
     * 文字列を区切り文字で分割して配列を取得する.
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
     * 文字数を取得する.
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
     * 引数の文字列が最初に見つかった位置を取得する.
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
     * 引数の文字列が最後に見つかった位置を取得する.
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
     * 文字列が等しいか判定する.
     *
     * @param string $compare
     *            比較対象
     * @return bool
     */
    public function equals(string $compare): bool
    {
        return strcmp($this->value, $compare) == 0;
    }
    
    /**
     * 正規表現に一致するか判定する.
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
     * ランダムな英数字で構成された文字列を作成する.
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
            $value->append($base->subString(mt_rand(0, $baseLength), 1));
        }
        return $value;
    }
    
}
