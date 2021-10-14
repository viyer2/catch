<?php
set_include_path("./classes");
set_include_path("../Interfaces");
spl_autoload_register();
  class CSVOutputWriter implements WriterInterface
  {
      public $fileName;
      protected $fileHandle;

      public function setFileName($fileName)
      {
          $this->fileName = $fileName;

          //open the file in append mode
          $this->fileHandle = fopen($fileName, 'w');

          if (!$this->fileHandle) {
              throw new Exception("Cannot open file in append mode");
          }
      }
      public function closeFileHandle()
      {
          try {
              fclose($this->fileHandle);
          } catch (Exception $e) {
              throw (new Exception($e->getMessage));
          }
      }

      public function writeHeader($headingStr) {
        $heading = array();
        array_push($heading, $headingStr);
        $this->writeToFile($heading);
      }
      public function writeToFile($line)
      {

        if (!
        fputcsv(
            $this->fileHandle,
            $line,
            $separator = ",",
            $enclosure = '"',
            $escape = "\\"

            )
          )
          {
            throw new Exception("Cannot write header line");
          }
      }
      public function writeRecord(array $record)
      {
          $this->writeToFile($record);
      }
  }
