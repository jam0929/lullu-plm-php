<?php
/**
 * File - change_password_form.php
 *
 * PHP Version 5.4
 *
 * @category  View
 * @package   JumpingNutsInc
 * @author    Hwan Oh <hwangoon@gmail.com>
 * @author    Jae Moon Kim <jam0929@gmail.com>
 * @copyright 2013-2014 Jumping Nuts Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://jumpingnuts.com
 */
 
$old_password = array(
    'name'    => 'old_password',
    'id'    => 'old_password',
    'value' => set_value('old_password'),
    'class' => 'form-control input-lg',
    'required' => 'true',
);
$new_password = array(
    'name'    => 'new_password',
    'id'    => 'new_password',
    'maxlength'    => $this->config->item('password_max_length', 'tank_auth'),
    'class' => 'form-control input-lg',
    'required' => 'true',
);
$confirm_new_password = array(
    'name'    => 'confirm_new_password',
    'id'    => 'confirm_new_password',
    'maxlength'    => $this->config->item('password_max_length', 'tank_auth'),
    'class' => 'form-control input-lg',
    'required' => 'true',
);
$submit = array(
    'name' => 'change_password',
    'value' => lang('user_change_password'),
    'class'=>'btn btn-primary btn-lg btn-block',
);
?>

<h1 class="page-header"><?php echo lang('user_change_password'); ?></h1>

<?php 
    echo form_open(
        $this->uri->uri_string(), 
        array('role'=>'form', 'class'=>'col-md-6 col-md-offset-3')
    ); 
?>

    <div class="form-group">
        <?php echo form_label(lang('user_old_password'), $old_password['id']); ?>
        <?php echo form_password($old_password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($old_password['name']); ?>
        <?php 
            echo isset($errors[$old_password['name']])
            ? $errors[$old_password['name']]
            : ''; 
        ?>
    </div>
    
    <div class="form-group">
        <?php echo form_label(lang('user_new_password'), $new_password['id']); ?>
        <?php echo form_password($new_password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($new_password['name']); ?>
        <?php 
            echo isset($errors[$new_password['name']])
            ? $errors[$new_password['name']]
            : ''; 
        ?>
    </div>
    
    <div class="form-group">
        <?php 
            echo form_label(
                lang('user_confirm_password'), $confirm_new_password['id']
            ); 
        ?>
        <?php echo form_password($confirm_new_password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($confirm_new_password['name']); ?>
        <?php 
            echo isset($errors[$confirm_new_password['name']])
            ? $errors[$confirm_new_password['name']]
            : ''; 
        ?>
    </div>
    
    <hr />
    
    <?php echo form_submit($submit); ?>
<?php echo form_close(); ?>
<?php
/* End of file change_password_form.php */
/* Location: ./application/views/auth/change_password_form.php */
?>