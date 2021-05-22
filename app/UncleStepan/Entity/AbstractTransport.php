<?php


    namespace App\UncleStepan\Entity;


    use App\UncleStepan\Helper;
    use App\UncleStepan\Traits\HaveDevices;

    class AbstractTransport
    {
        use HaveDevices;

        public $type;
        public $direction;

        public function __construct()
        {
            $directionSet = [
                'to_straight' => 0.83,
                'to_right' => 0.12,
                'to_left' => 0.05,
                'to_forward' => 0
            ];

            $this->type = class_basename(static::class);
            $this->direction = Helper::choiseFromSet(
                array_keys($directionSet),
                array_values($directionSet)
            );

            $countChildren = rand(static::MIN_CHILD_COUNT, static::MAX_CHILD_COUNT );
            for ($i = 0; $i <$countChildren; $i++ ){
                $this->addChild();
            }
            $this->totalDevices = $this->getTotalDevices();
        }

        private function addChild()
        {
            $this->childs[] = new Man();
        }

        public function getTotalDevices()
        {
            $count = $this->ownDevices;
            foreach ($this->childs as $child) {
                $count += $child->getTotalDevices();
            }
            return $count;
        }
    }
