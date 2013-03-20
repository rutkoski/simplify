<?php
    /**
    * o------------------------------------------------------------------------------o
    * | This package is licensed under the Phpguru license 2008. A quick summary is  |
    * | that the code is free to use for non-commercial purposes. For commercial     |
    * | purposes of any kind there is a small license fee to pay. You can read more  |
    * | at:                                                                          |
    * |                  http://www.phpguru.org/static/license.html                  |
    * o------------------------------------------------------------------------------o
    *
    * ? Copyright 2008 Richard Heyes
    */

    require_once('htmlMimeMail5.php');
    
    $mail = new htmlMimeMail5();

    /**
    * Set the from address
    */
    $mail->setFrom('Richard <richard@example.com>');
    
    /**
    * Set the subject
    */
    $mail->setSubject('Test email');
    
    /**
    * Set high priority
    */
    $mail->setPriority('high');

    /**
    * Set the text of the Email
    */
    $mail->setText('Sample text');
    
    /**
    * Set the HTML of the email
    */
    $mail->setHTML('<b>Sample HTML</b> <img src="background.gif">');
    
    /**
    * Set the delivery receipt of the email
    */
    $mail->setReceipt('test@test.com');
    
    /**
    * Add an embedded image
    */
    $mail->addEmbeddedImage(new fileEmbeddedImage('background.gif'));
    
    /**
    * Add an attachment
    */
    $mail->addAttachment(new fileAttachment('example.zip'));

    /**
    * Send the email
    */
    $address = 'fruity@licious.com';
    $result  = $mail->send(array($address));
?>

Email has been sent to <?=$address?>. Result: <?var_dump($result)?>