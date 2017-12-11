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
 * Class Garage_data
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Garage_data extends Base_data
{
    private $TAG = 'Garage_data';

    /**
     * @var string $NAME
     */
    public $NAME;
    public $OPEN_HOUR;
    public $CLOSE_HOUR;
    /**
     * @var string $ADDRESS
     */
    public $ADDRESS;
    /**
     * @var float $LATITUDE
     */
    public $LATITUDE;
    /**
     * @var float $LONGITUDE
     */
    public $LONGITUDE;
    /**
     * @var int $FORCE_CLOSE value (1 or 0)
     */
    public $FORCE_CLOSE;

    /**
     * @return bool
     */
    public function is_force_close():bool
    {
        return ($this->FORCE_CLOSE == 1);
    }

}
