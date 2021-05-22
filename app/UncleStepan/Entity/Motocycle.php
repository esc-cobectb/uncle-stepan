<?php


    namespace App\UncleStepan\Entity;


    class Motocycle extends AbstractTransport
    {
        const MIN_CHILD_COUNT = 1;
        const MAX_CHILD_COUNT = 1;

        public function __construct()
        {
            parent::__construct();
        }

    }
