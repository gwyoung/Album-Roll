<!DOCTYPE html>
<html>
	<div class="content_header">
		give feedback
	</div>
	<div class="list_items">
		<div class="list_item">
			<div class="instructions">
				We're always looking for ways to improve the site for our users, so here's a chance to let us know if something went wrong or if you have ideas for what we could do better. Please be as specific as possible in your feedback description and include your email address just in case we need to follow up. Thanks for helping out!
			</div>
			<?php echo form_open('email/feedback'); ?>
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
					2. Description:
				</div>
				<div class="form_field">
					<?php echo form_textarea(array('name' => 'description', 'rows' => 8, 'cols' => 32, 
						'value' => set_value('description'))); ?>
					<?php echo form_error('description', '<div id="description_error" class="error_text">', 
						'</div>'); ?>
				</div>
			</div>
			<?php echo form_submit(array('name' => 'submit', 'id' => 'form_submit', 
				'class' => 'form_submit', 'value' => 'send it')); ?>
			<?php echo form_close(); ?>
		</div>
	</div>
</html>