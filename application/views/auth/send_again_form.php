<?php
/**
 * File - send_again_form.php
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
 
$email = array(
    'name'    => 'email',
    'id'    => 'email',
    'value'    => set_value('email'),
    'maxlength'    => 80,
    'size'    => 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<table>
    <tr>
        <td><?php echo form_label('Email Address', $email['id']); ?></td>
        <td><?php echo form_input($email); ?></td>
        <td style="color: red;">
            <?php echo form_error($email['name']); ?>
            <?php 
                echo isset($errors[$email['name']])
                ? $errors[$email['name']]
                : ''; 
            ?>
        </td>
    </tr>
</table>
<?php echo form_submit('send', 'Send'); ?>
<?php echo form_close(); ?>
<?php
/* End of file send_again_form.php */
/* Location: ./application/controllers/send_again_form.php */
?>