<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SearchEngine;

/**
 * Description of TextParserTrait
 *
 * @author adrien
 */
trait TextParserTrait {
    
    /**
     * Parse document
     *
     * @param   string  $path  The pathname
     *
     * @return  array  Document meta data
     */
    public function parse($path) {
        $doc = $this->getParser()->load($path);
        $prop = $doc->getDocInfo();
        $meta = [];

        if ($prop->getCreator() != "") {
            $meta['creator'] = $prop->getCreator();
        }

        if ($prop->getLastModifiedBy() != "") {
            $meta['lastModifiedBy'] = $prop->getLastModifiedBy();
        }

        if ($prop->getCreated()) {
            $meta['created'] = $prop->getCreated();
        }

        if ($prop->getModified()) {
            $meta['modified'] = $prop->getModified();
        }

        if ($prop->getTitle() != "") {
            $meta['title'] = $prop->getTitle();
        }

        if ($prop->getDescription() != "") {
            $meta['description'] = $prop->getDescription();
        }

        if ($prop->getSubject() != "") {
            $meta['subject'] = $prop->getSubject();
        }

        if ($prop->getKeywords() != "") {
            $meta['keywords'] = $prop->getKeywords();
        }

        if ($prop->getCategory() != "") {
            $meta['category'] = $prop->getCategory();
        }

        if ($prop->getCompany() != "") {
            $meta['company'] = $prop->getCompany();
        }

        if ($prop->getManager() != "") {
            $meta['manager'] = $prop->getManager();
        }

        if (!empty($prop->getCustomProperties())) {
            foreach ($prop->getCustomProperties() as $key => $value) {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $meta[$key] = $value;
            }
        }

        return $meta;
    }
}
