<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h2 class="mb-4"><?php echo _l('add_api_token'); ?></h2>
                        
                        <?php echo form_open('multi_pipeline/api/add_token', array('class' => 'form-horizontal')); ?>
                        
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label"><?php echo _l('token_name'); ?></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="expires_at" class="col-sm-2 control-label"><?php echo _l('expiration_date'); ?></label>
                            <div class="col-sm-10">
                                <!-- Campo de data com suporte para jQuery Datepicker -->
                                <input type="text" class="form-control datepicker" id="expires_at" name="expires_at" autocomplete="off">
                                <small class="form-text text-muted"><?php echo _l('leave_blank_for_no_expiration'); ?></small>
                            </div>
                        </div>

                        <?php echo form_hidden('user_id', get_staff_user_id()); ?>
                        
                        <button type="submit" class="btn btn-primary"><?php echo _l('add_token'); ?></button>
                        
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<!-- Script para ativar o Datepicker -->
<script>
    $(document).ready(function(){
        // Ativar o datepicker no campo de data
        $('#expires_at').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    });
</script>
