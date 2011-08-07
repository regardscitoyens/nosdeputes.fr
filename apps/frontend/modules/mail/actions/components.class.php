<?php
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

    if (isset($this->action)) 
      throw new Exception('action should not be defined anymore');

    $this->getContext()->getConfiguration()->loadHelpers('Partial');
    
    $message = $this->getMailer()->compose(array('contact@nosdeputes.fr' => '"Nos Deputes (Ne pas repondre)"'),
		                                         $this->to,
												 $this->subject,
												 get_partial('mail/'.$this->partial, $this->mailContext)
										   );
    $this->getMailer()->send($message);
  }
}

?>