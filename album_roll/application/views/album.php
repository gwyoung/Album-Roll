<!DOCTYPE html>
<html>
	<div class="metadata">
		<div class="metadata_main">
			<div class="large_image_div">
				<img class='large_image' src="<?php echo $album->image_url(); ?>"/>
			</div>
			<div class="metadata_content">
				<div class="metadata_name">
					<?php echo anchor('search/artist/'.$album->artist->artist_id, $album->artist->name); ?>
				</div>
				<div class="metadata_name"><?php echo $album->title; ?></div>
				<div id="star_rating"></div>
				<div id="average_rating">
					<?php echo $average_rating; ?> &nbsp&nbsp(<?php echo $total_votes; ?> vote<?php echo $total_votes == 1 ? '' : 's'; ?>)
				</div>
				<div class="metadata_line">
					Label: <?php echo anchor('search/label/'.$album->label->label_id, 
						$album->label->name); ?>
				</div>
				<div class="metadata_line">
					Release date: <?php echo date('F jS, ', $album->release_date).
						anchor('search/year/'.date('Y', $album->release_date), 
						date('Y', $album->release_date)); ?>
				</div>		
			</div>
		</div>
		<div class="metadata_list">
			<?php if($logged_in) echo '<div class="metadata_list_button"><img id="add_tag_button" src="'.base_url().'images/plus_icon.jpg" class="plus_minus_icon" /></div>'; ?>
			<div class="metadata_list_title">
				Tags:
			</div>
			<div class="metadata_list_content" id="album_tags_content">
				<?php echo $tags; ?>
			</div>
		</div>
		<div class="metadata_list_form" id="album_tag_options">
			<div class="list_form_content" id="album_tag_form">
				<form id="add_tag_form">
					Tag:
					<?php echo form_input(array('id' => 'tag_name', 'size' => 15, 'maxlength' => 40)); ?>
					<img id='submit_tag_button' src="<?php echo base_url().'images/plus_icon.jpg'; ?>"
						class='plus_minus_icon' />
				</form>
			</div>
		</div>
		<div class="metadata_list">
			<?php if($logged_in) echo '<div class="metadata_list_button"><img id="add_information_link_button" src="'.base_url().'images/plus_icon.jpg" class="plus_minus_icon" /></div>'; ?>
			<div class="metadata_list_title">
				Information:
			</div>
			<div class="metadata_list_content" id="information_links_content">
				<?php echo $information_links; ?>
			</div>
		</div>
		<div class="metadata_list_form" id="information_link_options">
			<div class="list_form_content" id="information_link_form">
				<form id="add_information_link_form">
					Title:
					<?php echo form_input(array('id' => 'information_link_title', 'size' => 8, 'maxlength' => 150));?>
					&nbsp&nbspUrl:
					<?php echo form_input(array('id' => 'information_link_url', 'size' => 15, 'maxlength' => 200)); ?>
					<img id='submit_information_link_button' src="<?php echo base_url().'images/plus_icon.jpg'; ?>"
						class='plus_minus_icon' />
				</form>
			</div>
		</div>
		<div class="metadata_list">
			<?php if($logged_in) echo '<div class="metadata_list_button"><img id="add_review_link_button" src="'.base_url().'images/plus_icon.jpg" class="plus_minus_icon" /></div>'; ?>
			<div class="metadata_list_title">
				Reviews:
			</div>
			<div class="metadata_list_content" id="review_links_content">
				<?php echo $reviews; ?>
			</div>
		</div>
		<div class="metadata_list_form" id="review_link_options">
			<div class="list_form_content" id="review_link_form">
				<form id="add_review_link_form">
					Title:
					<?php echo form_input(array('id' => 'review_link_title', 'size' => 8, 'maxlength' => 150));?>
					&nbsp&nbspUrl:
					<?php echo form_input(array('id' => 'review_link_url', 'size' => 15, 'maxlength' => 200)); ?>
					<img id='submit_review_link_button' src="<?php echo base_url().'images/plus_icon.jpg'; ?>" class='plus_minus_icon' />
				</form>
			</div>
		</div>
		<div class="metadata_list">
			<?php if($logged_in) echo '<div class="metadata_list_button"><img id="add_stream_link_button" src="'.base_url().'images/plus_icon.jpg" class="plus_minus_icon" /></div>'; ?>
			<div class="metadata_list_title">
				Streams:
			</div>
			<div class="metadata_list_content" id="stream_links_content">
				<?php echo $streams; ?>
			</div>
		</div>
		<div class="metadata_list_form" id="stream_link_options">
			<div class="list_form_content" id="stream_link_form">
				<form id="add_stream_link_form">
					Title:
					<?php echo form_input(array('id' => 'stream_link_title', 'size' => 8, 'maxlength' => 150));?>
					&nbsp&nbspUrl:
					<?php echo form_input(array('id' => 'stream_link_url', 'size' => 15, 'maxlength' => 200)); ?>
					<img id='submit_stream_link_button' src="<?php echo base_url().'images/plus_icon.jpg'; ?>" class='plus_minus_icon' />
				</form>
			</div>
		</div>
	</div>
	
	<div class="list_items">
		<div class="horizontal_box">
			<div class="horizontal_box_header">
				related
			</div>
			<?php echo $related_albums; ?>
		</div>
		<div class="sub_content_header">
			lists
		</div>
		<?php echo $lists; ?>
		<div class="sub_content_header">
			comments
		</div>
		<?php if($logged_in){ ?>
			<div class="list_item">
				<div><img id='add_comment_button' class='insert_icon' src="<?php echo base_url().'images/plus_icon.jpg'; ?>" /></div>
				<div style="display:none" id="add_album_comment">
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
				$.post("<?php echo site_url().'/albums/rate'; ?>",
				{'rating':score, 'album_id':<?php echo $album->album_id; ?>},
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
		
		<?php if($logged_in) { ?>
		
			$('#add_tag_button').click(function(){
				if($('#album_tag_options').is(':visible'))
				{
					$('#album_tag_options').hide();
					$('#add_tag_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#tag_name').val('');
					$('#tag_submit_error').replaceWith('');
				}
				else
				{
					$('#album_tag_options').show();
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
					$('#album_tag_form').after('<div id="tag_submit_error" class="error_text">Please enter a value.</div>');
					return false;
				}
				if(name.length > 40)
				{
					$('#album_tag_form').after('<div id="tag_submit_error" class="error_text">The tag name can\'t be more than 50 characters.</div>');
					return false;
				}
				
				$('#tag_name').val('');
				
			    $.post("<?php echo site_url().'/albums/add_tag'; ?>",
			      { 'tag_name':name, 'album_id':<?php echo $album->album_id; ?> },
			
			      function(result) {
			        $('#tag_submit_error').replaceWith('');
					if(result == '<?php echo SESSION_EXPIRED; ?>')
					{
						location.reload(true);
						return false;
					}
			        if(result)
					{
						$('#album_tags_content').html(result);
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
			
			$('#add_information_link_button').click(function(){
				if($('#information_link_options').is(':visible'))
				{
					$('#information_link_options').hide();
					$('#add_information_link_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#information_link_title').val('');
					$('#information_link_url').val('');
					$('#information_link_submit_error').replaceWith('');
				}
				else
				{
					$('#information_link_options').show();
					$('#add_information_link_button').attr("src","<?php echo base_url().'images/minus_icon.jpg'; ?>");
					$('#information_link_title').focus();
				}
			});
			
			$('#submit_information_link_button').click(save_information_link);
			$('#add_information_link_form').submit(function (event) {
				event.preventDefault();
				save_information_link();
			});
			$('#add_information_link_form').keypress(function(e){
		    	if(e.which == 13){
					e.preventDefault();
		        	$('#add_information_link_form').submit();
					return false;
		       	}
		    });
			
			function save_information_link(){
				var title = $('#information_link_title').val();
				var url = $('#information_link_url').val();
				$('#information_link_submit_error').replaceWith('');
				
				if(!title || !url)
				{
					$('#information_link_form').after('<div id="information_link_submit_error" class="error_text">Both fields are required.</div>');
					return false;
				}
				if(title.length > 150)
				{
					$('#information_link_form').after('<div id="information_link_submit_error" class="error_text">The title can\'t be more than 150 characters.</div>');
					return false;
				}
				if(url.length > 200)
				{
					$('#information_link_form').after('<div id="information_link_submit_error" class="error_text">The url can\'t be more than 200 characters.</div>');
					return false;
				}
			    
			    $.post("<?php echo site_url().'/albums/add_information_link'; ?>",
			      { 'link_title':title, 'link_url':url, 'album_id':<?php echo $album->album_id; ?> },
			
			      function(result) {
					if(result == '<?php echo SESSION_EXPIRED; ?>')
					{
						location.reload(true);
						return false;
					}
			        if(result == '0')
					{
						$('#information_link_form').after('<div id="information_link_submit_error" class="error_text">Invalid url.</div>');
					}
					else if(result)
					{
						$('#information_links_content').html(result);
						$('#add_information_link_button').click();
					}
			      }
			    );
			}
			
			$('#add_review_link_button').click(function(){
				if($('#review_link_options').is(':visible'))
				{
					$('#review_link_options').hide();
					$('#add_review_link_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#review_link_title').val('');
					$('#review_link_url').val('');
					$('#review_link_submit_error').replaceWith('');
				}
				else
				{
					$('#review_link_options').show();
					$('#add_review_link_button').attr("src","<?php echo base_url().'images/minus_icon.jpg'; ?>");
					$('#review_link_title').focus();
				}
			});
			
			$('#submit_review_link_button').click(save_review_link);
			$('#add_review_link_form').submit(function (event) {
				event.preventDefault();
				save_review_link();
			});
			$('#add_review_link_form').keypress(function(e){
		    	if(e.which == 13){
					e.preventDefault();
		        	$('#add_review_link_form').submit();
					return false;
		       	}
		    });
			
			function save_review_link(){
				var title = $('#review_link_title').val();
				var url = $('#review_link_url').val();
				$('#review_link_submit_error').replaceWith('');
				
				if(!title || !url)
				{
					$('#review_link_form').after('<div id="review_link_submit_error" class="error_text">Both fields are required.</div>');
					return false;
				}
				if(title.length > 150)
				{
					$('#review_link_form').after('<div id="review_link_submit_error" class="error_text">The title can\'t be more than 150 characters.</div>');
					return false;
				}
				if(url.length > 200)
				{
					$('#review_link_form').after('<div id="review_link_submit_error" class="error_text">The url can\'t be more than 200 characters.</div>');
					return false;
				}
			    
			    $.post("<?php echo site_url().'/albums/add_review_link'; ?>",
			      { 'link_title':title, 'link_url':url, 'album_id':<?php echo $album->album_id; ?> },
			
			      function(result) {
					if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
			        if(result == '0')
					{
						$('#review_link_form').after('<div id="review_link_submit_error" class="error_text">Invalid url.</div>');
					}
					else if(result)
					{
						$('#review_links_content').html(result);
						$('#add_review_link_button').click();
					}
			      }
			    );
			}
			
			$('#add_stream_link_button').click(function(){
				if($('#stream_link_options').is(':visible'))
				{
					$('#stream_link_options').hide();
					$('#add_stream_link_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#stream_link_title').val('');
					$('#stream_link_url').val('');
					$('#stream_link_submit_error').replaceWith('');
				}
				else
				{
					$('#stream_link_options').show();
					$('#add_stream_link_button').attr("src","<?php echo base_url().'images/minus_icon.jpg'; ?>");
					$('#stream_link_title').focus();
				}
			});
			
			$('#submit_stream_link_button').click(save_stream_link);
			$('#add_stream_link_form').submit(function (event) {
				event.preventDefault();
				save_stream_link();
			});
			$('#add_stream_link_form').keypress(function(e){
		    	if(e.which == 13){
					e.preventDefault();
		        	$('#add_stream_link_form').submit();
					return false;
		       	}
		    });
			
			function save_stream_link(){
				var title = $('#stream_link_title').val();
				var url = $('#stream_link_url').val();
				$('#stream_link_submit_error').replaceWith('');
				
				if(!title || !url)
				{
					$('#stream_link_form').after('<div id="stream_link_submit_error" class="error_text">Both fields are required.</div>');
					return false;
				}
				if(title.length > 150)
				{
					$('#stream_link_form').after('<div id="stream_link_submit_error" class="error_text">The title can\'t be more than 150 characters.</div>');
					return false;
				}
				if(url.length > 200)
				{
					$('#stream_link_form').after('<div id="stream_link_submit_error" class="error_text">The url can\'t be more than 200 characters.</div>');
					return false;
				}
			    
			    $.post("<?php echo site_url().'/albums/add_stream_link'; ?>",
			      { 'link_title':title, 'link_url':url, 'album_id':<?php echo $album->album_id; ?> },
			
			      function(result) {
					if(result == '<?php echo SESSION_EXPIRED; ?>')
						{
							location.reload(true);
							return false;
						}
			        if(result == '0')
					{
						$('#stream_link_form').after('<div id="stream_link_submit_error" class="error_text">Invalid url.</div>');
					}
					else if(result)
					{
						$('#stream_links_content').html(result);
						$('#add_stream_link_button').click();
					}
			      }
			    );
			}
			
			$('#add_comment_button').click(function(){
				if($('#add_album_comment').is(':visible'))
				{
					$('#add_album_comment').hide();
					$('#add_comment_button').attr("src","<?php echo base_url().'images/plus_icon.jpg'; ?>");
					$('#comment_text').val('');
				}
				else
				{
					$('#add_album_comment').show();
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
				$.post("<?php echo site_url('albums/add_comment'); ?>",
					{'text':text, 'album_id':<?php echo $album->album_id; ?>},
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
				$.post("<?php echo site_url('albums/remove_comment'); ?>",
					{'comment_id':comment_id, 'album_id':<?php echo $album->album_id; ?> },
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