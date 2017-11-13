<?php

/**
 * Engine.php
 *
 * @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
 * @license    CeCILL-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 */

namespace SearchEngine;

/**
 * Engine class
 *
 * @since  0.0.1
 */
class PdfParser extends Parser {

    private $parser;
    protected static $type = 'pdf';

    /**
     * Get the pdf parser (lazy instantiation)
     *
     * @return  \Smalot\PdfParser\Parser
     */
    protected function getPdfParser() {
        if (!isset($this->parser)) {
            $this->parser = new \Smalot\PdfParser\Parser;
        }

        return $this->parser;
    }

    /**
     * Parse document
     *
     * @param   string  $path  The pathname
     *
     * @return  array  Document meta data
     */
    public function parse($path) {
        $pdf = $this->getPdfParser()->parseFile($path);
        $details = $pdf->getDetails();
        $meta = [];

        foreach ($details as $property => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $meta[$property] = $value;
        }

        return $meta;
    }

}
