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
    'value' => lang('user_modify'),
    'class'=>'btn btn-primary btn-lg btn-block',
);
?>

<h1 class="page-header"><?php echo lang('user_modify'); ?></h1>

<?php 
    echo form_open(
        $this->uri->uri_string(), 
        array('role'=>'form', 'class'=>'col-md-6 col-md-offset-3')
    ); 
?>
    
    <?php if ($use_username) : ?>
    <div class="form-group">
        <?php echo form_label(lang('user_username')); ?>
        <p class="form-control-static">
            <?php echo $this->session->userdata('username'); ?>
        </p>
    </div>
    <?php 
endif; 
    ?>
    
    <div class="form-group">
        <?php echo form_label(lang('user_email')); ?>
        <p class="form-control-static">
            <?php echo $this->session->userdata('email'); ?>
        </p>
        <a href="<?php echo base_url('user/change-email'); ?>" 
            class="btn btn-info btn-block">
            <?php echo lang('user_change_email'); ?>
        </a>
    </div>
    
    <div class="form-group">
        <?php echo form_label(lang('user_password'), $password['id']); ?>
        <a href="<?php echo base_url('user/change-password'); ?>" 
            class="btn btn-info btn-block">
            <?php echo lang('user_change_password'); ?>
        </a>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($password['name']); ?>
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
    
    <a 
        href="<?php echo base_url('user/unregister'); ?>" 
        class="btn btn-block btn-danger">
        <?php echo lang('user_unregister'); ?>
    </a>
<?php echo form_close(); ?>
<?php
/* End of file modify_form.php */
/* Location: ./application/views/auth/modify_form.php */
?>