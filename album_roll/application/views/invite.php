<!DOCTYPE html>
<html>
	<div class="content_header">
		invite
	</div>
	<div class="list_items">
		<div class="list_item">
		<?php if($this->session->userdata('user_id')){ ?>
			<div class="instructions">
				Sharing favorites is more fun with friends, especially if they have similar tastes! Enter a few email addresses and we'll tell your friends about the site. Thanks for sharing!
			</div>
			<?php echo form_open('email/invite', array('id' => 'invite_form')); ?>
				<div class="form">
					<div class="form_name">
						1. Email Addresses:
					</div>
					<div class="form_field">
						<?php echo form_input(array('name' => 'emails', 'size' => 30,
							'value' => set_value('emails'), 'class' => 'defaultText', 'title' => 
							'a@example.com, b@example.net')); ?>
						<?php echo form_error('emails', '<div id="emails_error" class="error_text">', 
							'</div>'); ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						2. Message:
					</div>
					<div class="form_field">
						<?php echo form_textarea(array('name' => 'message', 'rows' => 12, 'cols' => 32, 
							'value' => $message)); ?>
						<?php echo form_error('message', '<div id="message_error" class="error_text">', 
							'</div>'); ?>
					</div>
				</div>
			<?php echo form_submit(array('name' => 'submit', 'id' => 'form_submit', 
				'class' => 'form_submit', 'value' => 'invite')); ?>
			<?php echo form_close(); ?>
			<script type="text/javascript" language="javascript">
			
				$('#invite_form').submit(function()
				{
					$(".defaultText").each(function() {
						if($(this).val() == this.title) {
							$(this).val("");
						}
					});
				});
			
				//for default text in emails box
		
				$(".defaultText").focus(function(srcc)
			    {
			        if ($(this).val() == $(this)[0].title)
			        {
			            $(this).removeClass("defaultTextActive");
			            $(this).val("");
			        }
			    });
			    
			    $(".defaultText").blur(function()
			    {
			        if ($(this).val() == "")
			        {
			            $(this).addClass("defaultTextActive");
			            $(this).val($(this)[0].title);
			        }
			    });
			    
			    $(".defaultText").blur();
			
			</script>
		<?php } else { ?>
			<div class="instructions">
				You must be logged in to invite friends! Use the left sidebar to log in or create an account, then return to this page.
			</div>
		<?php } ?>
		</div>
	</div>
</html>