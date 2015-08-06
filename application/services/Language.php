<?php

class Application_Service_Language extends Zend_Translate
{
    private static $_instance = null;

    public static function getInstance($locale = null)
    {
        if (null === self::$_instance || $locale !== null)
        {
            self::$_instance = new Application_Service_Language(
                ['adapter' => 'array',
                    'content' => APPLICATION_PATH . '/languages/en.php',
                    'locale'  => 'en']
            );
            self::$_instance->addTranslation(
                ['content' => APPLICATION_PATH . '/languages/de.php',
                    'locale'  => 'de']
            );

            try
            {
                $settings = Application_Model_UserSettingRepository::getInstance()->getSetting();
                if ($settings)
                {
                    self::$_instance->setLocale(new Zend_Locale($settings->getLanguage()));
                }
                else if ('de' === substr(new Zend_Locale(Zend_Locale::BROWSER), 0, 2))
                {
                    self::$_instance->setLocale('de');
                }
                else
                {
                    self::$_instance->setLocale('en');
                }
            }
            catch (Exception $e)
            {
                self::$_instance->setLocale('en');
            }
        }
        return self::$_instance;
    }
}
