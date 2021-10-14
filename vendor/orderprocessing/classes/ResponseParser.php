<?php

set_include_path("./classes");
spl_autoload_register();

class ResponseParser
{
    protected $responseFile;
    protected $outputFormat;

    public function setInputFileName($fileName)
    {
        $this->responseFile = $fileName;

    }


    public function parser($params)
    {

        //the question says that the input is in JSONLINES format.
        $fileHandle = fopen($this->responseFile, 'r');
        $recCounter = 0;
        $rejectedOrders = 0;
        $validOrders = 0;

        if ($fileHandle) {
            while (($line = fgets($fileHandle)) !== false) {
                $recCounter += 1;
                // process the line read.
                $line = rtrim($line);
                //print gettype($line)."  Thisis the type \n";
                //Make objects of the order and do your magic

                $order = OrderDecoderFactory::getOrderDecoder($params['inputFormat'], $line);
                if ($order->isError()) {
                    $rejectedOrders +=1;
                    $prams->{'errorFile'}->writeRecord($order->errorMessage());
                } else {
                    $validOrders += 1;
                    if ($recCounter ==1) {
                          $params['outputWriter']->writeHeader($order->returnHeading());
                    }
                    $params['outputWriter']->writeRecord($order->getReturnFields());
                }
                // PHP garbage collector will free memory when it needs to
                unset($order);
            }
            fclose($fileHandle);
            print "Stats\n Total Recs Read: $recCounter\n Valid Orders: $validOrders \n Dropped Orders: $rejectedOrders\n ";
        } else {
            throw new Exception("Cannot open file for reading");
        }
    }
}
