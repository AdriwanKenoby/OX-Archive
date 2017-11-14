<?php

/**
 * EngineBuilder.php
 *
 * @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
 * @license    CeCILL-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 */

namespace SearchEngine;

/**
 * EngineBuilder class
 *
 * @since  0.0.1
 */
class EngineBuilder {

    /**
     * The constructor is not public
     */
    private function __construct() {

    }

    /**
     * Instantiation
     *
     * @return  EngineBuilder
     */
    public static function create() {
        return new static;
    }

    /**
     * Build a Engine
     *
     * @return  EngineBuilder  A EngineBuilder
     */
    public function build($host, $port, $index, $type) {
        return new Engine($host, $port, $index, $type);
    }

}
