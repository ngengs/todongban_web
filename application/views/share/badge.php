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
 * Date: 12/21/2017
 * Time: 7:30 AM
 *
 * Created by PhpStorm.
 *
 * @var int $badge
 * @var int $response_count
 * @var string $badge_name
 * @var \User_data $user
 * @var \Garage_data|null $garage
 */

$colors = ["purple-gradient", "peach-gradient", "blue-gradient"];
$selected_index = array_rand($colors);
$selected_color = $colors[$selected_index];
?>
<!doctype html>
<html lang="en">
<head>
    <title>Gelar <?php if ($user->TYPE == User_data::$TYPE_GARAGE && !empty($garage)) {
            echo $garage->NAME;
        } else {
            echo
            $user->FULL_NAME;
        } ?> | Todong Ban</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap CSS -->
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="<?php css_url('bootstrap-v4.min'); ?>">
    <link rel="stylesheet" href="<?php css_url('mdb.min'); ?>">
    <link rel="stylesheet" href="<?php css_url('mdb-compiled.min'); ?>">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src='https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js'></script>
    <script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
    <![endif]-->
    <style>
        html, body {
            height: 100%;
        }

        .md-gradient-red {
            background: linear-gradient(40deg, #B71C1C, #9C27B0);
        }

        .md-gradient-purple {
            background: linear-gradient(40deg, #9C27B0, #673AB7);
        }

        .animate-bg {
            -webkit-animation: bg-changer 10s ease infinite;
            -moz-animation: bg-changer 10s ease infinite;
            animation: bg-changer 10s ease infinite;
            background-size: 150% 150%;
            /*background-size: 400% 400%;*/
        }

        @-webkit-keyframes bg-changer {
            0% {
                background-position: 88% 0%
            }
            50% {
                background-position: 13% 100%
            }
            100% {
                background-position: 88% 0%
            }
        }

        @-moz-keyframes bg-changer {
            0% {
                background-position: 88% 0%
            }
            50% {
                background-position: 13% 100%
            }
            100% {
                background-position: 88% 0%
            }
        }

        @keyframes bg-changer {
            0% {
                background-position: 88% 0%
            }
            50% {
                background-position: 13% 100%
            }
            100% {
                background-position: 88% 0%
            }
        }
    </style>
</head>
<body class="<?php echo $selected_color; ?> animate-bg">
<main id="wrapper">
    <div class="container pt-1 pt-md-5">
        <!-- Content here -->
        <div class="row justify-content-center text-center">
            <div class="col-md-4 mt-md-5">

                <!--Card-->
                <div class="card testimonial-card">

                    <!--Background color-->
                    <div class="card-up <?php echo $selected_color; ?> lighten-2">
                    </div>

                    <!--Avatar-->
                    <div class="avatar"><img src="<?php avatar_user_url($user) ?>" alt="avatar" class="img-responsive">
                    </div>

                    <div class="card-body">
                        <!--Name-->
                        <?php if ($user->TYPE == User_data::$TYPE_GARAGE && !empty($garage)) { ?>
                            <h4 class="card-title mt-1 mb-0"><?php echo $garage->NAME; ?></h4>
                            <i class="fa fa-home"></i>&nbsp;
                            <small>Akun Bengkel</small>
                        <?php } else { ?>
                            <h4 class="card-title mt-1"><?php echo $user->FULL_NAME; ?></h4>
                            <i class="fa fa-user"></i>&nbsp;
                            <small>Akun Personal</small>
                        <?php } ?>
                        <hr>
                        <!--Quotation-->
                        <p>Gelar saya<br><i class="fa fa-quote-left"></i> <strong><?php echo $badge_name;
                                ?></strong></p>
                    </div>

                </div>
                <!--Card-->
                <?php
                $text_color = 'text-dark';
                if ($selected_color != 'peach-gradient') {
                    $text_color = 'text-white';
                } ?>
                <p class="<?php echo $text_color; ?> mt-5 pt-2 small">Saya sudah
                    <?php echo $response_count; ?> kali berusaha membatu kendaraan orang.<br>Kapan giliranmu?
                    <a href="<?php echo base_url('download'); ?>" class="<?php echo $text_color; ?> font-weight-bold"
                       style="text-decoration: underline" target="_blank" title="Download">Download sekarang</a></p>

            </div>
        </div>
    </div>
</main>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="<?php js_plugin_url('jquery/jquery.min'); ?>"></script>
<script src="<?php js_url('popper.min'); ?>"></script>
<script src="<?php js_url('bootstrap-v4.min'); ?>"></script>
<script src="<?php js_url('mdb.min'); ?>"></script>
</body>
</html>
