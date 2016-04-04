<?php
/**
 * File - reset_password_form.php
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
 
$new_password = array(
    'name'    => 'new_password',
    'id'    => 'new_password',
    'maxlength'    => $this->config->item('password_max_length', 'tank_auth'),
    'size'    => 30,
);
$confirm_new_password = array(
    'name'    => 'confirm_new_password',
    'id'    => 'confirm_new_password',
    'maxlength'    => $this->config->item('password_max_length', 'tank_auth'),
    'size'     => 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<table>
    <tr>
        <td><?php echo form_label('New Password', $new_password['id']); ?></td>
        <td><?php echo form_password($new_password); ?></td>
        <td style="color: red;">
            <?php echo form_error($new_password['name']); ?>
            <?php 
                echo isset($errors[$new_password['name']])
                ? $errors[$new_password['name']]
                : ''; 
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php 
                echo form_label(
                    'Confirm New Password', 
                    $confirm_new_password['id']
                ); 
            ?>
        </td>
        <td><?php echo form_password($confirm_new_password); ?></td>
        <td style="color: red;">
            <?php echo form_error($confirm_new_password['name']); ?>
            <?php 
                echo isset($errors[$confirm_new_password['name']])
                    ? $errors[$confirm_new_password['name']]
                    : ''; 
            ?>
        </td>
    </tr>
</table>
<?php echo form_submit('change', 'Change Password'); ?>
<?php echo form_close(); ?>
<?php
/* End of file reset_password_form.php */
/* Location: ./application/views/auth/reset_password_form.php */
?>