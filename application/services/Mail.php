<?php

class Application_Service_Mail
{
    private $_domain;
    private $_name;
    private $_transport;
    private $_translate;

    public function __construct()
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();

        $this->_domain = $options['webhost']['url'];
        $this->_name = $options['webhost']['title'];

        $this->_transport = new Zend_Mail_Transport_Smtp($options['smtp']['host'],
                            ['name'     => $options['smtp']['name'],
                             'auth'     => $options['smtp']['auth'],
                             'username' => $options['smtp']['username'],
                             'password' => $options['smtp']['password']]);

        $this->_translate = Application_Service_Language::getInstance();
    }

    public function report($message)
    {
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($message)
             ->setBodyHtml($message)
             ->setFrom('no-reply@' . $this->_domain, $this->_name)
             ->addTo('mail@' . $this->_domain, $this->_name . ' admin')
             ->setSubject('feader report')
             ->send($this->_transport);
    }

    public function send($user, $subject, $message, $disclaimer = true, $sendTo = null)
    {
        if ($disclaimer)
        {
            $message .= '<br><br>' . $this->_translate->_('mail_footer') . ' https://feader.eu/about/imprint';
        }

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText($message)
            ->setBodyHtml($message)
            ->setSubject($subject);
        if (is_null($sendTo))
        {
            $mail->setFrom('no-reply@' . $this->_domain, $this->_name)
                ->addTo($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName());
        }
        else
        {
            $mail->addTo($sendTo)
                ->setFrom($user->getEmail(), $user->getFirstName() . ' ' . $user->getLastName());
        }

        $mail->send($this->_transport);
    }

    public function sendAll($subject, $message)
    {

    }

    public function sendActivated(Application_Model_Entity_SignUp $signUp)
    {
        $locale = new Zend_Locale($signUp->getLanguage());
        $this->_translate = Application_Service_Language::getInstance($locale);
        $firstName = $signUp->getFirstName();
        $lastName = $signUp->getLastName();
        $link = stripslashes("https://$this->_domain/home/fead");
        $message = $this->_translate->_('mail_dear') . " $firstName $lastName,<br>" .
            $this->_translate->_('mail_activate_text') . "<br>$link";
        $subject = $this->_translate->_('mail_activate_subject');
        $this->send($signUp, $subject, $message);
    }

    public function sendSignUp(Application_Model_Entity_User $user, $code)
    {
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $link = stripslashes("https://$this->_domain/user/validation/code/$code");
        $message = $this->_translate->_('mail_dear') . " $firstName $lastName,<br>" .
            $this->_translate->_('mail_signup_text_1') . "<br>" .
            "$link<br><br>" .
            $this->_translate->_('mail_signup_text_2') . "<br>$code<br>https://$this->_domain/user/validation";
        $subject = $this->_translate->_('mail_signup_subject');
        $this->send($user, $subject, $message);
    }

    public function sendInvitation($email, Application_Model_Entity_User $user)
    {
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $link = stripslashes("https://$this->_domain/user/signup/code/29da0faeb47f91a8cef1bb2c1b5da19d");
        $message = $this->_translate->_('mail_dear') . ",<br>" .
            $this->_translate->_('mail_invite_text') . "$link<br><br>" .
            "$firstName $lastName";
        $subject = $this->_translate->_('mail_invite_subject');
        $this->send($user, $subject, $message, false, $email);
    }
}