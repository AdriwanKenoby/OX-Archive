<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SearchEngine;

/**
 * Description of JpgParser
 *
 * @author adriwonkenobe
 */
class JpegParser extends Parser {

    use ImageParserTrait;

    protected static $type = 'jpeg';

}
