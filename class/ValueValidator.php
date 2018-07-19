<?php
namespace hirohiro716\Scent;

use Exception;

/**
 * 値の検証を行うクラス.
 *
 * @author hiro
 */
class ValueValidator
{

    /*
     * 検証する方法の定数
     */
    private const BLANK = "は必須です。";

    private const INTEGER = "に数字以外の文字列が含まれています。";

    private const DECIMAL = "は整数または少数である必要があります。";

    private const LENGTH = "は" . self::ERROR_MESSAGE_ARGUMENT . "桁である必要があります。";

    private const MAX_LENGTH = "の文字数がオーバーしています。" . self::ERROR_MESSAGE_ARGUMENT . "文字まで入力できます。";

    private const MIN_LENGTH = "の文字数が足りません。" . self::ERROR_MESSAGE_ARGUMENT . "文字必要です。";

    private const ZERO = "にゼロは入力できません。";

    private const MAX_VALUE = "は最大で「" . self::ERROR_MESSAGE_ARGUMENT . "」まで入力できます。";

    private const MIN_VALUE = "は「" . self::ERROR_MESSAGE_ARGUMENT . "」以上である必要があります。";

    private const MAIL_CHARS = "にメールアドレスで使用できない文字が含まれています。";

    private const DATETIME = "は日付または時刻として有効ではありません。";

    private const TELEPHONE = "は電話番号（ハイフン有り）として正しくありません。";

    private const REGEX = "が正しくありません。";

    private const REGEX_REVERSE = "に許可されていない文字が含まれています。";

    /*
     * 予約済みの検証方法配列
     */
    private $parameters;

    /**
     * コンストラクタ.
     *
     * @param string $targetName
     */
    public function __construct(string $targetName = "")
    {
        $this->parameters = new Hash();
        $this->targetName = $targetName;
    }

    /*
     * 検証する値の名前
     */
    private $targetName;

    /**
     * 検証対象の名前をセットする.
     *
     * @param string $name
     */
    public function setTargetName(string $name): void
    {
        $this->targetName = name;
    }

    /**
     * 空白チェックを予約する.
     */
    public function addBlankCheck(): void
    {
        $this->parameters->put(self::BLANK, null);
    }

    /**
     * 数値有効性チェックを予約する.
     */
    public function addIntegerCheck(): void
    {
        $this->parameters->put(self::INTEGER, null);
    }

    /**
     * 少数値有効性チェックを予約する.
     */
    public function addDecimalCheck(): void
    {
        $this->parameters->put(self::DECIMAL, null);
    }

    /**
     * 文字数チェックを予約する.
     *
     * @param int $length
     */
    public function addLengthCheck(int $length): void
    {
        $this->parameters->put(self::LENGTH, $length);
    }

    /**
     * 最大文字数チェックを予約する.
     *
     * @param int $maxLength
     */
    public function addMaxLengthCheck(int $maxLength): void
    {
        $this->parameters->put(self::MAX_LENGTH, $maxLength);
    }

    /**
     * 最小文字数チェックを予約する.
     *
     * @param int $minLength
     */
    public function addMinLengthCheck(int $minLength): void
    {
        $this->parameters->put(self::MIN_LENGTH, $minLength);
    }

    /**
     * ゼロチェックを予約する.
     */
    public function addZeroCheck(): void
    {
        $this->parameters->put(self::ZERO, null);
    }

    /**
     * 最大値チェックを予約する.
     *
     * @param int $maxValue
     */
    public function addMaxValueCheck(int $maxValue): void
    {
        $this->parameters->put(self::MAX_VALUE, $maxValue);
    }

    /**
     * 最小値チェックを予約する.
     *
     * @param int $minValue
     */
    public function addMinValueCheck(int $minValue): void
    {
        $this->parameters->put(self::MIN_VALUE, $minValue);
    }

    /**
     * メールアドレスに使用できる文字だけで構成されているかのチェックを予約する.
     */
    public function addMailCharsCheck(): void
    {
        $this->parameters->put(self::MAIL_CHARS, null);
    }

    /**
     * 日付有効性チェックを予約する.
     */
    public function addDatetimeCheck(): void
    {
        $this->parameters->put(self::DATETIME, null);
    }

    /**
     * 数字とハイフン以外の文字が含まれていないかのチェックを予約する.
     */
    public function addTelephoneNumberCheck(): void
    {
        $this->parameters->put(self::TELEPHONE, null);
    }

    /**
     * 正規表現の条件を満たしているかのチェックを予約する.
     *
     * @param string $regexPattern
     */
    public function addRegexCheck(string $regexPattern): void
    {
        $this->parameters->put(self::REGEX, $regexPattern);
    }

    /**
     * 正規表現の条件を満たしている場合にValidationExceptionをスローするチェックを予約する.
     *
     * @param string $regexPattern
     */
    public function addRegexReverseCheck(string $regexPattern): void
    {
        $this->parameters->put(self::REGEX_REVERSE, $regexPattern);
    }

