<!DOCTYPE html>
<html>
	<div class="metadata">
		<div class="metadata_main">
			<div class="large_image_div">
				<img class='large_image' src="<?php echo $user->image_url(); ?>"/>
			</div>
			<div class="metadata_content">
				<div class="metadata_name"><?php echo $user->name; ?></div>
				<div class="metadata_line">
					Member since: <?php echo date('F jS, Y', $user->member_since); ?>
				</div>
				<div class="metadata_line">
					Last seen: <?php echo date('F jS, Y', $user->last_seen); ?>
				</div>
				<div class="metadata_blurb">
					<div id="user_blurb">
						<div id="blurb_text"><?php echo $user->blurb; ?>
						<?php if($is_current_user) echo 
						'<img id="edit_blurb_button" src="'.base_url().'images/pencil_icon.jpg" class="edit_icon" />'; ?>
						</div>
					</div>
					<div class="blurb_text_area" id="user_blurb_edit">
						<?php echo form_open(); ?>
						<?php echo form_textarea(array('name' => 'blurb', 'rows' => 5, 'cols' => 32, 
							'id' => 'user_blurb_text_area', 'value' => $user->blurb)); ?>
						<div><img id='save_blurb_button' src="<?php echo base_url().'images/save_icon.jpg'; ?>" class='save_icon' /></div>
						<?php echo form_close(); ?>
					</div>
				</div>	
			</div>
		</div>
		<div class="metadata_list">
			<?php if($is_current_user) echo '<div class="metadata_list_button"><img id="add_link_button" src="'.base_url().'images/plus_icon.jpg" class="plus_minus_icon" /></div>'; ?>
			<div class="metadata_list_title">
				Links:
			</div>
			<div class="metadata_list_content" id="user_links_content">
				<?php echo $links; ?>
			</div>
		</div>
		<div class="metadata_list_form" id="profile_link_options">
			<div class="list_form_content" id="profile_link_form">
				<form id="add_link_form">
					Title:
					<?php echo form_input(array('id' => 'link_title', 'size' => 8, 'maxlength' => 150));?>
					&nbsp&nbspUrl:
					<?php echo form_input(array('id' => 'link_url', 'size' => 15, 'maxlength' => 200)); ?>
					<img id='submit_link_button' src="<?php echo base_url().'images/plus_icon.jpg'; ?>"
						class='plus_minus_icon' />
				</form>
			</div>
		</div>
	</div>
	
	<div class="list_items">
		<div class="horizontal_box">
			<div class="horizontal_box_header"><?php echo anchor('lists/roll/'.$current_rotation_id, 
				'current rotation'); ?></div>
			<?php echo $current_rotation; ?>
		</div>
		
		<div class="sub_content_header">
			<?php echo anchor('lists/roll/'.$favorites_id, 'favorites'); ?>
		</div>
		
		<?php echo $favorites; ?>
		
		<div class="sub_content_header">
			<?php echo anchor('lists/user/'.$user->user_id, 'themed lists'); ?>
		</div>
		
		<?php echo $user_lists; ?>
	</div>
	
	<script type="text/javascript" language="javascript">
	
		<?php if($is_current_user) { ?>
			$('#edit_blurb_button').live('click', function(){
				if($('#user_blurb_edit').is(':hidden'))
				{
					$('#user_blurb').hide();
					$('#user_blurb_edit').show();
					$('#user_blurb_text_area').val($.trim($('#blurb_text').text()));
					$('#user_blurb_text_area').select();
				}
			});
			
			$('#save_blurb_button').click(function(){
				var blurb = jQuery.trim($('#user_blurb_text_area').val());
				$('#blurb_error').replaceWith('');
				if(!blurb)
				{
					$('#user_blurb_text_area').after('<div id="blurb_error" class="error_text">Your blurb cannot be empty.</div>');
					return false;
				}
				if(blurb.length > 150)
				{
					$('#user_blurb_text_area').after('<div id="blurb_error" class="error_text">Keep it short. 150 characters max.</div>');
					return false;
				}
				$.post("<?php echo site_url().'/users/edit_blurb'; ?>",
					{'blurb':blurb},
					function(result)
					{
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						if(result){
							$('#user_blurb').show();
							$('#user_blurb_edit').hide();
							$('#blurb_text').html(result<?php if($is_current_user) echo 
							' + \'<img id="edit_blurb_button" src="'.base_url().'images/pencil_icon.jpg" class="edit_icon" />\''; ?>);
						}
					});
			});
			
			$('#add_link_button').click(function(){
				if($('#profile_link_options').is(':visible'))
				{
					$('#profile_link_options').hide();
					$('#add_link_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#link_title').val('');
					$('#link_url').val('');
					$('#link_submit_error').replaceWith('');
				}
				else
				{
					$('#profile_link_options').show();
					$('#add_link_button').attr("src","<?php echo base_url().'images/minus_icon.jpg'; ?>");
					$('#link_title').focus();
				}
			});
			
			$('#submit_link_button').click(save_link);
			$('#add_link_form').submit(function (event) {
				event.preventDefault();
				save_link();
			});
			$('#add_link_form').keypress(function(e){
		    	if(e.which == 13){
					e.preventDefault();
		        	$('#add_link_form').submit();
					return false;
		       	}
		    });
			
			function save_link(){
				var title = $('#link_title').val();
				var url = $('#link_url').val();
				$('#link_submit_error').replaceWith('');
				
				if(!title || !url)
				{
					$('#profile_link_form').after('<div id="link_submit_error" class="error_text">Both fields are required.</div>');
					return false;
				}
				if(title.length > 150)
				{
					$('#profile_link_form').after('<div id="link_submit_error" class="error_text">The title can\'t be more than 150 characters.</div>');
					return false;
				}
				if(url.length > 200)
				{
					$('#profile_link_form').after('<div id="link_submit_error" class="error_text">The url can\'t be more than 200 characters.</div>');
					return false;
				}
			    
			    $.post("<?php echo site_url().'/users/add_link'; ?>",
			      { 'link_title':title, 'link_url':url },
			
			      function(result) {
					if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
			        if(result == '0')
					{
						$('#profile_link_form').after('<div id="link_submit_error" class="error_text">Invalid url.</div>');
					}
					else if(result)
					{
						$('#user_links_content').html(result);
						$('#add_link_button').click();
					}
			      }
			    );
			}
			
			$('.x_icon').live('click', function() {
				var link_url = $(this).attr('alt');
				$.post("<?php echo site_url().'/users/remove_link'; ?>",
					{'link_url':link_url},
					function(result){
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						if(result)
						{
							$('#user_links_content').html(result);
						}
					});
			});
		<?php } ?>
	</script>
</html>