<?php

class Application_Model_RssParser extends Application_Model_ParserAbstract
{
    public function parseWithMinDate($latestDate = null)
    {
        $this->_fead = new Application_Model_Entity_Fead();
        $this->_fead->setType('rss');

        $document = new DOMDocument();
        $document->loadXML($this->_data);
        // version
        //$this->_fead->_version = $document->getElementsByTagName('rss')->item(0)->getAttribute('version');

        foreach ($document->getElementsByTagName('item') as $article)
        {
            $a = new Application_Model_Entity_Article();
            $a->setFeadId($this->_feadId);
            for ($n = $article->firstChild; $n; $n = $n->nextSibling) {
                if ($n instanceof DOMElement) {
                    if ('title' === $n->tagName) {
                        $a->setTitle($n->nodeValue);
                    }
                    else if ('description' === $n->tagName) {
                        $a->setContent($n->nodeValue);
                        $preview = strip_tags($a->getContent(), ['p', 'span', 'i']);
                        $length = 255;
                        if ($length < strlen($preview))
                        {
                            $preview = substr($preview, 0, $length);
                        }
                        $a->setPreview($preview);
                    }
                    else if ('link' === $n->tagName) {
                        $a->setUrl($n->nodeValue);
                    }
                    else if ('pubDate' === $n->tagName) {
                        $date = $this->parseDate($n->nodeValue);

                        // check if article is newer than the latest in the database
                        if ($date <= $latestDate) {
                            return;
                        }

                        $a->setDateCreated($date);
                        $a->setDateModified($date);
                    }
                    else if ('guid' === $n->tagName) {
                        $a->setGuid($n->nodeValue);
                    }
                }
            }

            $a->fill();

            array_push($this->_articles, $a);
        }
    }
}