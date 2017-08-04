<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author rizky Kharisma <ngeng.ngengs@gmail.com>
 *
 * Date: 8/4/2017
 * Time: 1:13 AM
 *
 * Created by PhpStorm.
 */
?>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php if (!empty($user->AVATAR)) { ?>
                    <img src="<?php avatar_user_url($user); ?>" class="img-circle" alt="User Image">
                <?php } ?>
            </div>
            <div class="pull-left info">
                <p><?php echo $user->FULL_NAME; ?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu text-capitalize" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li <?php if (!empty($menu) && $menu === 1) echo 'class="active"'; ?>>
                <a href="<?php echo base_url('admin/dashboard'); ?>">
                    <i class="fa fa-home"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="treeview  <?php if (!empty($menu) && ($menu === 20 || $menu === 21)) echo 'active'; ?>">
                <a href="<?php echo base_url('admin/dashboard'); ?>">
                    <i class="fa fa-sign-in"></i> <span>Pendaftar</span>
                    <span class="pull-right-container">
                        <?php if (empty($count_rejected) && empty($count_validation)) { ?>
                            <i class="fa fa-angle-left pull-right"></i>
                        <?php } ?>
                        <?php if (!empty($count_rejected)) { ?>
                            <small class="label pull-right bg-red">
                            <?php echo $count_rejected; ?>
                            </small>
                        <?php } ?>
                        <?php if (!empty($count_validation)) { ?>
                            <small class="label pull-right bg-blue">
                            <?php echo $count_validation; ?>
                            </small>
                        <?php } ?>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li<?php if($menu == 20) echo ' class="active"';?>><a href="<?php echo base_url('admin/manage/register/validation');
                    ?>">
                            <i class="fa fa-circle-o"></i> Belum Validasi
                            <span class="pull-right-container">
                            <?php if (!empty($count_validation)) { ?>
                                <small class="label pull-right bg-blue">
                            <?php echo $count_validation; ?>
                            </small>
                            <?php } ?>
                        </span>
                        </a>
                    </li>
                    <li<?php if($menu == 21) echo ' class="active"';?>><a href="<?php echo base_url('admin/manage/register/rejected'); ?>">
                            <i class="fa fa-circle-o"></i> Telah Tolak
                            <span class="pull-right-container">
                            <?php if (!empty($count_rejected)) { ?>
                                <small class="label pull-right bg-red">
                            <?php echo $count_rejected; ?>
                            </small>
                            <?php } ?>
                            </span>
                        </a></li>
                </ul>
            </li>
            <li class="treeview  <?php if (!empty($menu) && ($menu === 30 || $menu === 31)) echo 'active'; ?>">
                <a href="<?php echo base_url('admin/dashboard'); ?>">
                    <i class="fa fa-users"></i> <span>Pengguna</span>
                    <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li<?php if($menu == 30) echo ' class="active"';?>><a href="<?php echo base_url('admin/manage/user'); ?>">
                            <i class="fa fa-circle-o"></i> Data Pengguna Aktif
                        </a>
                    </li>
                    <li<?php if($menu == 31) echo ' class="active"';?>><a href="<?php echo base_url('admin/manage/user/user-banned'); ?>">
                            <i class="fa fa-circle-o"></i> Data Pengguna Blokir
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
