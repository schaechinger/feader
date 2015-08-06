<?php

class CronController extends Feader_ControllerAbstract
{
    public function init()
    {
        parent::init();

        $this->_helper->layout->disableLayout();

        $this->_resource = 'guest';
    }

    public function indexAction()
    {

    }

    public function articleAction()
    {
        if ($this->getParam('secret') !== md5('keepOnFeading'))
        {
            $this->redirect('cron/noaccess?from=' . urlencode($_SERVER['REQUEST_URI']));
        }

        $this->updateArticles($this->getParam('ids'));
    }

    public function bingAction()
    {
        $url = 'http://www.bing.com/default.aspx';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);

        $end = strpos($data, '.jpg') + 4;
        $start = strpos($data, '/az/', $end - 70);
        $path =  substr($data, $start, $end - $start);

        $descAnchor = strpos($data, 'hpcCopyInfo"');
        $descStart = strpos($data, '<p>', $descAnchor) + 3;
        $descEnd = strpos($data, '</p>', $descAnchor);

        $desc = substr($data, $descStart, $descEnd - $descStart);
        $copyIndex = strpos($desc, '&#169;');
        if ($copyIndex > 0)
        {
            $desc = substr($desc, 0, $copyIndex - 2);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://www.bing.com' . $path);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $data = curl_exec($curl);
        curl_close($curl);

        $filename = realpath(APPLICATION_PATH . '/../data/uploads/bing') . '/' .
            date('Y-m-d') . ' - ' . $desc . '.jpg';
        echo file_put_contents($filename, $data) === 0 ? 'error' : '';
    }

    public function feadAction()
    {
        // TODO handle updated articles (compare timestamp (updated time?) and title)
        // TODO handle articles without timestamp

        if ($this->getParam('secret') !== md5('keepOnFeading'))
        {
            $this->redirect('cron/noaccess?from=' . urlencode($_SERVER['REQUEST_URI']));
        }

        $id = $this->getParam('id');
        if (is_null($id))
        {
            return;
        }

        $output = $id . ' - ';

        $debug = $this->getParam('debug');

        $this->updateFead($id, $debug);
    }

    public function noaccessAction()
    {
        Application_Model_CronRepository::getInstance()->noaccess(urldecode($this->getParam('from')));
    }

    public function testAction()
    {
        $date = time();

        if (null === $this->getParam('next'))
        {
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, 'http://test.feader.eu/cron/test/next/true');
            curl_setopt ($ch, CURLOPT_USERPWD, false); // 'user:pw'
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $result = curl_exec ($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            $date = time() - $date;

            echo $result . '<br>' . $date;
        }
        else
        {
            for ($i = 0; $i < 10; $i++)
            {
                sleep(1);
            }

            $date = time() - $date;
            $this->_mail->report('update took ' . $date . ' seconds');
        }
    }

    public function userAction()
    {
        /*
        if ($this->getParam('secret') !== md5('keepOnFeading'))
        {
            $this->redirect('cron/noaccess?from=' . urlencode($_SERVER['REQUEST_URI']));
        }
        */

        $this->updateUsers();
    }

    private function updateUsers()
    {
        $date = $this->_userRepo->getLatestDate();
        $signUps = Application_Model_SignUpRepository::getInstance()->processSignup(5, $date);

        foreach ($signUps as $signUp)
        {
            $id = $this->_userRepo->add($signUp);
            $language = Application_Model_SignUpRepository::getInstance()->get($signUp->getId())->getLanguage();
            Application_Model_SignUpRepository::getInstance()->setId($id, $signUp->getEmail());
            Application_Model_UserSettingRepository::getInstance()->addSetting($id, $language);
            if ($signUp->isValidated())
            {
                $this->_mail->sendActivated($signUp);
            }
        }
    }

