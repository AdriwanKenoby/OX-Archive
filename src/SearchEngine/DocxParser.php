<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SearchEngine;

/**
 * Description of DocxParser
 *
 * @author adriwonkenobe
 */
class DocxParser extends Parser{

    private $parser;
    protected static $type = 'docx';

    protected function getDocxParser() {
        throw new Exception("docx support not implemened yet");
   }

   /**
     * Parse document
     *
     * @param   string  $path  The pathname
     *
     * @return  array  Document meta data
     */
    public function parse($path) {
        throw new Exception("docx support not implemened yet");
    }
}
