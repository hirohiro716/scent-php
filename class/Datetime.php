<?php
namespace hirohiro716\Scent;

/**
 * 日時クラス.
 *
 * @author hiro
 */
class Datetime
{

    private $year = 0;

    private $month = 0;

    private $day = 0;

    private $hour = 0;

    private $minute = 0;

    private $second = 0;

    private $pattern = 'Y-m-d H:i:s';

    /**
     * コンストラクタ.
     *
     * @param mixed $datetime
     */
    public function __construct($datetime = null)
    {
        switch (gettype($datetime)) {
            case 'string':
                $this->setDatetimeString($datetime);
                break;
            case 'integer':
                $this->setTimestamp($datetime);
                break;
            default:
                $this->setTimestamp(time());
                break;
        }
    }
    
    /**
     * toStringの実装.
     * 
     * @return string
     */
    public function __toString(): string
    {
        return $this->toDatetimeString();
    }

    /**
     * 年を変更する.
     *
     * @param int $year
     */
    public function modifyYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * 年を取得する.
     *
     * @return int
     */
    public function toYear(): int
    {
        $this->setTimestamp($this->getTimestamp());
        return $this->year;
    }

    /**
     * 月を変更する.
     *
     * @param int $month
     */
    public function modifyMonth(int $month): void
    {
        $this->month = $month;
    }

    /**
     * 月を取得する.
     *
     * @return int
     */
    public function toMonth(): int
    {
        $this->setTimestamp($this->getTimestamp());
        return $this->month;
    }

    /**
     * 日を変更する.
     *
     * @param int $day
     */
    public function modifyDay(int $day): void
    {
        $this->day = $day;
    }

    /**
     * 日を取得する.
     *
     * @return int
     */
    public function toDay(): int
    {
        $this->setTimestamp($this->getTimestamp());
        return $this->day;
    }

