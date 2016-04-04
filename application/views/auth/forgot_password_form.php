<?php
/**
 * File - forgot_password_form.php
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
 
$login = array(
    'name'    => 'login',
    'id'    => 'login',
    'value' => set_value('login'),
    'class' => 'form-control input-lg',
    'placeholder' => '',
    'required' => 'true',
    'maxlength'    => 80,
);
if ($this->config->item('use_username', 'tank_auth')) {
    $login_label = lang('user_email_or_username');
} else {
    $login_label = lang('user_email');
}
$submit = array(
    'name' => 'reset',
    'value' => lang('user_forgot_password'),
    'class'=>'btn btn-primary btn-lg btn-block',
);
?>

<h1 class="page-header"><?php echo lang('user_forgot_password'); ?></h1>

<?php 
    echo form_open(
        $this->uri->uri_string(), 
        array('role'=>'form', 'class'=>'col-md-6 col-md-offset-3')
    ); 
?>
    <div class="form-group">
        <?php echo form_label($login_label, $login['id']); ?>
        <?php echo form_input($login); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($login['name']); ?>
        <?php echo isset($errors[$login['name']])?$errors[$login['name']]:''; ?>
    </div>
    
    <hr />
    
    <?php echo form_submit($submit); ?>
<?php echo form_close(); ?>
<?php
/* End of file forgot_password_form.php */
/* Location: ./application/views/auth/forgot_password_form.php */
?>