    /**
     * 予約済みのチェックをクリアする.
     */
    public function clear(): void
    {
        $this->parameters->clear();
    }

    /*
     * エラーメッセージの変動する箇所用置き換え文字列
     */
    private const ERROR_MESSAGE_ARGUMENT = "xrChpnw9u4";

    /**
     * パラメーターを利用したエラーメッセージを取得する.
     *
     * @param string $const
     *            チェック方法
     * @return string
     */
    private function buildErrorMessage(string $const): string
    {
        if ($this->parameters->isExistKey($const)) {
            $constObject = new StringObject($const);
            $parameter = new StringObject($this->parameters->get($const));
            return $this->targetName . $constObject->replace(self::ERROR_MESSAGE_ARGUMENT, $parameter);
        }
        return "";
    }

    public function execute($value): void
    {
        $val = new StringObject($value);
        foreach ($this->parameters as $const => $parameter) {
            switch ($const) {
                case self::BLANK:
                    if ($val->length() == 0) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::INTEGER:
                    if ($val->length() > 0 && $val->isRegexMatch("^-{0,1}[0-9]{1,}$") == false) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::DECIMAL:
                    if ($val->length() > 0 && $val->isRegexMatch("^-{0,1}[0-9]{1,}(\.{0,1}[0-9]{1,}|[0-9]{0,})$") == false) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::LENGTH:
                    if ($val->length() != $parameter) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::MAX_LENGTH:
                    if ($val->length() > $parameter) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::MIN_LENGTH:
                    if ($val->length() < $parameter) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::ZERO:
                    if ($val->toFloat() == 0) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::MAX_VALUE:
                    if ($val->toFloat() > $parameter) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::MIN_VALUE:
                    if ($val->toFloat() < $parameter) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::MAIL_CHARS:
                    if ($val->isRegexMatch("^[a-zA-Z0-9\.@!#\$%&'\*\+=\?\^_`\{\|\}~-]{0,}$") == false || $val->length() > 0 && $val->lastIndexOf("@") == - 1) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::DATETIME:
                    if ($val->toTimestamp() === null && $val->toInteger() === null || $val->toInteger() < 0) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::TELEPHONE:
                    if ($val->isRegexMatch("^[0-9]{1,5}-[0-9]{1,5}-[0-9]{1,5}$") == false) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::REGEX:
                    if ($val->isRegexMatch($parameter) == false) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
                case self::REGEX_REVERSE:
                    if ($val->isRegexMatch($parameter)) {
                        throw new ValidationException($this->buildErrorMessage($const));
                    }
                    break;
            }
        }
    }

    /**
     * nullまたは空文字かどうかをチェックする.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isBlank($value): bool
    {
        try {
            $validator = new self();
            $validator->addBlankCheck();
            $validator->execute($value);
            return true;
        } catch (ValidationException $exception) {
            return false;
        }
    }

    /**
     * 数値かどうかをチェックする.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isInteger($value): bool
    {
        try {
            $validator = new self();
            $validator->addBlankCheck();
            $validator->addIntegerCheck();
            $validator->execute($value);
            return true;
        } catch (ValidationException $exception) {
            return false;
        }
    }

    /**
     * 数値または少数かどうかをチェックする.
     *
     * @param mixed $value
     * @return bool
     */
    public static function isDecimal($value): bool
    {
        try {
            $validator = new self();
            $validator->addBlankCheck();
            $validator->addDecimalCheck();
            $validator->execute($value);
            return true;
        } catch (ValidationException $exception) {
            return false;
        }
    }

    /**
     * メールアドレスに使用できる文字だけで構成されているかをチェックする.
     *
     * @param string $mailAddress
     * @return bool
     */
    public static function isMailChars(string $mailAddress): bool
    {
        try {
            $validator = new self();
            $validator->addMailCharsCheck();
            $validator->execute($mailAddress);
            return true;
        } catch (ValidationException $exception) {
            return false;
        }
    }

    /**
     * 日付として有効かどうかチェックする.
     *
     * @param mixed $value
     *            日時文字列またはUNIXタイムスタンプ
     * @return bool
     */
    public static function isDatetime($value): bool
    {
        try {
            $validator = new self();
            $validator->addDatetimeCheck();
            $validator->execute($value);
            return true;
        } catch (ValidationException $exception) {
            return false;
        }
    }

    /**
     * ハイフン付き電話番号として有効かどうかチェックする.
     *
     * @param string $value
     * @return bool
     */
    public static function isTelephoneNumber(string $value): bool
    {
        try {
            $validator = new self();
            $validator->addTelephoneNumberCheck();
            $validator->execute($value);
            return true;
        } catch (ValidationException $exception) {
            return false;
        }
    }

    /**
     * 配列として有効かどうかをチェックする.
     *
     * @param mixed $array
     * @return bool
     */
    public static function isArray($array): bool
    {
        return is_array($array);
    }
}

/**
 * 値の検証に失敗した場合の例外クラス.
 *
 * @author hiro
 */
class ValidationException extends Exception
{
}
