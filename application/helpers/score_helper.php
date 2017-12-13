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
 * @author rizky Kharisma <ngeng.ngengs@gmail.com>
 *
 * Date: 8/4/2017
 * Time: 12:03 AM
 *
 * Created by PhpStorm.
 */

/*
| https://gist.github.com/julienbourdeau/77eaca0fd1e4af3fde9fe018fdf13d7d
|--------------------------------------------------------------------------
| 5 Star Rating
|--------------------------------------------------------------------------
|
| Lower bound of Wilson score confidence interval for a Bernoulli parameter (0.9604)
|
| See:
|  * http://www.evanmiller.org/how-not-to-sort-by-average-rating.html
|  * https://gist.github.com/richardkundl/2950196
|  * https://onextrapixel.com/how-to-build-a-5-star-rating-system-with-wilson-interval-in-mysql/
|
*/


if (!function_exists('score_weight')) {
    /**
     * @param $positive
     * @param $negative
     *
     * @return float|int
     */
    function score_weight($positive, $negative)
    {
        $sqrt = sqrt((($positive * $negative) / ($positive + $negative)) + 0.9604);
        $i = (($positive + 1.9208) / ($positive + $negative) - 1.96 * $sqrt / ($positive + $negative));

        return ($i / (1 + 3.8416 / ($positive + $negative)));
    }
}

if (!function_exists('five_star_rating_weight')) {
    /**
     * @param $one int Count of star 1
     * @param $two int Count of star 2
     * @param $three int Count of star 3
     * @param $four int Count of star 4
     * @param $five int Count of star 5
     *
     * @return float|int weight score
     */
    function five_star_rating_weight($one, $two, $three, $four, $five)
    {
        $positive = $two * 0.25 + $three * 0.5 + $four * 0.75 + $five;
        $negative = $one + $two * 0.75 + $three * 0.5 + $four * 0.25;

        return score_weight($positive, $negative);
    }
}

if (!function_exists('five_star_rating_avg_weight')) {
    /**
     * @param $avg
     * @param $total
     *
     * @return float|int
     */
    function five_star_rating_avg_weight($avg, $total)
    {
        $positive = ($avg * $total - $total) / 4;
        $negative = $total - $positive;

        return score_weight($positive, $negative);
    }
}

