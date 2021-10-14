<?php
namespace Console\App\Classes;
  interface WriterInterface {
      public function writeHeader(array $heading);
      public function writeRecord(array $record);
      public function setFileName(string $fileName);
      public function closeFileHandle();
  }

?>
