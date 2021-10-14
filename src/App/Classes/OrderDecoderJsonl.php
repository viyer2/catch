<?php

    // lets get all the required fileds from the JSON
    // The question wants a specific set of fields
    // Later if you want to add more fields you can extend this class
    // All modifications to the data do be done here
    //

namespace Console\App\Classes;

    class OrderDecoderJsonl
    {
        public $heading  = "order_id, order_datetime, total_order_value, average_unit_price, distinct_unit_count, total_uinits_count, customer_state";
        public $jsonObj;

        // if this is set then drop the record
        public $isError;
        public $errorMessage;

        public $returnFields = array();

        public $itemCounts = array('TOTALS' => array('TOTAL_UNITS' => 0, 'TOTAL_VALUE' => 0), 'ITEMS' => array());

        protected $orderId;
        protected $orderDateTime;
        protected $totalOrderValue;
        protected $averageUnitPrice;
        protected $distinctUnitCount;
        protected $totalUnitCount;
        protected $customerState;
        protected $avgUnitPrices = array();

        public function __construct(string $line)
        {
            $this->decodeData($line);
        }

        protected function decodeData(string $line)
        {
            $this->decodeJsonl($line);
        }

        //Lets decode Json
        protected function decodeJsonl(string $line)
        {
            $this->jsonObj = json_decode($line);
            if ($this->jsonObj) {
                $this->processOrder();
            } else {
                $this->drop = 1;
                $this->errorMessage .= "No Json object passed \n";
            }
        }

        protected function processOrder()
        {
            $this->extractOrderId();
            $this->extractOrderDate();

            $this->calculateOrderData();
            $this->setTotalUnitCounts();
            $this->setDistinctUnitCount();
            $this->extractDeliveryState();
        }

        protected function extractOrderId()
        {
            $this->orderId = $this->jsonObj->{'order_id'};
            $this->setReturnFields('order_id', $this->orderId);
        }

        protected function extractOrderDate()
        {
            if ($this->jsonObj->{'order_date'}) {
                $dateFormatter = new DateFormatter();
                $dateFormatter->setDateString($this->jsonObj->{'order_date'});
                $this->setReturnFields('order_datetime', $dateFormatter->returnISO());
            } else {
                $this->drop = 1;
                $this->errorMessage .= "The order date was not supplied \n";
            }
        }

        protected function calculateOrderData()
        {

            // I will calculate all the fields i need from here so as to loop through only once
            if ($this->jsonObj->{'items'}) {
                foreach ($this->jsonObj->{'items'} as $items) {
                    $this->itemCounts['ITEMS'][$items->{'product'}->{'product_id'}] = $items->{'unit_price'};
                    $this->itemCounts['TOTALS']['TOTAL_UNITS'] += $items->{'quantity'};
                    //apply the dicounts on the total later
                    $this->itemCounts['TOTALS']['TOTAL_VALUE'] += $items->{'quantity'} * $items->{'unit_price'};
                }


                //apply discounts now
                $this->applyDiscount();

                if ($this->itemCounts['TOTALS']['TOTAL_VALUE'] <= 0) {
                    $this->drop =1;
                    $this->errorMessage .= "Order has zero value after applying discounts";
                }

                $this->itemCounts['TOTALS']['TOTAL_VALUE'] = number_format($this->itemCounts['TOTALS']['TOTAL_VALUE'], 2);
                $this->setReturnFields('total_order_value', $this->itemCounts['TOTALS']['TOTAL_VALUE']);
            } else {
                $this->drop = 1;
                $this->errorMessage .= "The order has no items or is of zero value \n";
            }
        }

        protected function applyDiscount()
        {
            $discount = $this->jsonObj->{'discounts'};
            
            if ($discount) {
                if (is_array($discount)) {
                    //There are more than on discounts
                    // a 30% discount and additional 20% discount does not mean you get 50%
                    // it results in a 44% discount so you have to calculate them individually
                    foreach ($discount as $discounts) {
                        $this->itemCounts['TOTALS']['TOTAL_VALUE'] =
                    $this->calculateDiscount($this->itemCounts['TOTALS']['TOTAL_VALUE'], $discounts);
                    }
                } else {
                    //there is only one discoiunt
                    $this->itemCounts['TOTALS']['TOTAL_VALUE'] =
                $this->calculateDiscount($itemCounts['TOTALS']['TOTAL_VALUE'], $discount);
                }
            }
        }


        protected function calculateDiscount($total, $discount)
        {
            if (!$discount->{'value'}) {
                $discount = 0;
            }
            $total = $total - ($total * ($discount->{'value'} / 100));
            return $total;
        }

        protected function extractDeliveryState()
        {
            $this->setReturnFields('customer_state', $this->jsonObj->{'customer'}->{'shipping_address'}->{'state'});
        }

        protected function setTotalUnitCounts()
        {
            $this->setReturnFields('total_units_count', $this->itemCounts['TOTALS']['TOTAL_UNITS']);
        }

        protected function setDistinctUnitCount()
        {
            $this->setReturnFields('distinct_units_count', count($this->itemCounts['ITEMS']));
        }

        // Iam  assuming this is a list of all the prices of each unit
        protected function calculateAverageUnitPrice()
        {
        }

        protected function setReturnFields($key, $value)
        {
            $this->returnFields[$key] = $value;
        }

        public function getReturnFields()
        {
            return $this->returnFields;
        }

        public function isError()
        {
            return $this->isError;
        }
        public function errorMessage()
        {
            $msgArray = array($this->errorMessage);
            array_push($msgArray, $this->orderId);
            return $msgArray;
        }
        public function returnHeading()
        {
            return $this->heading;
        }
    }
