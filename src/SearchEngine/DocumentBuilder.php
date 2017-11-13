<?php

/**
 * DocumentBuilder.php
 *
 * @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
 * @license    CeCILL-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 */

namespace SearchEngine;

/**
 * DocumentBuilder class
 *
 * @since  0.0.1
 */
class DocumentBuilder {

    /**
     * The constructor is not public
     */
    private function __construct() {

    }

    /**
     * Instantiation
     *
     * @return DocumentBuilder
     */
    public static function create() {
        return new static;
    }

    /**
     * Build a document
     *
     * @param   string  $path  The pathname
     *
     * @return  Document  A document
     */
    public function build($path) {
        return new Document($path);
    }

}
