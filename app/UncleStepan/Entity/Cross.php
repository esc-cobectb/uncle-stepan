<?php


    namespace App\UncleStepan\Entity;


    use App\UncleStepan\Helper;

    class Cross
    {
        const RAND_RANGE = 10000;
        const MAX_COUNT_NEW_TRANSPORT_IN_SNAP = 3;
        public $chanceDirection;
        public $transportSet;

        public function __construct()
        {
        }

        public function snap()
        {
            $countTransport = rand(0, self::MAX_COUNT_NEW_TRANSPORT_IN_SNAP);
            $crossSnap = [];

            for ($i = 0; $i < $countTransport; $i++) {
                $direction = Helper::choiseFromSet(
                    array_keys($this->chanceDirection),
                    array_values($this->chanceDirection)
                );
                $transport = Helper::choiseFromSet(
                    array_keys($this->transportSet),
                    array_values($this->transportSet)
                );

                $crossSnap[] = new $transport;
            }

            return $crossSnap;
        }

        /**
         * @return mixed
         */
        public function getChanceDirection()
        {
            return $this->chanceDirection;
        }

        /**
         * @param  mixed  $chanceDirection
         */
        public function setChanceDirection($chanceDirection): void
        {
            $this->chanceDirection = $chanceDirection;
        }

        /**
         * @return mixed
         */
        public function getTransportSet()
        {
            return $this->transportSet;
        }

        /**
         * @param  mixed  $transportSet
         */
        public function setTransportSet($transportSet): void
        {
            $this->transportSet = $transportSet;
        }

    }
