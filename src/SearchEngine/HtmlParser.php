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
class HtmlParser extends Parser {

    use TextParserTrait;

    private $parser;
    protected static $type = 'html';

    protected function getParser() {
        if (!isset($this->parser)) {
            return $this->parser  = \PhpOffice\PhpWord\IOFactory::createReader('HTML');
        }
        return $this->parser;
    }

}
