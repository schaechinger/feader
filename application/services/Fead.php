<?php

class Application_Service_Fead extends Zend_Feed_Reader
{
    private static $_instance = null;

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param $url
     * @return array|null
     */
    private function findFead($url)
    {
        try {
            $import = $this->findFeedLinks($url);

            if (is_null($import)) {
                return null;
            }

            $url = [];
            $index = 0;

            if (!is_null($import->atom)) {
                $url[$index++] = $import->atom;
            }
            if (!is_null($import->rss)) {
                $url[$index++] = $import->rss;
            }
            if (!is_null($import->rdf)) {
                $url[$index++] = $import->rdf;
            }

            if (0 === $index) {
                return null;
            }

            return $url;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param array $url
     * @return Application_Model_Entity_Fead|null
     */
    private function importFead(array $url)
    {
        $import = null;

        for ($i = 0; $i < sizeof($url); $i++) {
            try {
                $import = $this->import($url[$i]);

                if (is_null($import)) {
                    continue;
                }

                break;
            } catch (Exception $e) {
            }

            try {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url[$i]);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                $data = curl_exec($curl);
                curl_close($curl);

                $import = $this->importString($data);

                if (is_null($import)) {
                    continue;
                }

                break;
            } catch (Exception $e) {
            }
        }

        if (!is_null($import)) {
            $fead = new Application_Model_Entity_Fead();
            $fead->setTitle($import->getTitle());
            $fead->setUrl($import->getFeedLink());

            return $fead;
        }

        return null;
    }

    /**
     * request the content of a url
     * @param $url String the url that should be requested
     * @return String[]|null
     */
    private function getContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        $header = substr($data, 0, strpos($data, '<') - 2);
        // get the status code of the http response
        $status = substr($header, 9, 3);

        if ('200' === $status) {
            return ['url' => $url,
                'data' => substr($data, strlen($header) + 2)];
        } // moved -> call right url
        else if ('3' === substr($status, 0, 1)) {
            $start = strpos($header, 'Location: ') + 10;
            $url = substr($header, $start, strpos($header, chr(10), $start) - $start - 1);
            return $this->getContent($url);
        } // error
        else {
            return null;
        }
    }

    /**
     * Search the given url for a fead or a link to it
     * @param $url String the url the fead should be included
     * @return Array|null
     */
    public function searchFead($url)
    {
        $content = $this->getContent($url);

        if (!$content) {
            return null;
        }

        $data = $content['data'];
        $url = $content['url'];

        // rss-fead
        if (strpos($data, '<rss')) {
            $titleStart = strpos($data, '>', strpos($data, '<title')) + 1;
            return [['url'   => $content['url'],
                     'title' => substr($data, $titleStart, strpos($data, '</title>', $titleStart) - $titleStart),
                     'type'  => 'rss']];
        } // atom-fead
        else if (strpos($data, '<feed')) {
            $titleStart = strpos($data, '>', strpos($data, '<title')) + 1;
            return [['url'   => $content['url'],
                     'title' => substr($data, $titleStart, strpos($data, '</title>', $titleStart) - $titleStart),
                     'type'  => 'atom']];
        } // html page (search for feads)
        else if (strpos(strtolower($data), '<!doctype') ||
            strpos($data, '<html')
        ) {
            // take only head
            $data = substr($data, strpos($data, '<head'), strpos($data, '</head>') + 7);

            $document = new DOMDocument();
            $document->loadHTML($data);
            $feads = [];

            foreach ($document->getElementsByTagName('link') as $element) {
                $type = $element->getAttributeNode('rel')->nodeValue;

                if ('alternate' === $type) {
                    if ('application/rss+xml' === $element->getAttributeNode('type')->nodeValue) {
                        $type = 'rss';
                    } else if ('application/atom+xml' === $element->getAttributeNode('type')->nodeValue) {
                        $type = 'atom';
                    }
                    $linkUrl = $element->getAttributeNode('href')->nodeValue;
                    if (0 === strpos($linkUrl, '/')) {
                        $realUrl = parse_url($url);
                        if (is_array($realUrl)) {
                            $scheme = $realUrl['scheme'];
                            if (is_null($scheme)) {
                                $scheme = 'http';
                            }
                            $linkUrl = $scheme . '://' . $realUrl['host'] . $linkUrl;
                        }
                    }
                    $title = $element->getAttributeNode('title')->nodeValue;
                    if (is_null($title) || 0 === strlen($title)) {
                        $title = $type . ' fead';
                    }

                    $fead = new Application_Model_Entity_Fead();
                    $fead->setUrl($linkUrl);
                    $fead->setType($type);
                    $fead->setTitle($title);

                    array_push($feads, [
                            'url'   => $linkUrl,
                            'title' => $title,
                            'type'  => $type
                        ]);
                }
            }

            if ([] === $feads) {
                return null;
            }

            return $feads;
        }
    }

    /**
     * update the given fead
     * @param $url String the url of the fead
     * @param $id int the fead's id if available
     */
    public function updateFead($url, $id)
    {
        $parser = null;
        $data = $this->getContent($url);
        if (!$data) {
            return;
        }
        $data = $data['data'];

        // rss-fead
        if (strpos($data, '<rss')) {
            $parser = new Application_Model_RssParser($data);
        }
        // atom-fead
        else if (strpos($data, '<feed')) {
            $parser = new Application_Model_AtomParser($data);
        }
        // error
        else {
            return;
        }

        $parser->setFeadId($id);
        $parser->parseWithMinDate();

        $articleRepo = Application_Model_ArticleRepository::getInstance();
        while ($article = $parser->nextArticle()) {
            $articleRepo->addArticle($article);
        }
    }
}