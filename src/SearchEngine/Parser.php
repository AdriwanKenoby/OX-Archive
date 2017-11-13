<?php

/**
 * Parser.php
 *
 * @copyright  2015 Christophe Demko christophe.demko@univ-lr.fr
 * @license    CeCILL-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html
 */

namespace SearchEngine;

/**
 * Parser class
 *
 * @since  0.0.1
 */
abstract class Parser {

    /**
     * Magic getter
     *
     * @param   string  $property  Property name
     *
     * @return  mixed  Property value
     *
     * @throw   \InvalidArgumentException
     */
    public function __get($property) {
        switch ($property) {
            case 'type':
                return static::$type;
            default:
                throw new \InvalidArgumentException('Property "' . $property . '" is invalid');
        }
    }

}
