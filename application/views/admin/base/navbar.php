<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author rizky Kharisma <ngeng.ngengs@gmail.com>
 *
 * Date: 8/4/2017
 * Time: 1:12 AM
 *
 * Created by PhpStorm.
 */
?>

<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </a>

    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <!--                    <img src="../../dist/img/user2-160x160.jpg" class="user-image" alt="User Image">-->
                    <span class="hidden-xs"><?php echo $user->FULL_NAME; ?></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <?php if (!empty($user->AVATAR)) { ?>
                            <img src="<?php avatar_user_url($user); ?>" class="img-circle" alt="User Image">
                        <?php } ?>

                        <p>
                            <?php echo $user->FULL_NAME; ?> - Admin
                            <small>Nomor identitas <?php echo $user->IDENTITY_NUMBER; ?></small>
                            <small>Bergabung semenjak <?php echo date('Y-M-d', strtotime($user->DATE_CREATE));
                            ?></small>
                        </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body">
                        <!-- /.row -->
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="<?php echo base_url('admin/setting'); ?>" class="btn btn-default
                            btn-flat">Settings</a>
                        </div>
                        <div class="pull-right">
                            <a href="<?php echo base_url('admin/auth/signout'); ?>" class="btn btn-default
                            btn-flat">Sign
                                out</a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
