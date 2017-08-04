<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @author rizky Kharisma <ngeng.ngengs@gmail.com>
 *
 * Date: 8/4/2017
 * Time: 2:34 AM
 *
 * Created by PhpStorm.
 */
?>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Pendaftar</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tipe</th>
                            <th>Waktu Daftar</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (!empty($users)) {
                            $i = 1;
                            foreach ($users as $key => $value) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $value->FULL_NAME; ?></td>
                                    <td><?php echo $value->EMAIL; ?></td>
                                    <td><?php echo ($value->TYPE == 1)?'Personal':'Bengkel'; ?></td>
                                    <td><?php echo date('Y-MM-d h:i', strtotime($value->DATE_CREATE)); ?></td>
                                    <td><a href="<?php echo base_url('admin/manage/user/detail/' . $value->ID); ?>"
                                           class="btn btn-warning btn-sm">
                                            <span class="fa fa-eye"></span> Detail</a>
                                    </td>
                                </tr>
                            <?php }
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>
