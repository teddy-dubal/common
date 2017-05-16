<?php

namespace App\Common\Tools;

use Exception;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

class Mail {

    protected $_to;
    protected $_from;
    protected $_replyTo;
    protected $_subject;
    protected $_layout;
    protected $_content;
    protected $_config;
    protected $_mail;

    /**
     * Usage :
      $mail = new Mail();
      $mail->setTo(array('teddy.dubal@gmail.com'));
      $mail->setTemplate($template['_val']);
      $mail->setConfig(array());
      $mail->setData(array());
      $mail->send();
     */
    public function __construct() {
        $this->_mail = new Message();
        $this->_mail->setEncoding("UTF-8");
    }

    /**
     * Advenced function fot smtp configuration
     * @param type $data
     * @return self
     */
    public function setSmtpConfig($data = []) {
        $this->_config = $data;
        return $this;
    }

    /**
     *
     * @param type $st
     * @return self
     */
    public function setSmtpHost($st) {
        $this->_config['smtp']['host'] = $st;
        return $this;
    }

    /**
     *
     * @param type $st
     * @return self
     */
    public function setSubject($st) {
        $this->_subject = $st;
        return $this;
    }

    /**
     *
     * @param type $data
     */
    public function setAttachment($url) {
        $data            = file_get_contents($url);
        $at              = $this->_mail->createAttachment($data);
        $at->type        = 'application/pdf';
        $at->disposition = Mime::DISPOSITION_INLINE;
        $at->encoding    = Mime::ENCODING_BASE64;
        $at->filename    = date('YmdHis') . '.pdf';
        return $this;
    }

    /**
     * Allow to send mail , to sevreal receivers
     * @param array $to
     * $to = array(array('to' => test@test.fr,'toName'=> 'Test Name'),array('to'=>test2@test.com,'toName'=> 'Test Second Name'));
     * Or
     * $to = array('to' => 'unique@mail.com', 'toName' => 'Only Me');
     *
     */
    public function setTo($to = []) {
        $this->_to = $to;
        return $this;
    }

    /**
     * @param array $from
     * $from = array('from' => 'unique@mail.com', 'fromName' => 'Only Me');
     *
     */
    public function setFrom($from = []) {
        $this->_from = $from;
        return $this;
    }

    /**
     * @param array $rp
     * $rp = array('reply' => 'unique@mail.com', 'replyName' => 'Only Me');
     *
     */
    public function setReplyTo($rp = []) {
        $this->_replyTo = $rp;
        return $this;
    }

    /**
     * @param array $cc
     * $cc = array(array('cc' => test@test.fr,'ccName'=> 'Test Name'),array('cc'=>test2@test.com,'ccName'=> 'Test Second Name'));
     * or
     * $cc = array('cc' => test@test.fr,'ccName'=> 'Test Name');
     */
    public function setCc($cc = []) {
        $this->_cc = $cc;
        return $this;
    }

    /**
     *
     * @param type $jsonTemplate JSON String
     */
    public function setTemplate($jsonTemplate) {
        $this->_content = is_array($jsonTemplate) ? $jsonTemplate : $jsonTemplate;
        return $this;
    }

    /**
     *
     * @param type $data
     */
    public function setData($data = []) {
        $this->_data = $data;
    }

    /**
     *
     * @param type $h
     */
    public function setHeaders($h = []) {
        foreach ($h as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $this->_mail->setHeaders($name, $v);
                }
            } else {
                $this->_mail->setHeaders($name, $v);
            }
        }
    }

    /**
     *
     */
    public function send() {
        try {
            if (empty($this->_content))
                throw new Exception('Template not defined');
            if (empty($this->_config))
                throw new Exception('SetUp Config');
            $config = $this->_config;
            $a      = $this->_data;
            /*
              $fromName = preg_replace_callback('`{([^}]+)}`', function ($m) use ($a) {
              return isset($a[strtolower($m[1])]) ? $a[strtolower($m[1])] : '';
              }
              , $this->_content['fromName']);
             */

            $options   = new SmtpOptions($config);
            $transport = new Smtp();
            $transport->setOptions($options);

            $this->_mail->setFrom($this->_from['from'], $this->_from['fromName']);

            if (is_array($this->_to)) {
                foreach ($this->_to as $to) {
                    if (is_array($to)) {
                        if (isset($to['to']) && filter_var($to['to'], FILTER_VALIDATE_EMAIL)) {
                            $toMail = $to['to'];
                        } else {
                            throw new Exception('Invalid mail: ' . $to['to']);
                        }
                        $toName = isset($to['toName']) ? $to['toName'] : $to['to'];
                    } else {
                        if (isset($this->_to['to']) && filter_var($this->_to['to'], FILTER_VALIDATE_EMAIL)) {
                            $toMail = $this->_to['to'];
                        } else {
                            throw new Exception('Invalid mail: ' . $this->_to['to']);
                        }
                        $toName = isset($this->_to['toName']) ? $this->_to['toName'] : $this->_to['to'];
                        $this->_mail->addTo($toMail, $toName);
                        break;
                    }
                    $this->_mail->addTo($toMail, $toName);
                }
            }
            if (is_array($this->_replyTo)) {
                foreach ($this->_replyTo as $rpt) {
                    if (is_array($rpt)) {
                        if (isset($rpt['to']) && filter_var($rpt['to'], FILTER_VALIDATE_EMAIL)) {
                            $rptMail = $rpt['to'];
                        } else {
                            throw new Exception('Invalid mail: ' . $rpt['to']);
                        }
                        $rptName = isset($rpt['toName']) ? $rpt['toName'] : $rpt['to'];
                    } else {
                        if (isset($this->_replyTo['to']) && filter_var($this->_replyTo['to'], FILTER_VALIDATE_EMAIL)) {
                            $rptMail = $this->_replyTo['to'];
                        } else {
                            throw new Exception('Invalid mail: ' . $this->_replyTo['to']);
                        }
                        $rptName = isset($this->_replyTo['toName']) ? $this->_replyTo['toName'] : $this->_replyTo['to'];
                    }
                    $this->_mail->setReplyTo($rptMail, $rptName);
                }
            }

            $subject = preg_replace_callback('`{{([^}]+)}}`', function ($m) use ($a) {
                return isset($a[strtolower($m[1])]) ? $a[strtolower($m[1])] : '';
            }
                    , $this->_subject);
            $this->_mail->setSubject($subject);

            try {
                $htmlMarkup = preg_replace_callback('`{{([^}]+)}}`', function ($m) use ($a) {
                    return isset($a[strtolower($m[1])]) ? $a[strtolower($m[1])] : '';
                }
                        , $this->_content);
            } catch (Exception $exc) {

            }
            $html       = new MimePart($htmlMarkup);
            $html->type = "text/html";
            $body       = new MimeMessage();
            $body->setParts([$html]);
            $this->_mail->setBody($body);
            $transport->send($this->_mail);
        } catch (Exception $e) {
            echo $e->getMessage();
            throw $e;
        }

        return true;
    }

}
