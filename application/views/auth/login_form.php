<?php
/**
 * File - login_form.php
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
if ($login_by_username AND $login_by_email) {
    $login_label = lang('user_email_or_username');
} else if ($login_by_username) {
    $login_label = lang('user_username');
} else {
    $login_label = lang('user_email');
}
$password = array(
    'name'    => 'password',
    'id'    => 'password',
    'class' => 'form-control input-lg',
    'placeholder' => '',
    'required' => 'true',
    'maxlength'    => 80,
);
$remember = array(
    'name'    => 'remember',
    'id'    => 'remember',
    'value'    => 1,
    'checked'    => set_value('remember'),
    'style' => 'margin:0;padding:0',
);
$captcha = array(
    'name'    => 'captcha',
    'id'    => 'captcha',
    'maxlength'    => 8,
);
$submit = array(
    'name'  => 'submit',
    'value' => lang('user_login'),
    'class' =>'btn btn-primary btn-lg btn-block',
);
?>

<h1 class="page-header"><?php echo lang('user_login'); ?></h1>
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
    <div class="form-group">
        <?php echo form_label(lang('user_password'), $password['id']); ?>
        <?php echo form_password($password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($password['name']); ?>
        <?php echo isset($errors[$password['name']])?$errors[$password['name']]:''; ?>
    </div>
    <div class="form-group">
        <?php echo form_checkbox($remember); ?>
        <?php echo form_label(lang('user_remember_me'), $remember['id']); ?>
    </div>
    
    <?php if ($show_captcha) : ?>
        <?php if ($use_recaptcha) : ?>
    <div id="recaptcha_image"></div>
    
    <ul class="list-inline">
        <li><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></li>
        <li class="recaptcha_only_if_image">
            <a href="javascript:Recaptcha.switch_type('audio')">
                Get an audio CAPTCHA
            </a>
        </li>
        <li class="recaptcha_only_if_audio">
            <a href="javascript:Recaptcha.switch_type('image')">
                Get an image CAPTCHA
            </a>
        </li>
    </ul>
    <div class="form-group">
        <label for="recaptcha_response_field" class="recaptcha_only_if_image">
            Enter the words above
        </label>
        <label for="recaptcha_response_field" class="recaptcha_only_if_audio">
            Enter the numbers you hear
        </label>
        <input 
            type="text" 
            class="form-control input-lg" 
            id="recaptcha_response_field" 
            name="recaptcha_response_field" 
        />
    </div>
    
    <div class="form-group text-danger">
        <?php echo form_error('recaptcha_response_field'); ?>
    </div>
    <?php echo $recaptcha_html; ?>
    
        <?php else: ?>
    <div class="form-group">
        <label for="">Enter the code exactly as it appears:</label>
        <?php echo $captcha_html; ?>
    </div>
    <div class="form-group">
        <?php echo form_label('Confirmation Code', $captcha['id']); ?>
        <?php echo form_input($captcha); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($captcha['name']); ?>
    </div>
        <?php 
endif; 
        ?>
    <?php 
endif; 
    ?>

    <hr />
    
    <?php echo form_submit($submit); ?>
    
    <ul class="list-unstyled list-inline text-right">
        <li>
            <?php 
                echo anchor(
                    '/user/forgot-password/', 
                    lang('user_forgot_password')
                ); 
            ?>
        </li>
        <li>
            <?php 
            if ($this->config->item('allow_registration', 'auth')) {
                echo anchor('/user/register/', lang('user_register')); 
            }
            ?>
        </li>
    </ul>
<?php echo form_close(); ?>
<?php
/* End of file login_form.php */
/* Location: ./application/views/auth/login_form.php */
?>