<?php
namespace hirohiro716\Scent;

/**
 * 文字列のクラス.
 *
 * @author hiro
 */
class StringObject
{

    private $value;

    /**
     * コンストラクタ.
     *
     * @param mixed $value
     */
    public function __construct($value = "")
    {
        if ($value === null) {
            $value = "";
        }
        $this->value = $value;
    }

    /**
     * stringを取得する.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * stringを取得する.
     *
     * @return string
     */
    public function get(): string
    {
        return $this->value;
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
        $timestamp = strtotime($this->value);
        if ($timestamp == -1) {
            return null;
        }
        return $timestamp;
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
     * @return int
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
     * @return int
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
        return strcmp($this->value, $compare);
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
    
    private static $randomBase = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJLKMNOPQRSTUVWXYZ0123456789";
    
    /**
     * ランダムな英数字で構成された文字列を作成する.
     * 
     * @param int $length
     * @return StringObject
     */
    public static function createRandomString(int $length): StringObject
    {
        $base = new StringObject(self::$randomBase);
        $value = new StringObject();
        for ($i = 0; $i < $length; $i++) {
            $value->append($base->subString(mt_rand(0, 61), 1));
        }
        return $value;
    }
    
}
