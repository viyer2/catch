<?php
namespace Console\App\Classes;
    class OutputWriterFactory
    {
        public static function getWriter(string $type)
        {
            strtoupper($type);
            switch ($type) {
                case('CSV') :
                               $obj = new CSVOutputWriter();
                               break;
                case('YAML'):
                               $obj = new YAMLOutputWriter();
                               // no break
                default:
                              $obj = new CSVOutputWriter();
                              break;
                }
            return $obj;
        }
    }
