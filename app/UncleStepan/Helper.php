<?php


    namespace App\UncleStepan;


    class Helper
    {
        const CHOISE_SENSIVITY = 10000;

        /**
         * @throws \Throwable
         */
        static function choiseFromSet($set, $chance = null)
        {
            if ($chance) {
                $rnd = rand(0, static::CHOISE_SENSIVITY);
                $variants = array_combine($set, $chance);
                throw_if(!$variants);
                $currentChance = 0;

                foreach ($variants as $variant => $variantChance) {
                    $currentChance += $variantChance * self::CHOISE_SENSIVITY;

                    if ($rnd <= $currentChance) {
                        return $variant;
                    }
                }
            } else {
                shuffle($set);
                return $set[array_rand($set)];
            }

            return false;
        }
    }
