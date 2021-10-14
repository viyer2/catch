<?php
set_include_path ( "./classes" );
spl_autoload_register ();
  class OrderDecoderFactory {

      public static function getOrderDecoder (string $format, string $line) {
          strtoupper($format);
          switch($format) {
            case "JSONL":

                        $obj = new OrderDecoderJsonl($line);
                        break;

            default:
                      $obj = new OrderDecoderJsonl($line);
                      break;
          }


          return $obj;

      }

  }

?>
