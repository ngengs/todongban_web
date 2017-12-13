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
 * @var \User_data $registered
 * @var \Garage_data $registered_garage
 * @var boolean $from_validation
 */
?>
<div class="row">
    <div class="col-sm-12">
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Detail Pengguna</h3>
            </div>
            <div class="box-body">
                <div class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <?php
                                switch ($registered->STATUS) {
                                    case User_data::$STATUS_ACTIVE:
                                        echo "<span class='label label-success'>Aktif</span>";
                                        break;
                                    case User_data::$STATUS_NOT_ACTIVE:
                                        echo "<span class='label label-default'>Dalam Proses Verifikasi</span>";
                                        break;
                                    case User_data::$STATUS_REJECTED:
                                        echo "<span class='label label-warning'>Pendaftaran Tertolak</span>";
                                        break;
                                    case User_data::$STATUS_BANNED:
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
                            <p class="form-control-static"><?php echo ($registered->GENDER == User_data::$GENDER_MALE)
                                    ? 'Laki-laki' : 'Perempuan';
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
            </div>

            <?php if ($registered->TYPE == User_data::$TYPE_GARAGE) { ?>
        </div>
    </div>


    <div class="col-sm-12">
        <div class="box box-danger">
            <div class="box-header">
                <h3 class="box-title">Detail Bengkel</h3>
            </div>
            <div class="box-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Nama Bengkel</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $registered_garage->NAME; ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Jam Buka</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <?php echo date('H:i', strtotime($registered_garage->OPEN_HOUR)); ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Jam Tutup</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <?php echo date('H:i', strtotime($registered_garage->CLOSE_HOUR)); ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Alamat</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $registered_garage->ADDRESS; ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Lokasi</label>
                        <div class="col-sm-10">
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item"
                                        src="https://www.google.com/maps/embed/v1/place?key=<?php echo $this->config->item('maps_key',
                                                                                                                           'sensitive'); ?>
  &q=<?php echo $registered_garage->LATITUDE . ',' . $registered_garage->LONGITUDE; ?>"></iframe>
                            </div>
                        </div>
                    </div>
                    <?php if (!$from_validation
                              && ($registered->STATUS == User_data::$STATUS_ACTIVE
                                  || $registered->STATUS == User_data::$STATUS_BANNED)) { ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Kondisi</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><?php echo $registered_garage->is_force_close()
                                        ? "<span class='label label-danger'>Tutup Paksa</span>"
                                        : "<span class='label label-default'>Mengikuti Jadwal Buka</span>";
                                    ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <div class="box-footer text-right">
                <?php if ($from_validation && ($registered->STATUS == User_data::$STATUS_REJECTED)) { ?>
                    <a href="<?php echo base_url('admin/manage/register/validate/' . $registered->ID); ?>"
                       class="btn btn-warning">Perbaharui data</a>
                <?php } ?>
                <?php if ($from_validation && ($registered->STATUS == User_data::$STATUS_NOT_ACTIVE)) { ?>
                    <a href="#" class="btn btn-danger">Tolak</a>
                    <a href="<?php echo base_url('admin/manage/register/validate/' . $registered->ID); ?>"
                       class="btn btn-success">Validasi</a>
                <?php } ?>
                <?php if (!$from_validation
                          && ($registered->STATUS == User_data::$STATUS_ACTIVE
                              || $registered->STATUS == User_data::$STATUS_BANNED)) { ?>
                    <a href="#" class="btn btn-danger">Hapus</a>
                <?php } ?>
                <?php if (!$from_validation && ($registered->STATUS == User_data::$STATUS_BANNED)) { ?>
                    <a href="<?php echo base_url('admin/manage/user/unbanned/' . $registered->ID); ?>" class="btn
                        btn-success">Cabut Blokir</a>
                <?php } ?>
                <?php if (!$from_validation && ($registered->STATUS == User_data::$STATUS_ACTIVE)) { ?>
                    <a href="<?php echo base_url('admin/manage/user/banned/' . $registered->ID); ?>" class="btn
                        btn-warning">Blokir</a>
                <?php } ?>
            </div>
            <!-- /.box -->
        </div>
    </div>
</div>
