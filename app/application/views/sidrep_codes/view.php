<?php echo form_open("", array("id" => "sinader_code-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">
    
    
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('client'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->client_name;
            ?>
        </div>
    </div>

    <div id="proyectos_group">
        <div class="form-group">
            <label for="project" class="col-md-3"><?php echo lang('project'); ?></label>
            <div class="col-md-9">
                <?php echo $proyecto; ?>
            </div>
        </div>
    </div>
   
    <div class="form-group">
        <label for="name" class="col-md-3"><?php echo lang('category'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->category_name;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="code_list_a" class="col-md-3"><?php echo lang('code_list_a'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->code_list_a ? $model->code_list_a : "-";
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="code_lists_i_ii_iii" class="col-md-3"><?php echo lang('code_lists_i_ii_iii'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->code_lists_i_ii_iii ? $model->code_lists_i_ii_iii : "-";
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="dangerous_characteristic" class="col-md-3"><?php echo lang('dangerous_characteristic'); ?></label>
        <div class="col-md-9">
            <?php
            echo $html_dangerous_characteristic;
            ?>
        </div>
    </div>

    <div class="form-group">
        <label for="physical_status" class="col-md-3"><?php echo lang('physical_status'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->physical_status ? lang($model->physical_status) : "-";
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="created_date" class="col-md-3"><?php echo lang('created_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo $model->created;
            ?>
        </div>
    </div>
    
    <div class="form-group">
        <label for="modified_date" class="col-md-3"><?php echo lang('modified_date'); ?></label>
        <div class="col-md-9">
            <?php
            echo ($model->modified)?$model->modified:'-';
            ?>
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> <?php echo lang('close'); ?></button>
</div>


<script type="text/javascript">
    $(document).ready(function() {
		
        
    });
</script>    