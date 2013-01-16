<!DOCTYPE html>
<html>
	<div class="content_header">
		sign up
	</div>
	<div class="list_items">
		<div class="list_item">
			<?php echo form_open('users/signup'); ?>
			<div class="form">
				<div class="form_name">
					1. Email:
				</div>
				<div class="form_field">
					<?php echo form_input(array('name' => 'email', 'size' => 30, 'maxlength' => 50,
						'value' => set_value('email'))); ?>
					<?php echo form_error('email', '<div id="email_error" class="error_text">', 
						'</div>'); ?>
				</div>
			</div>
			<div class="form">
				<div class="form_name">
					2. Name:
				</div>
				<div class="form_field">
					<?php echo form_input(array('name' => 'signup_name', 'size' => 30, 'maxlength' => 25,
						'value' => set_value('signup_name'))); ?>
					<?php echo form_error('signup_name', '<div id="name_error" class="error_text">', 
						'</div>'); ?>
				</div>
			</div>
			<div class="form">
				<div class="form_name">
					3. Password:
				</div>
				<div class="form_field">
					<?php echo form_password(array('name' => 'signup_password', 'size' => 30, 
						'maxlength' => 50)); ?>
					<?php echo form_error('signup_password', '<div id="password_error" class="error_text">',
						'</div>'); ?>
				</div>
			</div>
			<div class="form">
				<div class="form_name">
					4. Confirm Password:
				</div>
				<div class="form_field">
					<?php echo form_password(array('name' => 'confirm_password', 'size' => 30, 
						'maxlength' => 50)); ?>
					<?php echo form_error('confirm_password', '<div id="confirm_password_error" class="error_text">', '</div>'); ?>
				</div>
			</div>
			<div class="form">
				<div class="form_name">
					5. About You:
				</div>
				<div class="form_field">
					<?php echo form_textarea(array('name' => 'blurb', 'rows' => 5, 'cols' => 32, 
						'value' => $blurb_value)); ?>
					<?php echo form_error('blurb', '<div id="blurb_error" class="error_text">', 
						'</div>'); ?>
				</div>
			</div>
			<?php echo form_submit(array('name' => 'submit', 'id' => 'form_submit', 
				'class' => 'form_submit', 'value' => 'let\'s roll')); ?>
			<?php echo form_close(); ?>
		</div>
	</div>
</html>