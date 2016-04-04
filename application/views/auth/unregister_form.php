<?php
/**
 * File - unregister_form.php
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
$submit = array(
    'name' => 'unregister',
    'value' => lang('user_unregister'),
    'class'=>'btn btn-danger btn-lg btn-block',
);
?>

<h1 class="page-header"><?php echo lang('user_unregister'); ?></h1>

<?php 
    echo form_open(
        $this->uri->uri_string(), 
        array('role'=>'form', 'class'=>'col-md-6 col-md-offset-3')
    ); 
?>
    <div class="form-group">
        <?php echo form_label(lang('user_password'), $password['id']); ?>
        <?php echo form_password($password); ?>
    </div>
    <div class="form-group text-danger">
        <?php echo form_error($password['name']); ?>
        <?php 
            echo isset($errors[$password['name']])
            ? $errors[$password['name']]
            : ''; 
        ?>
    </div>
    
    <hr />
    
    <?php echo form_submit($submit); ?>
<?php echo form_close(); ?>
<?php
/* End of file unregister_form.php */
/* Location: ./application/views/auth/unregister_form.php */
?>