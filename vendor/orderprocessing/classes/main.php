<?php
set_include_path("./classes");
spl_autoload_register();
require_once  __DIR__.'/../../../vendor/autoload.php';
// change this program to be the Symfony cli
//read from config file
 $downloader   = new CurlDownloader('https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1/orders.jsonl', '/tmp/');

 $errorFile    =  OutputWriterFactory::getWriter("CSV");
 $outputWriter  = OutputWriterFactory::getWriter("CSV");

 $outputWriter->setFileName('/tmp/output.csv');
 $errorFile->setFilename('/tmp/errors.csv');
 $parser        = new ResponseParser();
 try {
     $fetchedFile = $downloader->fetch();
     $parser->setInputFileName($fetchedFile);
     $parser->parser(array(
          'inputFormat'   =>  'CSV',
          'errorFile'     =>  $errorFile,
          'outputWriter'  =>  $outputWriter

     ));

 } catch (Exception $exception) {
     echo $exception->getMessage();
 }
 finally {
   $outputWriter->closeFileHandle();
   $errorFile->closeFileHandle();
 }
