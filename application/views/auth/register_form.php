<?php
/**
 * File - modify_form.php
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
 
if ($use_username) {
    $username = array(
        'name'    => 'username',
        'id'    => 'username',
        'value' => set_value('username'),
        'maxlength'    => $this->config->item('username_max_length', 'tank_auth'),
        'class' => 'form-control input-lg',
        'placeholder' => '',
        'required' => 'true',
    );
}
$email = array(
    'name'    => 'email',
    'id'    => 'email',
    'value'    => set_value('email'),
    'maxlength'    => 80,
    'class' => 'form-control input-lg',
    'placeholder' => '',
    'required' => 'true',
);
$password = array(
    'name'    => 'password',
    'id'    => 'password',
    'value' => set_value('password'),
    'maxlength'    => $this->config->item('password_max_length', 'tank_auth'),
    'class' => 'form-control input-lg',
    'placeholder' => '',
    'required' => 'true',
);
$confirm_password = array(
    'name'    => 'confirm_password',
    'id'    => 'confirm_password',
    'value' => set_value('confirm_password'),
    'maxlength'    => $this->config->item('password_max_length', 'tank_auth'),
    'class' => 'form-control input-lg',
    'placeholder' => '',
    'required' => 'true',
);
$captcha = array(
    'name'    => 'captcha',
    'id'    => 'captcha',
    'maxlength'    => 8,
    'class' => 'form-control input-lg',
    'placeholder' => '',
    'required' => 'true',
);
$submit = array(
    'name' => 'register',
    'value' => lang('user_regist'),
    'class'=>'btn btn-primary btn-lg btn-block',
);
?>

<h1 class="page-header"><?php echo lang('user_regist'); ?></h1>

<?php 
    echo form_open(
        $this->uri->uri_string(), 
        array('role'=>'form', 'class'=>'col-md-6 col-md-offset-3')
    ); 
?>
    
    <?php if ($use_username) : ?>
    <div class="form-group">
        <?php echo form_label(lang('user_username'), $username['id']); ?>
        <?php echo form_input($username); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($username['name']); ?>
        <?php echo isset($errors[$username['name']])?$errors[$username['name']]:''; ?>
    </div>
    <?php 
endif; 
    ?>
    
    <div class="form-group">
        <?php echo form_label(lang('user_email'), $email['id']); ?>
        <?php echo form_input($email); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($email['name']); ?>
        <?php echo isset($errors[$email['name']])?$errors[$email['name']]:''; ?>
    </div>
    
    <div class="form-group">
        <?php echo form_label(lang('user_password'), $password['id']); ?>
        <?php echo form_password($password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($password['name']); ?>
    </div>
    
    <div class="form-group">
        <?php 
            echo form_label(
                lang('user_confirm_password'), 
                $confirm_password['id']
            ); 
        ?>
        <?php echo form_password($confirm_password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($confirm_password['name']); ?>
    </div>
    
    <?php if ($captcha_registration) : ?>
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
    
        <?php else : ?>
        
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
<?php echo form_close(); ?>
<?php
/* End of file register_form.php */
/* Location: ./application/views/auth/register_form.php */
?>