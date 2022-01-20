<?php
namespace hirohiro716\Scent;

/**
 * 正規表現に関する関数。
 *
 * @author hiro
 */
class RegexHelper
{
    
    /**
     * 半角整数のみ。
     */
    public const INTEGER_NARROW_ONLY = "^[0-9]{0,}$";
    /**
     * 全角整数のみ。
     */
    public const INTEGER_WIDE_ONLY = "^[０-９]{0,}$";
    /**
     * 小数のみ。
     */
    public const DECIMAL = "^[0-9\.]{0,}$";
    /**
     * 正負小数のみ。
     */
    public const DECIMAL_NEGATIVE = "^[0-9\.\-]{0,}$";
    /**
     * 電話番号(半角数字及びハイフン)のみ。
     */
    public const TEL_NUMBER_ONLY = "^[0-9\-]{0,}$";
    /**
     * 日時(半角数字、ハイフン及びスラッシュ及びコロン)のみ。
     */
    public const DATETIME_ONLY = "^[0-9\-\/: ]{0,}$";
    /**
     * 日付(半角数字、ハイフン及びスラッシュ)のみ。
     */
    public const DATE_ONLY = "^[0-9\-\/]{0,}$";
    /**
     * 時刻(半角数字及びコロン)のみ。
     */
    public const TIME_ONLY = "^[0-9:]{0,}$";
    /**
     * アルファベットのみ。
     */
    public const ALPHABET_ONLY = "^[a-zA-Zａ-ｚＡ-Ｚ]{0,}$";
    /**
     * アルファベット半角のみ。
     */
    public const ALPHABET_NARROW_ONLY = "^[a-zA-Z]{0,}$";
    /**
     * アルファベット全角のみ。
     */
    public const ALPHABET_WIDE_ONLY = "^[ａ-ｚＡ-Ｚ]{0,}$";
    /**
     * アルファベット半角小文字のみ。
     */
    public const ALPHABET_NARROW_LOWER_ONLY = "^[a-z]{0,}$";
    /**
     * アルファベット半角大文字のみ。
     */
    public const ALPHABET_NARROW_UPPER_ONLY = "^[A-Z]{0,}$";
    /**
     * アルファベット全角小文字のみ。
     */
    public const ALPHABET_WIDE_LOWER_ONLY = "^[ａ-ｚ]{0,}$";
    /**
     * アルファベット全角大文字のみ。
     */
    public const ALPHABET_WIDE_UPPER_ONLY = "^[Ａ-Ｚ]{0,}$";
    /**
     * 半角カタカナのみ。
     */
    public const KATAKANA_NARROW_ONLY = "^[ｦ-ﾟ]{0,}$";
    /**
     * 全角カタカナのみ。
     */
    public const KATAKANA_WIDE_ONLY = "^[ァ-ヴー]{0,}$";
    /**
     * ひらがなのみ。
     */
    public const HIRAGANA_ONLY = "^[ぁ-んー]{0,}$";
    /**
     * 改行のみ。
     */
    public const LINE_SEPARATOR = "^(\r\n|\r|\n){0,}$";
    /**
     * タブ文字のみ。
     */
    public const TAB = "^\t{0,}$";
    /**
     * スペース文字のみ。
     */
    public const SPACE = "^(　| ){0,}$";
    /**
     * 半角文字のみ。
     */
    public const HALF = "^[\x01-\x7E]{0,}$";
    /**
     * 全角文字のみ。
     */
    public const WIDE = "^[^\x01-\x7E]{0,}$";
    
    /**
     * 正規表現によって全角半角大小英数/ひらがな/全角カタカナを区別しない比較用文字列に変換する。
     * 
     * @param string $value 変換対象
     * @return string 変換後の値
     */
    public static function makeBroadCompareValue(string $value): string
    {
        $original = new StringObject($value);
        if ($original->length() == 0) {
            return $value;
        }
        $result = new StringObject();
        for ($index = 0; $index < $original->length(); $index++) {
            $one = $original->subString($index, 1);
            switch (true) {
                case $one->isRegexMatch(self::HIRAGANA_ONLY):
                    $result->append("(");
                    $result->append($one);
                    $result->append("|");
                    $result->append($one->toKatakana());
                    $result->append(")");
                    break;
                case $one->isRegexMatch(self::KATAKANA_WIDE_ONLY):
                    $result->append("(");
                    $result->append($one);
                    $result->append("|");
                    $result->append($one->toHiragana());
                    $result->append(")");
                    break;
                case $one->isRegexMatch(self::INTEGER_NARROW_ONLY):
                    $result->append("(");
                    $result->append($one);
                    $result->append("|");
                    $result->append($one->toWide());
                    $result->append(")");
                    break;
                case $one->isRegexMatch(self::INTEGER_WIDE_ONLY):
                    $result->append("(");
                    $result->append($one);
                    $result->append("|");
                    $result->append($one->toNarrow());
                    $result->append(")");
                    break;
                case $one->isRegexMatch(self::ALPHABET_ONLY):
                    // 半角小文字に変換
                    $alphabet = $one->toHalf()->toLower();
                    // 4パターン追加
                    $result->append("(");
                    $result->append($alphabet);
                    $result->append("|");
                    $result->append($alphabet->toUpper());
                    $result->append("|");
                    $result->append($alphabet->toWide());
                    $result->append("|");
                    $result->append($alphabet->toUpper()->toWide());
                    $result->append(")");
                    break;
                default:
                    $result->append($one);
                    break;
            }
        }
        return $result;        
    }
}