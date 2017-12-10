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
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <?php
                                switch ($registered->STATUS) {
                                    case 1:
                                        echo "<span class='label label-success'>Aktif</span>";
                                        break;
                                    case 2:
                                        echo "<span class='label label-default'>Dalam Proses Verifikasi</span>";
                                        break;
                                    case 3:
                                        echo "<span class='label label-warning'>Pendaftaran Tertolak</span>";
                                        break;
                                    case 4:
                                        echo "<span class='label label-danger'>Sedang di Blokir</span>";
                                        break;
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Foto Diri</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <a href="<?php avatar_user_url($registered); ?>" target="_blank"
                                   data-fancybox="gallery" data-caption="Foto Diri: <?php echo $registered->FULL_NAME;
                                ?>">
                                    <img src="<?php avatar_user_url($registered); ?>" class="img-responsive img-rounded
                                img-bordered" style="max-width: 200px">
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Username</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $registered->USERNAME; ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $registered->EMAIL; ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Telepon</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $registered->PHONE; ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Jenis Kelamin</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo ($registered->GENDER == 1) ? 'Laki-laki' :
                                    'Perempuan';
                                ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Foto Kartu Identitas</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <a href="<?php identity_user_url($registered); ?>" target="_blank"
                                   data-fancybox="gallery" data-caption="Nama: <?php echo
                                $registered->FULL_NAME;
                                ?><br>Nomor Identitas: <?php echo $registered->IDENTITY_NUMBER; ?>">
                                    <img src="<?php identity_user_url($registered); ?>" class="img-responsive img-rounded
                                img-bordered" style="max-width: 200px">
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">No. Identitas</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $registered->IDENTITY_NUMBER; ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Waktu pendaftaran</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo date('Y-M-d H:i',
                                                                           strtotime($registered->DATE_CREATE)); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($last_update_by)) { ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Perubahan Terakhir Oleh</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo $last_update_by; ?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (!empty($registered->DATE_UPDATE)) { ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Waktu Perubahan</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo date('Y-M-d H:i',
                                                                               strtotime($registered->DATE_UPDATE));
                                    ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="box-footer text-right">
                    <?php if ($from_validation && ($registered->STATUS == 3)) { ?>
                        <a href="<?php echo base_url('admin/manage/register/validate/' . $registered->ID); ?>"
                           class="btn btn-warning">Perbaharui data</a>
                    <?php } ?>
                    <?php if ($from_validation && ($registered->STATUS == 2)) { ?>
                        <a href="#"
                           class="btn
                    btn-danger">Tolak</a>
                        <a href="<?php echo base_url('admin/manage/register/validate/' . $registered->ID); ?>"
                           class="btn btn-success">Validasi</a>
                    <?php } ?>
                    <?php if (!$from_validation && ($registered->STATUS == 1 || $registered->STATUS == 4)) { ?>
                        <a href="#" class="btn btn-danger">Hapus</a>
                    <?php } ?>
                    <?php if (!$from_validation && ($registered->STATUS == 4)) { ?>
                        <a href="<?php echo base_url('admin/manage/user/unbanned/' . $registered->ID); ?>" class="btn
                        btn-success">Cabut Blokir</a>
                    <?php } ?>
                    <?php if (!$from_validation && ($registered->STATUS == 1)) { ?>
                        <a href="<?php echo base_url('admin/manage/user/banned/' . $registered->ID); ?>" class="btn
                        btn-warning">Blokir</a>
                    <?php } ?>
                </div>
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>
