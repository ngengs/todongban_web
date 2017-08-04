<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author rizky Kharisma <ngeng.ngengs@gmail.com>
 *
 * Date: 8/4/2017
 * Time: 1:11 AM
 *
 * Created by PhpStorm.
 */

$data['user'] = $user;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo (!empty($title) ? $title . (!empty($sub_title) ? ' ' . $sub_title : '') . ' | ' : '')
                      . $base_title; ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php css_url('bootstrap.min'); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php css_plugin_url('fontawesome/css/font-awesome.min'); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php css_url('adminlte.min'); ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php css_url('skins/skin-red'); ?>">
    <!--    Plugins-->
    <link rel="stylesheet" href="<?php css_plugin_url('datatables/datatables.bootstrap.min') ?>">
    <link rel="stylesheet" href="<?php css_plugin_url('fancybox/jquery.fancybox.min') ?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src='https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js'></script>
    <script src='https://oss.maxcdn.com/respond/1.4.2/respond.min.js'></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="../../index2.html" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <?php
            $length_app_name_short = strlen($app_name_short);
            $middle_app_name_short = round($length_app_name_short / 2);
            ?>
            <span class="logo-mini"><b><?php echo substr($app_name_short, 0, $middle_app_name_short); ?></b><?php
                echo substr($app_name_short,
                            $middle_app_name_short,
                            $length_app_name_short - $middle_app_name_short); ?>
                </span>
            <!-- logo for regular state and mobile devices -->
            <?php $app_name_split = explode(' ', $app_name); ?>
            <span class="logo-lg"><b><?php echo $app_name_split[0]; ?></b><?php echo $app_name_split[1]; ?></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <?php $this->view('admin/base/navbar', $data); ?>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <?php $this->view('admin/base/sidebar', $data); ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header text-capitalize">
            <?php if (!empty($title)) { ?>
                <h1>
                    <?php echo $title; ?>
                    <?php if (!empty($sub_title)) { ?>
                        <small><?php echo $sub_title; ?></small><?php } ?>
                </h1>
            <?php } ?>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('admin/dashboard'); ?>"><i class="fa fa-dashboard"></i>
                        Dashboard</a></li>
                <?php if (!empty($breadcrumb)) {
                    foreach ($breadcrumb as $key => $value) { ?>
                        <li<?php if (empty($value["url"])) echo ' class="active"'; ?>>
                            <?php if (!empty($value["url"])){ ?>
                            <a href="<?php echo base_url($value["url"]); ?>">
                                <?php } ?>
                                <?php echo $value["title"]; ?>
                                <?php if (!empty($value["url"])){ ?>
                            </a>
                        <?php } ?>
                        </li>
                    <?php }
                } ?>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">

            <?php if (!empty($error)) { ?>
                <!-- Error Message -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                            <h4>Error!</h4>
                            <p><?php echo $error; ?></p>
                        </div>
                    </div>
                </div>
                <!-- ./Error Message -->
            <?php } ?>
