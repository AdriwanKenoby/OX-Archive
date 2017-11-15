<?php

namespace SearchEngine;

trait ImageParserTrait {

  /**
    * Parse document
    *
    * @param   string  $path  The pathname
    *
    * @return  array  Document meta data
    */
   public function parse($path) {

       $exif = exif_read_data($path);
       $meta = [];

       foreach ($exif as $property => $value) {
           if (is_array($value)) {
               $value = implode(', ', $value);
           }
           $meta[$property] = $value;
       }

       return $meta;
   }
}