    /**
     * 年月日を変更する.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     */
    public function modifyDate(int $year, int $month, int $day): void
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
    }

    /**
     * 時を変更する.
     *
     * @param int $hour
     */
    public function modifyHour(int $hour): void
    {
        $this->hour = $hour;
    }

    /**
     * 時を取得する.
     *
     * @return int
     */
    public function toHour(): int
    {
        $this->setTimestamp($this->getTimestamp());
        return $this->hour;
    }

    /**
     * 分を変更する.
     *
     * @param int $minute
     */
    public function modifyMinute(int $minute): void
    {
        $this->minute = $minute;
    }

    /**
     * 分を取得する.
     *
     * @return int
     */
    public function toMinute(): int
    {
        $this->setTimestamp($this->getTimestamp());
        return $this->minute;
    }

    /**
     * 秒を変更する.
     *
     * @param int $second
     */
    public function modifySecond(int $second): void
    {
        $this->second = $second;
    }

    /**
     * 秒を取得する.
     *
     * @return int
     */
    public function toSecond(): int
    {
        $this->setTimestamp($this->getTimestamp());
        return $this->second;
    }

    /**
     * 時分秒を変更する.
     *
     * @param int $hour
     * @param int $minute
     * @param int $second
     */
    public function modifyTime(int $hour, int $minute, int $second): void
    {
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
    }

    /**
     * 何曜日かを取得する.
     *
     * @return int (0:日 1:月 2:火 3:水 4:木 5:金 6:土)
     */
    public function toWeek(): int
    {
        return date('w', $this->getTimestamp());
    }

    /**
     * 日時をセットする.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     */
    public function setDatetime(int $year, int $month, int $day, int $hour, int $minute, int $second): void
    {
        $this->year = $year;
        $this->month = $month;
        $this->day = $day;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
    }

    /**
     * 日付型式文字列をセットする.
     *
     * @param string $datetimeString
     */
    public function setDatetimeString(string $datetimeString): void
    {
        $datetime = strtotime($datetimeString);
        $this->year = intval(Date('Y', $datetime));
        $this->month = intval(Date('m', $datetime));
        $this->day = intval(Date('d', $datetime));
        $this->hour = intval(Date('H', $datetime));
        $this->minute = intval(Date('i', $datetime));
        $this->second = intval(Date('s', $datetime));
    }

    /**
     * タイムスタンプをセットする.
     *
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->year = intval(Date('Y', $timestamp));
        $this->month = intval(Date('m', $timestamp));
        $this->day = intval(Date('d', $timestamp));
        $this->hour = intval(Date('H', $timestamp));
        $this->minute = intval(Date('i', $timestamp));
        $this->second = intval(Date('s', $timestamp));
    }

    /**
     * 年を加算する.
     *
     * @param int $year
     */
    public function addYear(int $year): void
    {
        $this->year += $year;
    }

    /**
     * 月を加算する.
     *
     * @param int $month
     */
    public function addMonth(int $month): void
    {
        $this->month += $month;
    }

    /**
     * 日を加算する.
     *
     * @param int $day
     */
    public function addDay(int $day): void
    {
        $this->day += $day;
    }

    /**
     * 時を加算する.
     *
     * @param int $hour
     */
    public function addHour(int $hour): void
    {
        $this->hour += $hour;
    }

    /**
     * 分を加算する.
     *
     * @param int $minute
     */
    public function addMinute(int $minute): void
    {
        $this->minute += $minute;
    }

    /**
     * 秒を加算する.
     *
     * @param int $second
     */
    public function addSecond(int $second): void
    {
        $this->second += $second;
    }

    /**
     * 日付出力型式をセットする.
     *
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * タイムスタンプを取得する.
     *
     * @return int
     */
    public function toTimestamp(): int
    {
        return mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
    }

    /**
     * 日付文字列を取得する.
     *
     * @return string
     */
    public function toDatetimeString(): string
    {
        $datetime = mktime($this->hour, $this->minute, $this->second, $this->month, $this->day, $this->year);
        return date($this->pattern, $datetime);
    }

    /**
     * 指定した曜日で一番近い日のDatetimeオブジェクトを作成する.
     *
     * @param int $weekNo
     *            (0:日 1:月 2:火 3:水 4:木 5:金 6:土)
     * @return Datetime
     */
    public function createNearWeekDatetime(int $weekNo): Datetime
    {
        if ($this->getWeek() == $weekNo) {
            return clone $this;
        }
        $increment = 0;
        $incrementClone = clone $this;
        while ($incrementClone->getWeek() != $weekNo) {
            $incrementClone->addDays(1);
            $increment ++;
        }
        $decrement = 0;
        $decrementClone = clone $this;
        while ($decrementClone->getWeek() != $weekNo) {
            $decrementClone->addDays(- 1);
            $decrement ++;
        }
        if ($increment < $decrement) {
            return $incrementClone;
        } else {
            return $decrementClone;
        }
    }

    /**
     * 過去に遡って指定曜日のDatetimeのcloneを取得する.
     *
     * @param int $weekNo
     *            (0:日 1:月 2:火 3:水 4:木 5:金 6:土)
     * @return Datetime
     */
    public function createBeforeNearWeek(int $weekNo): Datetime
    {
        $clone = clone $this;
        while ($clone->getWeek() != $weekNo) {
            $clone->addDays(- 1);
        }
        return $clone;
    }

    /**
     * 未来に向かって指定曜日のDatetimeのcloneを取得する.
     *
     * @param integer $weekNo
     *            (0:日 1:月 2:火 3:水 4:木 5:金 6:土)
     * @return Datetime
     */
    public function createAfterNearWeek(int $weekNo): Datetime
    {
        $clone = clone $this;
        while ($clone->getWeek() != $weekNo) {
            $clone->addDays(1);
        }
        return $clone;
    }
    
    /**
     * 現時刻のタイムスタンプを取得する.
     * @return int
     */
    public static function now(): int
    {
        return time();
    }
    
    /**
     * 文字列をUNIXタイムスタンプに変換する. 変換できなかった場合はnullを返す.
     * 
     * @param string $datetimeString
     * @return int|null
     */
    public static function stringToTimestamp(string $datetimeString)
    {
        $timestamp = strtotime($datetimeString);
        if ($timestamp == -1) {
            return null;
        }
        return $timestamp;
    }
    
}
