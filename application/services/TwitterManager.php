<?php

class Application_Service_TwitterManager
{
    private $_twitter;
    private $_cache;
    private static $_instance = null;


    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new Application_Service_TwitterManager();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        $frontendOptions =
            ['lifetime' => 60,
             'automatic_serialization' => true];

        $backendOptions = ['cache_dir' => substr(APPLICATION_PATH, 0, strrpos(APPLICATION_PATH, '/')) . '/data/cache/'];

        $this->_cache = Zend_Cache::factory('Core',
            'File',
            $frontendOptions,
            $backendOptions);

        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $options = $bootstrap->getOptions();

        $accessToken = new Zend_Oauth_Token_Access();
        $accessToken->setToken($options['twitter']['accessToken']);
        $accessToken->setTokenSecret($options['twitter']['accessTokenSecret']);

        $this->_twitter = new Zend_Service_Twitter(
            ['username' => 'radaspona',
             'accessToken' => $accessToken,
             'oauthOptions' =>
                ['consumerKey' => $options['twitter']['consumerKey'],
                 'consumerSecret' => $options['twitter']['consumerKeySecret']]]
        );
    }

    public function getTweets($count)
    {
        $tweets = $this->_cache->load('tweets');
        if ($tweets === false)
        {
            $tweets = json_decode($this->_twitter->statusesUserTimeline(['count' => $count])->getRawResponse());
            $this->_cache->save($tweets, 'tweets');
        }

        return $tweets;
    }

    public function getUsername()
    {
        return $this->_twitter->getUsername();
    }

    public function tweet($tweet)
    {
        return $this->_twitter->statusesUpdate($tweet);
    }
}