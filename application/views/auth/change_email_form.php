<?php
/**
 * File - change_email_form.php
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
 
$password = array(
    'name'    => 'password',
    'id'    => 'password',
    'class' => 'form-control input-lg',
    'required' => 'true',
);
$email = array(
    'name'    => 'email',
    'id'    => 'email',
    'class' => 'form-control input-lg',
    'maxlength'    => 80,
    'required' => 'true',
    'placeholder' => 'new_email@example.com',
);
$submit = array(
    'name' => 'change_email',
    'value' => lang('user_change_email'),
    'class'=>'btn btn-primary btn-lg btn-block',
);
?>

<h1 class="page-header"><?php echo lang('user_change_email'); ?></h1>

<?php 
    echo form_open(
        $this->uri->uri_string(), 
        array('role'=>'form', 'class'=>'col-md-6 col-md-offset-3')
    ); 
?>

    <div class="form-group">
        <?php echo form_label(lang('user_old_email')); ?>
        <p class="form-control-static">
            <?php echo $this->session->userdata('email'); ?>
        </p>
    </div>
    
    <div class="form-group">
        <?php echo form_label(lang('user_password'), $password['id']); ?>
        <?php echo form_password($password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($password['name']); ?>
        <?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?>
    </div>
    
    <div class="form-group">
        <?php echo form_label(lang('user_new_email'), $email['id']); ?>
        <?php echo form_password($email); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($email['name']); ?>
        <?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?>
    </div>
    
    <hr />
    
    <?php echo form_submit($submit); ?>
<?php echo form_close(); ?>
<?php
/* End of file change_email_form.php */
/* Location: ./application/views/auth/change_email_form.php */
?>