<?php

/**
 * Document.php
 *
 * @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
 * @license    CeCILL-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 */

namespace SearchEngine;

/**
 * Document class
 *
 * @since  0.0.1
 */
class Document {

    /**
     * Document path
     */
    private $path;

    /**
     * Document parser (lazy instantiation)
     */
    private $parser;

    /**
     * Document meta data (lazy instantiation)
     */
    private $meta;

    /**
     * Constructor
     *
     * @param   string  $path  pathname to document
     */
    public function __construct($path) {
        $this->path = realpath($path);

        if (!$this->path) {
            throw new \InvalidArgumentException('"' . $path . '" is not a filename');
        }
    }

    /**
     * Parse document
     *
     * @return  array  Document meta data
     */
    public function parse() {
        if (!isset($this->meta)) {
            $this->meta = $this->getParser()->parse($this->path);
            $this->meta['path'] = $this->path;
            $this->meta['filetype'] = $this->getParser()->__get('type');
        }

        return $this->meta;
    }

    /**
     * Get a parser for this document
     *
     * @return  Parser  The parser
     */
    protected function getParser() {
        if (!isset($this->parser)) {
            $this->parser = ParserBuilder::create()->build($this->path);
        }

        return $this->parser;
    }

}
