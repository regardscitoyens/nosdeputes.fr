<?php

require_once sfConfig::get('sf_lib_dir').'/vendor/swift/swift_init.php'; 

class mailComponents extends sfComponents
{
  public function executeSend()
  {

    if (!isset($this->subject))
      throw new Exception('subject needed');

    if (!isset($this->to)) 
      throw new Exception('to needed');

    if (!isset($this->partial)) 
      throw new Exception('partial needed');

    if (!isset($this->mailContext)) 
      throw new Exception('mailContext needed');

    if (!isset($this->action)) 
      throw new Exception('action needed (reference to the calling action)');

    $transport = Swift_SmtpTransport::newInstance();

    $mailer = Swift_Mailer::newInstance($transport);

    $message = Swift_Message::newInstance()

      //Give the message a subject
      ->setSubject($this->subject)

      //Set the From address with an associative array
      ->setFrom(array('devnull@nosdeputes.fr' => 'Nos Deputes (Ne pas repondre)'))

      //Set the To addresses with an associative array
      ->setTo($this->to)

      //Give it a body
      ->setBody($this->action->getPartial($this->partial, $this->mailContext)
      ;

    $result = $mailer->send($message);

  }
}