<?php

    namespace Console\App\Classes;
    include 'Downloader.php';

    class CurlDownloader extends Downloader
    {
        public function fetch()
        {
            $this->validateMembers();

            //Curl specific implemntaion

            $curlHandler = curl_init($this->url);
            $fileName = basename($this->url);
            $fileName = $this->getDownloadPath().$fileName;
            // Will simply over write callers responsibility to ensure that
            // file names are unique

            $fileHandle = fopen($fileName, 'w');

            if ($fileHandle) {
                curl_setopt($curlHandler, CURLOPT_FILE, $fileHandle);
                curl_setopt($curlHandler, CURLOPT_HEADER, 0);

                // Perform a cURL session
                curl_exec($curlHandler);

                // Closes a cURL session and frees all resources
                curl_close($curlHandler);
                // Close file
                fclose($fileHandle);
            } else {
                throw new \Exception('File Handler error ');
            }

            // I fetched to here
            return $fileName;
        }
    }
