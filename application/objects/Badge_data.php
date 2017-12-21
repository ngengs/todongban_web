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
 * Class Badge_data
 *
 * @author     rizky Kharisma <ngeng.ngengs@gmail.com>
 */
class Badge_data
{
    public static $LEVEL_STAGE_1 = 1;
    public static $LEVEL_STAGE_2 = 5;
    public static $LEVEL_STAGE_3 = 10;
    public static $LEVEL_STAGE_4 = 15;
    public static $LEVEL_STAGE_5 = 20;

    public static $WEIGHT_STAGE_1 = 1;
    public static $WEIGHT_STAGE_2 = 2;
    public static $WEIGHT_STAGE_3 = 3;
    public static $WEIGHT_STAGE_4 = 4;
    public static $WEIGHT_STAGE_5 = 5;

    /**
     * @var string[] $BADGE_NAME_PERSONAL
     */
    public static $BADGE_NAME_PERSONAL = [
        'Bermimpi Jadi Penolong',
        'Penolong Baru yang ',
        'Penolong Senior yang ',
        'Pahlawan Lokal yang ',
        'Pahlawan Rakyat yang ',
        'Super Hero yang '
    ];
    /**
     * @var string[] $BADGE_NAME_GARAGE
     */
    public static $BADGE_NAME_GARAGE = [
        'Bengkel yang Ingin Menolong',
        'Bengkel Sosial yang ',
        'Bengkel Penolong yang ',
        'Bengkel Pahlawan yang ',
        'Bengkel Super yang ',
        'Bengkel Istimewa yang '
    ];
    /**
     * @var string[] $BADGE_WEIGHT
     */
    public static $BADGE_WEIGHT = [
        'belum memikat orang',
        'mulai dibicarakan orang',
        'didukung banyak orang',
        'disukai banyak orang',
        'sangat dicintai banyak orang'
    ];

    public $BADGE;
    public $LEVEL;
    public $WEIGHT;
    private $USER_TYPE;

    public function __construct(int $badge = 0, ?int $user_type = null)
    {
        $this->BADGE = (int)$badge;
        $dozens = $badge / 10;
        $units = $badge - ($dozens * 10);
        $this->LEVEL = (int)$dozens;
        $this->WEIGHT = (int)$units;
        $this->USER_TYPE = (!is_null($user_type)) ? (int)$user_type : User_data::$TYPE_PERSONAL;
    }


    /**
     * @return string
     */
    public function badge_name()
    {
        $user_type = $this->USER_TYPE;
        $level = $this->LEVEL;
        if ($level < Badge_data::$LEVEL_STAGE_1) {
            if ($user_type == User_data::$TYPE_PERSONAL) {
                return Badge_data::$BADGE_NAME_PERSONAL[0];
            } else {
                return Badge_data::$BADGE_NAME_GARAGE[0];
            }
        } else {
            if ($level < Badge_data::$LEVEL_STAGE_2) {
                if ($user_type == User_data::$TYPE_PERSONAL) {
                    return Badge_data::$BADGE_NAME_PERSONAL[1] . $this->badge_weight();
                } else {
                    return Badge_data::$BADGE_NAME_GARAGE[1] . $this->badge_weight();
                }
            } else {
                if ($level < Badge_data::$LEVEL_STAGE_3) {
                    if ($user_type == User_data::$TYPE_PERSONAL) {
                        return Badge_data::$BADGE_NAME_PERSONAL[2] . $this->badge_weight();
                    } else {
                        return Badge_data::$BADGE_NAME_GARAGE[2] . $this->badge_weight();
                    }
                } else {
                    if ($level < Badge_data::$LEVEL_STAGE_4) {
                        if ($user_type == User_data::$TYPE_PERSONAL) {
                            return Badge_data::$BADGE_NAME_PERSONAL[3] . $this->badge_weight();
                        } else {
                            return Badge_data::$BADGE_NAME_GARAGE[3] . $this->badge_weight();
                        }
                    } else {
                        if ($level < Badge_data::$LEVEL_STAGE_4) {
                            if ($user_type == User_data::$TYPE_PERSONAL) {
                                return Badge_data::$BADGE_NAME_PERSONAL[4] . $this->badge_weight();
                            } else {
                                return Badge_data::$BADGE_NAME_GARAGE[4] . $this->badge_weight();
                            }
                        } else {
                            if ($user_type == User_data::$TYPE_PERSONAL) {
                                return Badge_data::$BADGE_NAME_PERSONAL[5] . $this->badge_weight();
                            } else {
                                return Badge_data::$BADGE_NAME_GARAGE[5] . $this->badge_weight();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function badge_weight()
    {
        $weight = $this->WEIGHT;
        $weight_name = Badge_data::$BADGE_WEIGHT;
        if ($weight < Badge_data::$WEIGHT_STAGE_1) {
            return $weight_name[0];
        } else {
            if ($weight < Badge_data::$WEIGHT_STAGE_2) {
                return $weight_name[1];
            } else {
                if ($weight < Badge_data::$WEIGHT_STAGE_3) {
                    return $weight_name[2];
                } else {
                    if ($weight < Badge_data::$WEIGHT_STAGE_4) {
                        return $weight_name[3];
                    } else {
                        return $weight_name[4];
                    }
                }
            }
        }
    }

    public function __toString()
    {
        return json_encode(['data' => $this, 'name' => $this->badge_name()]);
    }


}
