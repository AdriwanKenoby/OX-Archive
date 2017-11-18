<?php

namespace SearchEngine;

/**
 * Description of JpgParser
 *
 * @author adriwonkenobe
 */
class DefaultParser extends Parser {

    protected static $type;

    /**
     * Parse document
     *
     * @param   string  $path  The pathname
     *
     */
    public function parse($path) {
        DefaultParser::$type = ucfirst(pathinfo($path)['extension']);
        return [];
    }

}
