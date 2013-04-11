<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

require_once (SY_DIR . '/vendor/html_mime_mail/htmlMimeMail5.php');

/**
 *
 * Basic mail class
 *
 */
class Simplify_Mail
{

  const MAIL = 'mail';

  const SMTP = 'smtp';

  public $htmlTemplate;

  public $textTemplate;

  public $htmlLayout = false;

  public $mailFrom;

  public $mailEngine = self::MAIL;

  public $smtpHost;

  public $smtpPort;

  public $smtpAuth;

  public $smtpUser;

  public $smtpPassword;

  /**
   *
   */
  protected $error = false;

  /**
   *
   */
  protected static $mail;

  /**
   *
   */
  protected $html;

  /**
   *
   */
  protected $text;

  /**
   *
   */
  public function getError()
  {
    return $this->error;
  }

  /**
   *
   */
  public function getHtml()
  {
    return $this->html;
  }

  /**
   *
   */
  public function getText()
  {
    return $this->text;
  }

  /**
   *
   */
  public function send($to, $subject, $data)
  {
    if (!$this->mailFrom) {
      $this->error = __('Missing mail_from parameter.');
      throw new Exception($this->error);
    }

    if (!$this->htmlTemplate) {
      $this->error = __('Missing html_template parameter.');
      throw new Exception($this->error);
    }

    $htmlTpl = $this->htmlTemplate;
    $textTpl = $this->textTemplate;

    $this->render($data, $htmlTpl, $textTpl);

    $mail = $this->getMail();
    $mail->setFrom($this->mailFrom);
    $mail->setSubject($subject);

    $to = (array) $to;

    $sent = $mail->send($to, $this->mailEngine);

    if (false === $sent) {
      $this->error = $mail->errors;

      sy_log('mail', $this->error);

      throw new Exception($this->error);
    }
  }

  /**
   *
   */
  protected function render($data, $htmlTpl, $textTpl = null)
  {
    $html = Simplify_View::factory();
    $html->setTemplate($htmlTpl);
    $html->setLayout($this->htmlLayout);
    $html->copyAll($data);
    $html = $html->render();

    if (empty($textTpl)) {
      $crlf = "\r\n";
      $text = $html;
      $text = preg_replace('#< */ *p *>|< *br */? *>#i', $crlf, $text);
      $text = strip_tags($text);
    }

    else {
      $text = Simplify_View::factory();
      $text->setTemplate($textTpl);
      $text->setLayout(false);
      $text->copyAll($data);
      $text = $text->render();
    }

    $this->html = $html;
    $this->text = $text;

    $mail = $this->getMail();
    $mail->setHTML($html);
    $mail->setText($text);
  }

  /**
   *
   */
  protected function getMail()
  {
    if (empty(self::$mail)) {
      self::$mail = new htmlMimeMail5();
      self::$mail->setCRLF("\n");
      self::$mail->setTextCharset('UTF-8');
      self::$mail->setHTMLCharset('UTF-8');
      self::$mail->setHeadCharset('UTF-8');
      self::$mail->setPriority('high');

      if ($this->mailEngine == self::SMTP) {
        self::$mail->setSMTPParams($this->smtpHost, $this->smtpPort, null, $this->smtpAuth, $this->smtpUser, $this->smtpPassword);
      }
    }

    return self::$mail;
  }

}
