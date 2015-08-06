<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    private function redirect($url)
    {
        if (!headers_sent())
        {
            header('Location: ' . $url);
            exit;
        }
        else: ?>
            <script type="text/javascript">
                window.location.href="<?php echo $url ?>";
            </script>
            <noscript>
            <meta http-equiv="refresh" content="0;url=<?php echo $url ?>">
            </noscript>
            <?php exit;
        endif;
    }

    protected function _initDoctype()
    {
        $path = '/var/vendor';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);

        $isCron = (0 === strpos($_SERVER['REQUEST_URI'], '/cron'));

        // link always to www.feader.eu
        if ('feader.eu' === $_SERVER['HTTP_HOST'])
        {
            if(!$isCron){
                $url = 'https://www.feader.eu' . $_SERVER['REQUEST_URI'];
                $this->redirect($url);
                Zend_Session::setOptions(['cookie_secure' => true]);
            }
        }
        else if('on' === $_SERVER['HTTPS'] && !$isCron){
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $this->redirect($url);
        }

        if (!$isCron)
        {
            Zend_Session::start();
        }

        // register new namespaces
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Feader_');
        $loader->registerNamespace('Facebook_');

        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    // used for page titles
    protected function _initPlaceholders()
    {
        $this->bootstrap('View');
        $view = $this->getResource('View');
        $view->doctype('XHTML1_STRICT');

        // Set the initial title and separator:
        $view->headTitle('feader')
             ->setSeparator(' - ');
    }
}

