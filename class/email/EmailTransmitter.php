<?php
namespace hirohiro716\Scent\Mail;

use hirohiro716\Scent\AbstractObject;
use hirohiro716\Scent\Hash;
use hirohiro716\Scent\StringObject;

/**
 * mb_send_mailを利用したメール送信クラス.
 *
 * @author hiro
 */
class EmailTransmitter extends AbstractObject
{

    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        parent::__construct();
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        $this->arrayTO = new Hash();
        $this->arrayCC = new Hash();
        $this->arrayBCC = new Hash();
        $this->addressFROM = new StringObject();
        $this->title = new StringObject();
        $this->body = new StringObject();
    }

    private $arrayTO;

    /**
     * TO（宛先）を追加する.
     *
     * @param string $addressTO
     */
    public function addTO(string $addressTO): void
    {
        $this->arrayTO->add($addressTO);
    }

    private $arrayCC;

    /**
     * CCを追加する.
     *
     * @param string $addressCC
     */
    public function addCC(string $addressCC): void
    {
        $this->arrayCC->add($addressCC);
    }

    private $arrayBCC;

    /**
     * BCCを追加する.
     *
     * @param string $addressBCC
     */
    public function addBCC(string $addressBCC): void
    {
        $this->arrayBCC->add($addressBCC);
    }

    private $addressFROM;

    /**
     * FROM（送信者）をセットする.
     *
     * @param string $addressFROM
     */
    public function setFROM(string $addressFROM): void
    {
        $this->addressFROM->set($addressFROM);
    }

    private $title;

    /**
     * タイトル（表題）をセットする.
     *
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title->set($title);
    }

    private $body;

    /**
     * 本文をセットする.
     *
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body->set($body);
    }

    /**
     * メールを送信する.
     *
     * @return bool
     */
    public function send(): bool
    {
        if ($this->arrayTO->size() == 0) {
            return false;
        }
        $to = $this->arrayTO->join(",");
        $from = "";
        if ($this->addressFROM->length() > 0) {
            $from = "FROM: " . $this->addressFROM . "\n";
        }
        $cc = "";
        if ($this->arrayCC->size() > 0) {
            $cc = "CC: " . $this->arrayCC->join(",") . "\n";
        }
        $bcc = "";
        if ($this->arrayBCC->size() > 0) {
            $bcc = "BCC: " . $this->arrayBCC->join(",") . "\n";
        }
        $header = null;
        $headerObject = new StringObject($from . $cc . $bcc);
        if ($headerObject->length() > 0) {
            $header = $headerObject->get();
        }
        return mb_send_mail($to, $this->title, $this->body, $header);
    }
}
