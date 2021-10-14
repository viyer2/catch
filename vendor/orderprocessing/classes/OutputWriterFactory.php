<?php
    class OutputWriterFactory {

        public static function getWriter(string $type) {

            strtoupper($type);
            switch($type)  {
                case('CSV') :
                               $obj = new CSVOutputWriter();
                               break;
                case('YAML'):
                               $obj = new YAMLOutputWriter();
                default:
                              $obj = new CSVOutputWriter();
                              break;
                }
            return $obj;
        }

    }

?>
