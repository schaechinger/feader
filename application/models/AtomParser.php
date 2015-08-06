<?php

class Application_Model_AtomParser extends Application_Model_ParserAbstract
{
    public function parseWithMinDate($latestDate = null)
    {
        $this->_fead = new Application_Model_Entity_Fead();
        $this->_fead->setType('atom');
        $this->_fead->_version = 1.0;

        $document = new DOMDocument();
        $document->loadXML($this->_data);

        foreach ($document->getElementsByTagName('entry') as $article)
        {
            $a = new Application_Model_Entity_Article();
            $a->setFeadId($this->_feadId);
            for ($n = $article->firstChild; $n; $n = $n->nextSibling) {
                if ($n instanceof DOMElement) {
                    if ('title' === $n->tagName) {
                        $a->setTitle($n->nodeValue);
                    }
                    else if ('summary' === $n->tagName && !$a->getContent()) {
                        $a->setContent($n->nodeValue);
                    }
                    else if ('content' === $n->tagName) {
                        $a->setContent($n->nodeValue);
                    }
                    else if ('link' === $n->tagName) {
                        $a->setUrl($n->getAttribute('href'));
                    }
                    else if ('updated' === $n->tagName) {
                        $date = $this->parseDate($n->nodeValue);

                        // check if article is newer than the latest in the database
                        if ($date <= $latestDate) {
                            return;
                        }

                        $a->setDateCreated($date);
                        $a->setDateModified($date);
                    }
                    else if ('id' === $n->tagName) {
                        $a->setGuid(substr($n->nodeValue, strrpos($n->nodeValue, ':')));
                    }
                }
            }

            $a->fill();

            array_push($this->_articles, $a);
        }
    }
}