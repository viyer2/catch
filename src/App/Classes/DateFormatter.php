<?php
namespace Console\App\Classes;
class DateFormatter
    {
        public $dateString;
        public $timeStamp;

        public const ISO8601="Y-m-d\TH:i:sP";

        public function setDateString(string $dateString)
        {
            $this->dateString = $dateString;
            $this->convertToTimestampUTC();
        }

        protected function convertToTimestampUTC()
        {
            //$this->$timeStamp = strtotime($this->dateString);
            $this->timeStamp = new \DateTime($this->dateString, new \DateTimeZone('UTC'));
        }

        public function returnISO()
        {
            return $this->timeStamp->format(self::ISO8601);
        }
    }