    private function updateArticles($ids = null)
    {
        if (is_null($ids))
        {
            $ids = Application_Model_FeadRepository::getInstance()->getAllIds();
            for ($i = 0; $i < sizeof($ids); $i++)
            {
                $ids[$i] = $ids[$i]['id'];
            }

            $size = 50;
            // split to seperate jobs
            if ($size < sizeof($ids))
            {
                $first = 0;
                $last = ($size > sizeof($ids)) ? sizeof($ids) : $size;

                while ($last <= sizeof($ids))
                {
                    $param = '';
                    for ($i = $first; $i < $last; $i++)
                    {
                        $param .= $ids[$i] . ',';
                    }
                    $param = substr($param, 0, -1);

                    $ch = curl_init();
                    $url = 'http://feader.eu/cron/article/secret/' . $this->getParam('secret') . '/ids/' . $param;

                    if ($this->getParam('debug'))
                    {
                        echo $url . '<br>';
                    }

                    curl_setopt ($ch, CURLOPT_URL, $url);
                    //curl_setopt ($ch, CURLOPT_USERPWD, $auth);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                    $result = curl_exec ($ch);
                    $info = curl_getinfo($ch);
                    curl_close($ch);
                    if ($last === sizeof($ids))
                    {
                        break;
                    }

                    $first = $last;
                    $last = ($last + $size > sizeof($ids)) ? sizeof($ids) : $last + $size;
                }

                return;
            }
        }
        else
        {
            $ids = explode(',', $ids);
        }

        foreach ($ids as $id)
        {
            $url = 'http://feader.eu/cron/fead/secret/' . $this->getParam('secret') . '/id/' . $id;

            if ($this->getParam('debug'))
            {
                echo $url . '<br>';
            }

            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            //curl_setopt ($ch, CURLOPT_USERPWD, $auth);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            $result = curl_exec ($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
        }
        // TODO log results
    }

    public function updateFead($id, $debug=null)
    {
        $frontendOptions = ['lifetime' => 60, 'automatic_serialization' => true];
        $backendOptions = ['cache_dir' => substr(APPLICATION_PATH, 0, strrpos(APPLICATION_PATH, '/')) . '/data/cache/'];
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        $output = '';

        Zend_Registry::set('Zend_Locale',  new Zend_Locale('de'));
        Zend_Locale::setCache($cache);

        $repository = Application_Model_ArticleRepository::getInstance();
        $feadData = Application_Model_FeadRepository::getInstance()->get($id);
        $date = $repository->getLatestDateForFead($id);

        // close db connections
        Application_Model_ArticleRepository::getInstance()->closeConnection();

        try
        {
            $fead = Application_Service_Fead::import(strtolower($feadData->getUrl()));

        }
        catch (Exception $e)
        {
            if ($debug)
            {
                $output .= $feadData->getTitle() . ': ' . $e->getMessage() . '<br>';
            }
            try
            {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $feadData->getUrl());
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                $data = curl_exec($curl);
                curl_close($curl);

                $fead = Application_Service_Fead::importString($data);
            }
            catch (Exception $e)
            {
                $output .= $feadData->getTitle() . ': ' . $e->getMessage() . '<br>';
                echo $feadData->getTitle() . ': ' . $e->getMessage();

                return;
            }
        }

        if ($debug)
        {
            $output .= $feadData->getTitle() . '<br>';
        }

        $articles = [];

        foreach ($fead as $i => $article)
        {
            $articleDate = $article->getDateModified();
            $now  = new Zend_Date();
            $now = $now->toString('YYYY-MM-dd HH:mm:ss');

            if (!is_null($articleDate) && $now > $articleDate->toString('YYYY-MM-dd HH:mm:ss'))
            {
                $articleDate = $articleDate->toString('YYYY-MM-dd HH:mm:ss');
            }
            else
            {
                $articleDate = $now;
            }

            if ($debug)
            {
                $output .= $articleDate . ' > ' . $date . '?<br>';
            }

            if ($articleDate <= $date)
            {
                break;
            }

            if ($debug)
            {
                $output .= '.';
            }
            $content = trim($article->getDescription(), ['script']);
            $preview = substr(trim(strip_tags($content, ['a', 'img', 'script'])), 0, 255);
            $url = $article->getLink();

            $thumb = strpos($article->getContent(), '<img');
            if ($thumb)
            {
                $thumb = strpos($article->getContent(), 'src="http', $thumb);
                $end = strpos($article->getContent(), '"', $thumb + 5);
                $thumb = substr($article->getContent(), $thumb + 5, $end - $thumb - 5);
            }
            else
            {
                $thumb = null;
            }

            $articles[$i] = new Application_Model_Entity_Article(
                ['feadId'       => $feadData->getId(),
                 'title'        => substr($article->getTitle(), 0, 255),
                 'preview'      => $preview,
                 'url'          => $url,
                 'thumb'        => $thumb,
                 'dateCreated'  => $article->getDateCreated()->toString('YYYY-MM-dd HH:mm:ss'),
                 'dateModified' => $articleDate,
                 'content'      => $content]
            );
        }

        Application_Model_ArticleRepository::getInstance()->openConnection();

        // import from oldest to youngest
        for ($i = sizeof($articles) - 1; $i >= 0; $i--)
        {
            Application_Model_ArticleRepository::getInstance()->addArticle($articles[$i]);
        }
        if ($debug)
        {
            echo $output;
        }
    }
}