<!DOCTYPE html>
<html>
	<div class="metadata">
		<div class="metadata_main">
			<div id="list_title"><?php echo $list->title; ?>
			<?php if($title_editable) echo '<img id="edit_title_button" src="'.base_url().'images/pencil_icon.jpg" class="edit_icon" />'; ?>
			</div>
			<div id="list_title_edit" style="display: none">
				<?php echo form_open(); ?>
				<div id="title_edit_div">
				<?php echo form_input(array('name' => 'title', 'size' => 200, 'maxlength' => 150, 
					'id' => 'list_title_input', 'value' => $list->title)); ?>
				</div>
				<div class="title_edit_save"><img id='save_title_button' 
					src="<?php echo base_url().'images/save_icon.jpg'; ?>" class='save_icon' /></div>
				<?php echo form_close(); ?>
			</div>
			<div id="star_rating"></div>
			<div id="average_rating">
				<?php echo $average_rating; ?> &nbsp&nbsp(<?php echo $total_votes; ?> vote<?php echo $total_votes == 1 ? '' : 's'; ?>)
			</div>
			<div class="metadata_line">Created by: <?php echo anchor('users/profile/'.$list->user_id, 
				$user_name); ?></div>
			<div class="metadata_line">Created on: <?php echo date('F jS, Y', $list->created_time); ?></div>
			<div class="metadata_line">Total views: <?php echo $list->view_count; ?></div>
			<?php if($year_string){ ?>
				<div class="metadata_line"><?php echo $year_string; ?></div>
			<?php } ?>
			<div class="metadata_blurb">
				<div id="list_blurb">
					<div id="blurb_text"><?php echo $list->blurb; ?>
					<?php if($is_current_user) echo '<img id="edit_blurb_button" src="'.base_url().'images/pencil_icon.jpg" class="edit_icon" />'; ?>
					</div>
				</div>
				<div class="blurb_text_area" id="list_blurb_edit">
					<?php echo form_open(); ?>
					<?php echo form_textarea(array('name' => 'blurb', 'rows' => 5, 'cols' => 36, 
						'id' => 'list_blurb_text_area', 'value' => $list->blurb)); ?>
					<div><img id='save_blurb_button' 
						src="<?php echo base_url().'images/save_icon.jpg'; ?>" class='save_icon' /></div>
					<?php echo form_close(); ?>
				</div>
			</div>
			<?php if($title_editable) { ?>
				<div class="metadata_line">
					<img id="delete_list_button" src="<?php echo base_url('images/x_icon.jpg'); ?>" class="edit_icon" />
				</div>
				<div id="delete_link" class="metadata_line" style="display:none">
					<?php echo anchor('lists/delete/'.$list->list_id, 'Click here to delete this list'); ?>
				</div>
				<script type="text/javascript" language="javascript">
					
					$('#delete_list_button').click(function(){
						if($('#delete_link').is(':hidden'))
						{
							$('#delete_link').show();
							$('#delete_list_button').attr('src', "<?php echo base_url('images/minus_icon.jpg'); ?>");
						}
						else
						{
							$('#delete_link').hide();
							$('#delete_list_button').attr('src', "<?php echo base_url('images/x_icon.jpg'); ?>");
						}
					});
					
				</script>
			<?php } ?>
		</div>
		<div class="metadata_list">
			<?php if($is_current_user) echo '<div class="metadata_list_button"><img id="add_tag_button" src="'.base_url().'images/plus_icon.jpg" class="plus_minus_icon" /></div>'; ?>
			<div class="metadata_list_title">
				Tags:
			</div>
			<div class="metadata_list_content" id="list_tags_content">
				<?php echo $tags; ?>
			</div>
		</div>
		<div class="metadata_list_form" id="list_tag_options">
			<div class="list_form_content" id="list_tag_form">
				<form id="add_tag_form">
					Tag:
					<?php echo form_input(array('id' => 'tag_name', 'size' => 15, 'maxlength' => 40)); ?>
					<img id='submit_tag_button' src="<?php echo base_url().'images/plus_icon.jpg'; ?>"
						class='plus_minus_icon' />
				</form>
			</div>
		</div>
	</div>
	
	<div class="list_items">
		<?php echo $list_albums; ?>
		<?php if($is_current_user){ ?>
			<div class="list_item">
				<div><img id='add_album_button' class='insert_icon' src="<?php echo base_url().'images/plus_icon.jpg'; ?>" /></div>
				<div style="display:none" id="add_album_to_list">
					<div class="instructions">Search for an album:</div>
					<?php echo form_open('albums/create', array('id' => 'search_album_form')); ?>
					<?php echo form_hidden('list_id', $list->list_id); ?>
					<div class="sidebar_field">
						<?php echo form_input(array('name' => 'existing_album', 'id' => 
							'existing_album_form', 'size' => 50)); ?>
					</div>
					<?php echo form_close(); ?>
					<div class="instructions">Don't see what you're looking for?</div>
					<?php echo form_open('albums/create', array('id' => 'new_album_form')); ?>
					<?php echo form_hidden('list_id', $list->list_id); ?>
					<?php echo form_submit(array('name' => 'submit', 'class' => 'form_submit', 'value' =>
						'add new album', 'id' => 'new_album_button')); ?>
					<?php echo form_close(); ?>
				</div>
				<div style="display:none" id="max_capacity_message">
					<div class="instructions">Woah! This list is at maximum capacity (<?php echo MAX_LIST_ALBUMS; ?>). You'll have to remove an album before you can add another.</div>
				</div>
			</div>
		<?php } ?>
		<div class="sub_content_header">
			related
		</div>
		<?php echo $related_lists; ?>
		<div class="sub_content_header">
			comments
		</div>
		<?php if($logged_in){ ?>
			<div class="list_item">
				<div><img id='add_comment_button' class='insert_icon' src="<?php echo base_url().'images/plus_icon.jpg'; ?>" /></div>
				<div style="display:none" id="add_list_comment">
					<div class="instructions" style="margin-top:0px">
						<form id="comment_form">
						<?php echo form_textarea(array('name' => 'comment_text', 'id' => 'comment_text', 
							'rows' => 5, 'cols' => 60, 'class' => 'centered_textarea')); ?>
						<?php echo form_submit(array('name' => 'submit', 'id' => 'comment_submit', 
							'value' => 'comment', 'class' => 'form_submit')); ?>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php echo $comments; ?>
	</div>
	
	<script type="text/javascript" language="javascript">
	
		$('#star_rating').raty({
			half:false,
			score:<?php echo $average_rating; ?>,
			readOnly:<?php echo !$logged_in ? 'true' : 'false'; ?>,
			path:"<?php echo base_url().'images/'; ?>",
			hints:['hate', 'dislike', 'meh', 'like', 'love'],
			click: function(score, evt){
				$.post("<?php echo site_url().'/lists/rate'; ?>",
				{'rating':score, 'list_id':<?php echo $list->list_id; ?>},
				function(result){
					if(result == '<?php echo SESSION_EXPIRED; ?>')
					{
						location.reload(true);
						return false;
					}
					if(result)
					{
						var results = result.split(":");
						$('#star_rating').raty('score', results[0]);
						if(results[1] == 1)
						{
							$('#average_rating').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' vote)');
						}
						else
						{
							$('#average_rating').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' votes)');
						}
					}
				});
			}
		});
		
		<?php if($is_current_user) { ?>
		
			$('#edit_title_button').live('click', function(){
				if($('#list_title_edit').is(':hidden'))
				{
					$('#list_title').hide();
					$('#list_title_edit').show();
					$('#list_title_input').val($.trim($('#list_title').text()));
					$('#list_title_input').select();
				}
			});
			
			$('#save_title_button').click(function(){
				var title = jQuery.trim($('#list_title_input').val());
				$('#title_error').replaceWith('');
				if(!title)
				{
					$('#title_edit_div').after('<div id="title_error" class="error_text">The list must have a title.</div>');
					return false;
				}
				if(title.length > 150)
				{
					$('#title_edit_div').after('<div id="title_error" class="error_text">The title can\'t be more than 150 characters.</div>');
					return false;
				}
				$.post("<?php echo site_url().'/lists/edit_title'; ?>",
					{'title':title, 'list_id':<?php echo $list->list_id; ?>},
					function(result)
					{
						$('#title_error').replaceWith('');
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						if(result == "0")
						{
							$('#title_edit_div').after('<div id="title_error" class="error_text">Another of your lists already has that title.</div>');
							return false;
						}
						$('#list_title').show();
						$('#list_title_edit').hide();
						$('#list_title').html(result<?php if($title_editable) echo 
						' + \'<img id="edit_title_button" src="'.base_url().'images/pencil_icon.jpg" class="edit_icon" />\''; ?>);
					});
			});
		
			$('#edit_blurb_button').live('click', function(){
				if($('#list_blurb_edit').is(':hidden'))
				{
					$('#list_blurb').hide();
					$('#list_blurb_edit').show();
					$('#list_blurb_text_area').val($.trim($('#blurb_text').text()));
					$('#list_blurb_text_area').select();
				}
			});
			
			$('#save_blurb_button').click(function(){
				var blurb = jQuery.trim($('#list_blurb_text_area').val());
				$('#blurb_error').replaceWith('');
				if(blurb.length > 150)
				{
					$('#list_blurb_text_area').after('<div id="blurb_error" class="error_text">Keep it short. 150 characters max.</div>');
					return false;
				}
				$.post("<?php echo site_url().'/lists/edit_blurb'; ?>",
					{'blurb':blurb, 'list_id':<?php echo $list->list_id; ?>},
					function(result)
					{
						$('#blurb_error').replaceWith('');
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						$('#list_blurb').show();
						$('#list_blurb_edit').hide();
						$('#blurb_text').html(result<?php if($is_current_user) echo 
						' + \'<img id="edit_blurb_button" src="'.base_url().'images/pencil_icon.jpg" class="edit_icon" />\''; ?>);
					});
			});
			
			$('#add_tag_button').click(function(){
				if($('#list_tag_options').is(':visible'))
				{
					$('#list_tag_options').hide();
					$('#add_tag_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#tag_name').val('');
					$('#tag_submit_error').replaceWith('');
				}
				else
				{
					$('#list_tag_options').show();
					$('#add_tag_button').attr("src","<?php echo base_url().'images/minus_icon.jpg'; ?>");
					$('#tag_name').focus();
				}
			});
			
			$('#submit_tag_button').click(save_tag);
			$('#add_tag_form').submit(function (event) {
				event.preventDefault();
				save_tag();
			});
			
			function save_tag()
			{
				var name = $('#tag_name').val();
			    $('#tag_submit_error').replaceWith('');
				if(!name)
				{
					$('#list_tag_form').after('<div id="tag_submit_error" class="error_text">Please enter a value.</div>');
					return false;
				}
				if(name.length > 40)
				{
					$('#list_tag_form').after('<div id="tag_submit_error" class="error_text">The tag name can\'t be more than 50 characters.</div>');
					return false;
				}
				
				$('#tag_name').val('');
				
			    $.post("<?php echo site_url().'/lists/add_tag'; ?>",
			      { 'tag_name':name, 'list_id':<?php echo $list->list_id; ?> },
			
			      function(result) {
			        $('#tag_submit_error').replaceWith('');
					if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
			        if(result)
					{
						$('#list_tags_content').html(result);
						$('#tag_name').val('');
						$('#tag_name').select();
					}
			      }
			    );
			}
			
			$('#tag_name').autocomplete({
				minLength:2,
				source: function(request, response){
					$.ajax({
						url:"<?php echo site_url('autocomplete/tags');?>",
						dataType: 'json',
						type: 'POST',
						data: {term: $('#tag_name').val()},
						success: function(data){
							response(data);
						}
					});
				},
				select: function(event, ui){
                    	//here ui.item refers to the selected item.
					event.preventDefault();
                    $('#tag_name').val(ui.item.label);
					save_tag();
					return false;
                },
				selectFirst:true
			});
			
			$('.x_icon').live('click', function() {
				var tag_id = $(this).attr('alt');
				$.post("<?php echo site_url().'/lists/remove_tag'; ?>",
					{'tag_id':tag_id, 'list_id':<?php echo $list->list_id; ?> },
					function(result){
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						if(result)
						{
							$('#list_tags_content').html(result);
						}
					});
			});
			
			$('#add_album_button').click(function(){
				if(busy)
				{
					return false;
				}
				if($('#add_album_to_list').is(':visible') || $('#max_capacity_message').is(':visible'))
				{
					$('#add_album_to_list').hide();
					$('#max_capacity_message').hide();
					$('#add_album_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#existing_album_form').val('');
				}
				else
				{
					if($('#list_of_albums').children('div').size() >= <?php echo MAX_LIST_ALBUMS; ?>)
					{
						$('#max_capacity_message').show();
					}
					else
					{
						$('#add_album_to_list').show();
						$('#existing_album_form').focus();
					}
					$('#add_album_button').attr("src","<?php echo base_url().'images/minus_icon.jpg'; ?>");
				}
			});
			
			var busy = false;
			
			$('#existing_album_form').autocomplete({
				minLength:3,
				source: function(request, response){
					$.ajax({
						url:"<?php echo site_url('autocomplete/albums');?>",
						dataType: 'json',
						type: 'POST',
						data: {term: $('#existing_album_form').val()},
						success: function(data){
							if(data.length == 0)
							{
								data.push({
									value: 0,
									label: 'No results. Click here to add the album!'
								});
							}
							response(data);
						}
					});
				},
				select: function(event, ui){
					event.preventDefault();
					if(ui.item.value == 0)
					{
						$('#new_album_button').click();
						return false;
					}
					if(busy)
					{
						return false;
					}
					busy = true;
					$('body').toggleClass('wait', busy);
					$.post("<?php echo site_url('lists/add_album'); ?>",
						{'album_id':ui.item.value, 'list_id':<?php echo $list->list_id; ?>},
						function(result)
						{
							busy = false;
							$('body').toggleClass('wait', busy);
							if(result == '<?php echo SESSION_EXPIRED; ?>')
							{
								location.reload(true);
								return false;
							}
							if(result)
							{
								$('#add_album_button').click();
								$('#list_of_albums').replaceWith(result);
							}
						});
				},
				focus: function( event, ui ) {
			        $( ".project" ).val( ui.item.label );
			        return false;  
			    },
				selectFirst:true
			});
			
			$('#search_album_form').submit(function(event){
				event.preventDefault();
			});
			
			$('.remove_album_icon').live('click', function(){
				if(busy)
				{
					return false;
				}
				busy = true;
				$('body').toggleClass('wait', busy);
				var album_id = $(this).attr('alt');
				$.post("<?php echo site_url().'/lists/remove_album'; ?>",
					{'album_id':album_id, 'list_id':<?php echo $list->list_id; ?> },
					function(result){
						busy = false;
						$('body').toggleClass('wait', busy);
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						if(result)
						{
							$('#list_of_albums').replaceWith(result);
						}
					});
			});
			
		<?php } ?>
		
		<?php if($logged_in){ ?>
			
			$('#add_comment_button').click(function(){
				if($('#add_list_comment').is(':visible'))
				{
					$('#add_list_comment').hide();
					$('#add_comment_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#comment_text').val('');
				}
				else
				{
					$('#add_list_comment').show();
					$('#add_comment_button').attr("src","<?php echo base_url().'images/minus_icon.jpg'; ?>");
					$('#comment_text').focus();
				}
			});
			
			$('#comment_form').submit(function(event){
				event.preventDefault();
				$('#comment_error').replaceWith('');
				var text = $('#comment_text').val();
				if(!text)
				{
					$('#comment_text').after('<div id="comment_error" class="error_text">Enter some text.</div>');
					return false;
				}
				if(text.length > 300)
				{
					$('#comment_text').after('<div id="comment_error" class="error_text">Enter some text.</div>');
					return false;
				}
				$.post("<?php echo site_url('lists/add_comment'); ?>",
					{'text':text, 'list_id':<?php echo $list->list_id; ?>},
					function(result)
					{
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						if(result)
						{
							$('#list_of_comments').replaceWith(result);
							$('#add_comment_button').click();
						}
					});
			});
			
			$('.remove_comment_icon').live('click', function(){
				var comment_id = $(this).attr('alt');
				$.post("<?php echo site_url('lists/remove_comment'); ?>",
					{'comment_id':comment_id, 'list_id':<?php echo $list->list_id; ?> },
					function(result){
						if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
						if(result)
						{
							$('#list_of_comments').replaceWith(result);
						}
					});
			});
			
		<?php } ?>
	</script>
</html>