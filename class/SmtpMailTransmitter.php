<?php
namespace hirohiro716\Scent;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * SMTPでメールを送信するクラス.
 *
 * @author hiro
 */
class SmtpMailTransmitter
{

    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        // 接続情報
        $this->server = new StringObject();
        $this->user = new StringObject();
        $this->password = new StringObject();
        $this->secureMethod = new StringObject("tls"); // setメソッドのdocに記載あり
        $this->port = 587; // setメソッドのdocに記載あり
        $this->charSet = new StringObject("UTF-8"); // setメソッドのdocに記載あり
        $this->encoding = new StringObject("base64"); // setメソッドのdocに記載あり
                                                      // メール情報
        $this->arrayTO = new Hash();
        $this->arrayCC = new Hash();
        $this->arrayBCC = new Hash();
        $this->addressFROM = new StringObject();
        $this->title = new StringObject();
        $this->body = new StringObject();
    }

    private $server;

    /**
     * 送信サーバーをセットする.
     *
     * @param string $server
     */
    public function setServer(string $server): void
    {
        $this->server->set($server);
    }

    private $user;

    /**
     * 送信サーバーにログインするユーザー名をセットする.
     *
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user->set($user);
    }

    private $password;

    /**
     * 送信サーバーにログインするパスワードをセットする.
     *
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password->set($password);
    }

    private $secureMethod;

    /**
     * SMTPコネクションで使用する暗号化をセットする.
     * "tls"がデフォルト.
     *
     * @param string $secureMethod
     */
    public function setSecureMethod(string $secureMethod): void
    {
        $this->secureMethod->set($secureMethod);
    }

    private $port;

    /**
     * 使用するポート番号をセットする.
     * 587がデフォルト.
     *
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    private $charSet;

    /**
     * 使用する文字コードをセットする.
     * "UTF-8"がデフォルト.
     *
     * @param string $charSet
     */
    public function setCharSet(string $charSet): void
    {
        $this->charSet->set($charSet);
    }

    private $encoding;

    /**
     * 使用するエンコーディングをセットする.
     * "base64"がデフォルト.
     *
     * @param string $encoding
     */
    public function setEncoding(string $encoding): void
    {
        $this->encoding->set($encoding);
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
     */
    public function send(): void
    {
        if ($this->arrayTO->size() == 0 || $this->server->length() == 0 || $this->user->length() == 0 || $this->password->length() == 0) {
            throw new Exception("It was not setting a mail address or server or login informations.");
        }
        $mailer = new PHPMailer();
        // サーバー認証
        $mailer->isSMTP();
        $mailer->Host = $this->server->get();
        $mailer->SMTPAuth = true;
        $mailer->Username = $this->user->get();
        $mailer->Password = $this->password->get();
        $mailer->SMTPSecure = $this->secureMethod->get();
        $mailer->Port = $this->port;
        $mailer->CharSet = $this->charSet->get();
        $mailer->Encoding = $this->encoding->get();
        $mailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        // 送信内容
        $mailer->setFrom($this->addressFROM);
        foreach ($this->arrayTO as $to) {
            $mailer->addAddress($to);
        }
        foreach ($this->arrayCC as $cc) {
            $mailer->addCC($cc);
        }
        foreach ($this->arrayBCC as $bcc) {
            $mailer->addBCC($bcc);
        }
        $mailer->Subject = $this->title->get();
        $mailer->Body = $this->body->get();
        if ($mailer->send() == false) {
            throw new Exception($mailer->ErrorInfo);
        }
    }
}