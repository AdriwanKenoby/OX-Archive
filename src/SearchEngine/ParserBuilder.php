<?php

/**
 * ParserBuilder.php
 *
 * @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
 * @license    CeCILL-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 */

namespace SearchEngine;

/**
 * ParserBuilder class
 *
 * @since  0.0.1
 */
class ParserBuilder {

    /**
     * The constructor is not public
     */
    private function __construct() {

    }

    /**
     * Instantiation
     *
     * @return ParserBuilder
     */
    public static function create() {
        return new static;
    }

    /**
     * Build a specific parser according to the pathname
     *
     * @param   string  $path  The pathname
     *
     * @return  Parser  A specif parser
     *
     * @throw   Exception
     */
    public function build($path) {
        $parts = pathinfo($path);
        $className = '\\SearchEngine\\' . ucfirst($parts['extension']) . 'Parser';

        if (class_exists($className)) {
            return new $className;
        } else {
            return new DefaultParser;
        }
    }

}
