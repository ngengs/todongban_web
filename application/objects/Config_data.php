<?php
/**
 * Copyright (c) 2017 Rizky Kharisma (@ngengs)
 *
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Config_data
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Config_data extends Base_data
{
    /**
     * @var int $VEHICLE_MOTOR_CYCLE
     */
    public static $VEHICLE_MOTOR_CYCLE = 1;
    /**
     * @var int $VEHICLE_CAR
     */
    public static $VEHICLE_CAR = 2;

    public static $TYPE_MOTORCYCLE_FLAT_TIRE = "18e135f0-6406-11e7-bfcc-68f7286287bc";
    public static $TYPE_MOTORCYCLE_NO_FUEL = "22b36853-6406-11e7-bfcc-68f7286287bc";
    public static $TYPE_MOTORCYCLE_BROKEN = "25df3334-6406-11e7-bfcc-68f7286287bc";
    public static $TYPE_CAR_FLAT_TIRE = "2b095992-6406-11e7-bfcc-68f7286287bc";
    public static $TYPE_CAR_NO_FUEL = "2e3905d8-6406-11e7-bfcc-68f7286287bc";
    public static $TYPE_CAR_BROKEN = "317b5fd5-6406-11e7-bfcc-68f7286287bc";

    /**
     * @var string $ID_USER
     */
    public $ID_USER;
    /**
     * @var string $ID_HELP_TYPE
     */
    public $ID_HELP_TYPE;
    /**
     * @var string $NAME
     */
    public $NAME;
    /**
     * @var int $VEHICLE Use value from Config_data::$VEHICLE_MOTOR_CYCLE or Config_data::$VEHICLE_CAR
     */
    public $VEHICLE;

    public function __cast()
    {
        parent::__cast();
        $this->VEHICLE = (int)$this->VEHICLE;
    }

    public function __toString()
    {
        return json_encode($this);
    }


}
