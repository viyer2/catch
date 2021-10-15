<?php

namespace Console\App\Classes;

use Symfony\Component\Yaml\Yaml;

class YAMLOutputWriter implements WriterInterface
  {
      public $fileName;
      protected $fileHandle;

      public function setFileName($fileName)
      {
          $this->fileName = $fileName;

          //open the file in append mode
          $this->fileHandle = fopen($fileName, 'w');

          if (!$this->fileHandle) {
              throw new \Exception("Cannot open file in write mode");
          }
      }
      public function closeFileHandle()
      {
          try {
              fclose($this->fileHandle);
          } catch (Exception $e) {
              throw (new \Exception($e->getMessage));
          }
      }

      public function writeHeader($headingStr)
      {
          $heading = array();
          array_push($heading, "---");
          $this->writeToFile($heading);
      }
      public function writeToFile($line)
      {

          $yaml = Yaml::dump($line, 4,8, Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE);

          if (!fwrite($this->fileHandle, $yaml)) {
              throw new \Exception("Cannot write line to file");
          }
      }
      public function writeRecord($record)
      {
          $yamlArr = array('ORDER_DETAILS' => $record['order_id'], 'DETAILS' => $record);
          $this->writeToFile($yamlArr);
      }
  }
