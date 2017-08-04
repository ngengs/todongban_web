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
?>

</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<footer class="main-footer">
    <div class="pull-right hidden-xs">
        <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2014-2016 <a href="<?php echo base_url();?>"><?php echo $app_name; ?></a>.</strong> All rights
    reserved.
</footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php js_plugin_url('jquery/jquery.min'); ?>"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php js_url('bootstrap.min'); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php js_url('adminlte.min'); ?>"></script>
<!--Plugins-->
<script src="<?php js_plugin_url('datatables/datatables.min');?>"></script>
<script src="<?php js_plugin_url('datatables/datatables.bootstrap.min');?>"></script>
<script src="<?php js_plugin_url('fancybox/jquery.fancybox.min');?>"></script>
<script>
    $('document').ready(function(){
        console.log('Why you open this?');
        var $tables = $('.data-table');
        if($tables.length) {
            $tables.DataTable();
        }
    });
</script>
</body>
</html>
