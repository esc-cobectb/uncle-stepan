<?php


    namespace App\UncleStepan\Traits;

    trait HaveDevices
    {
        public $childs = [];
        public $ownDevices = 0;

        public function getCountDevices(){
            $count = $this->ownDevices;

            if (!empty($this->child)){
                foreach ($this->childs as $child) {
                    $count += $child->getCountDevises;
                }
            }

            return $count;
        }
    }
