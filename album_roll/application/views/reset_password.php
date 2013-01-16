<!DOCTYPE html>
<html>
	<div class="content_header">
		reset password
	</div>
	<div class="list_items">
		<div class="list_item">
			<div class="instructions">
				Enter your email address and we'll send you mail with a shiny new temporary password.
			</div>
			<?php echo form_open('email/reset_password'); ?>
			<div class="form">
				<div class="form_name">
					Email:
				</div>
				<div class="form_field">
					<?php echo form_input(array('name' => 'email', 'size' => 30, 'maxlength' => 50,
						'value' => set_value('email'))); ?>
					<?php echo form_error('email', '<div id="email_error" class="error_text">', 
						'</div>'); ?>
				</div>
			</div>
			<?php echo form_submit(array('name' => 'submit', 'id' => 'form_submit', 
				'class' => 'form_submit', 'value' => 'do it')); ?>
			<?php echo form_close(); ?>
		</div>
	</div>
</html>