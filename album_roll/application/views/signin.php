<!DOCTYPE html>
<html>
	<div class="sidebar_header_text">sign in</div>
	<?php echo form_open('users/signin', array('id' => 'signin_form')); ?>
	<div class="sidebar_text">1. Name:</div>
	<div class="sidebar_field">
		<?php echo form_input(array('name' => 'name', 'id' => 'name_field', 'size' => 15, 'maxlength' => 25,
					'value' => set_value('name'))); ?>
	</div>
	<div class="sidebar_text">2. Password:</div>
	<div class="sidebar_field">
		<?php echo form_password(array('name' => 'password', 'id' => 'password_field', 'size' => 15, 
			'maxlength' => 25)); ?>
	</div>
	<div class="sidebar_field">
		<input type="checkbox" checked="TRUE" name="remember_me" id="remember_me_box"></input>
		remember me
	</div>
		<?php echo form_submit(array('name' => 'submit', 'id' => 'signin_submit', 'class' => 'form_submit',
			'value' => 'let\'s roll')); ?>
	<div class="sidebar_link">
		<?php echo anchor('users/signup', 'no account?'); ?>
	</div>
	<?php echo form_close(); ?>
	<script type="text/javascript" language="javascript">
	
		$('#signin_form').submit(function (event){
			event.preventDefault();
			
			$('#signin_error').replaceWith('');
			$('#reset_password').replaceWith('');
			
			var name = $('#name_field').val();
			var password = $('#password_field').val();
			var remember_me = $('#remember_me_box').is(':checked') ? 'true' : '';
			
			
			$.post("<?php echo site_url().'/users/signin'; ?>",
				{'password':password, 'name':name, 'remember_me':remember_me},
				function(result)
				{
					if(!result)
					{
						$('#password_field').after('<div id="signin_error" class="error_text">Mismatch.</div><div id="reset_password" class="error_text"><?php echo anchor("email/reset_password", "Reset password?"); ?></div>');
					}
					else
					{
						location.reload();
					}
				});
		});
		
	</script>
</html>