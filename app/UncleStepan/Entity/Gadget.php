<?php


    namespace App\UncleStepan\Entity;


    class Gadget
    {
        private $tagID;
        private $level;

        public function __construct($tagID, $level)
        {
            $this->tagID = $tagID;
            $this->level = $level;
        }

        private function __set($name, $value)
        {
            return false;
        }

        public function __get($name)
        {
            return $this->$name;
        }
    }
