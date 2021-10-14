<?php

    /**
     * Will fetch a file and store on Disk
     *
     */

    class Downloader
    {

        protected $url;
        protected $pathToDownload;

       function __construct($url, $pathToDownload) {
            $this->url            = $url;
            $this->pathToDownload = $pathToDownload;
        }

        public function setUrl ($url) {
            $this->url = $url;
        }


        public function setDownloadPath ($downloadPath) {
            $this->pathToDownload = $downloadPath;
        }


        public function getUrl() {
            return $this->url;
        }

        public function getDownloadPath() {
            return $this->pathToDownload;
        }

        protected function validateUrl() {

            if (filter_var($this->url, FILTER_VALIDATE_URL)) {
                return 1;
            }
            else {
                throw new Exception('URL passed is invalid '. $this->url);
            }
        }

        protected function validateDownloadPath() {
            if (!is_dir($this->pathToDownload)) {
                throw new Exception('Directory is bogus '. $this->pathToDownload);
            }
        }

        protected function  validateMembers() {
            $this->validateUrl();
            $this->validateDownloadPath();
        }

        public function fetch () {
            echo "You must do your own implementation here";
        }

    }

?>
