<!DOCTYPE html>
<html>
	<div class="content_header">
		options
	</div>
	<div class="list_items">
		<div class="list_item">
			<div class="sub_header"><a id="change_picture_header">1. Change Profile Picture</a></div>
			<div <?php if(!$errors) echo 'class="sub_form"'; ?> id="change_picture_form">
				<?php echo form_open_multipart('users/change_picture', array('id' => 'change_picture_form')); ?>
				<div class="form">
					<div class="form_name">
						Image URL:
					</div>
					<div class="form_field">
						<?php echo form_input(array('name' => 'image_url', 'size' => 30, 'value' => 
							set_value('image_url'), 'id' => 'image_url', 'maxlength' => 200)); ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						or Upload:
					</div>
					<div class="form_field">
						<input class="file_form" name="user_photo" id="user_photo" type='file'/>
						<?php if($errors) echo $errors; ?>
					</div>
				</div>
				<?php echo form_submit(array('id' => 'change_picture_submit', 'class' => 'form_submit',
					'value' => 'upload')); ?>
			<?php echo form_close(); ?>
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header"><a id="change_name_header">2. Change Name</a></div>
			<div class="sub_form" id="change_name_form">
				<?php echo form_open(); ?>
				<div class="form">
					<div class="form_name">
						New Name:
					</div>
					<div class="form_field">
						<?php echo form_input(array('id' => 'new_name', 'size' => 30, 
							'maxlength' => 50)); ?>
					</div>
				</div>
				<?php echo form_submit(array('id' => 'change_name_submit', 'class' => 'form_submit',
					'value' => 'change it up')); ?>
				<?php echo form_close(); ?>
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header"><a id="change_password_header">3. Change Password</a></div>
			<div class="sub_form" id="change_password_form">
				<?php echo form_open(); ?>
				<div class="form">
					<div class="form_name">
						1. Old Password:
					</div>
					<div class="form_field">
						<?php echo form_password(array('id' => 'old_password', 'size' => 30, 
							'maxlength' => 50)); ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						2. New Password:
					</div>
					<div class="form_field">
						<?php echo form_password(array('id' => 'new_password', 'size' => 30, 
							'maxlength' => 50)); ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						3. Confirm Password:
					</div>
					<div class="form_field">
						<?php echo form_password(array('id' => 'confirm_password', 'size' => 30, 
							'maxlength' => 50)); ?>
					</div>
				</div>
				<?php echo form_submit(array('id' => 'change_password_submit', 'class' => 'form_submit',
					'value' => 'change it up')); ?>
				<?php echo form_close(); ?>
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header"><a id="delete_account_header">4. Delete Account</a></div>
			<div class="sub_form" id="confirm_delete_text">
				<?php echo anchor('users/delete', 'Click here if you\'re absolutely sure.');?>
			</div>
		</div>
	</div>
	<script type="text/javascript" language="javascript">
	
		$('#change_picture_header').click(function(event){
			event.preventDefault();
			if($('#change_picture_form').is(':hidden'))
			{
				$('#change_picture_form').show();
				$('#image_url').focus();
			}
			else
			{
				$('#change_picture_form').hide();
			}
			$('#user_photo').val('');
		});
		
		$('#change_picture_form').submit(function(event){
			$('#upload_error').replaceWith('');
			if(!$('#image_url').val() && !$('#user_photo').val())
			{
				$('#user_photo').after('<div id="upload_error" class="error_text">Please specify a new image.</div>');
				return false;
			}
			$('body').toggleClass('wait', true);
		});
		
		$('#change_name_header').click(function(event){
			event.preventDefault();
			if($('#change_name_form').is(':hidden'))
			{
				$('#change_name_form').show();
				$('#new_name').focus();
			}
			else
			{
				$('#change_name_form').hide();
			}
			$('#new_name').val('');
			$('#change_name_error').replaceWith('');
		});
		
		$('#change_name_form').submit(function(event){
			event.preventDefault();
			var new_name = $('#new_name').val();
			$.post("<?php echo site_url().'/users/change_name'; ?>",
				{'new_name':new_name},
				function(result){
					$('#change_name_error').replaceWith('');
					if(result == '<?php echo SESSION_EXPIRED; ?>')
					{
						location.reload(true);
						return false;
					}
					if(result)
					{
						$('#new_name').after(result);
					}
					else
					{
						window.location = "<?php echo site_url().'/users/profile'; ?>";
					}
				});
		});
		
		$('#change_password_header').click(function(event){
			event.preventDefault();
			if($('#change_password_form').is(':hidden'))
			{
				$('#change_password_form').show();
				$('#old_password').focus();
			}
			else
			{
				$('#change_password_form').hide();
			}
			$('#old_password').val('');
			$('#new_password').val('');
			$('#confirm_password').val('');
			$('#change_password_error').replaceWith('');
		});
		
		$('#change_password_form').submit(function(event){
			event.preventDefault();
			var old_password = $('#old_password').val();
			var new_password = $('#new_password').val();
			var confirm_password = $('#confirm_password').val();
			$.post("<?php echo site_url().'/users/change_password'; ?>",
				{'old_password':old_password, 'new_password':new_password, 
					'confirm_password':confirm_password},
				function(result){
					if(result == '<?php echo SESSION_EXPIRED; ?>')
					{
						location.reload(true);
						return false;
					}
					$('#change_password_error').replaceWith('');
					$('#new_password').val('');
					$('#confirm_password').val('');
					$('#confirm_password').after(result);
				});
		});
		
		$('#delete_account_header').click(function(event){
			event.preventDefault();
			if($('#confirm_delete_text').is(':hidden'))
			{
				$('#confirm_delete_text').show();
			}
			else
			{
				$('#confirm_delete_text').hide();
			}
		});
		
	</script>
</html>