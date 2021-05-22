<?php


    namespace App\UncleStepan\Entity;


    class Man extends AbstractTransport
    {
        const MIN_CHILD_COUNT = 0;
        const MAX_CHILD_COUNT = 0;

        public function __construct()
        {
            $this->type = class_basename(static::class);
            $this->ownDevices = rand(1,2);
            $this->totalDevices = $this->ownDevices;
            $this->direction = false;
        }
    }
