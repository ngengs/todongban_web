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
