<!DOCTYPE html>
<html>
	<div class="content_header">
		add album
	</div>
	<div class="list_items">
		<div class="list_item">
			<div class="instructions">Enter an artist and an album title to proceed.</div>
			<?php echo form_open_multipart('albums/create', array('id' => 'new_album_form')); ?>
			<div class="form">
				<div class="form_name">
					1. Artist:
				</div>
				<div class="form_field">
					<?php echo form_input(array('name' => 'artist', 'size' => 30, 'maxlength' => 50,
						'value' => set_value('artist'), 'id' => 'album_artist')); ?>
				</div>
			</div>
			<div class="form">
				<div class="form_name">
					2. Title:
				</div>
				<div class="form_field">
					<?php echo form_input(array('name' => 'title', 'size' => 30, 'maxlength' => 150,
						'value' => set_value('title'), 'id' => 'album_title')); ?>
					<?php if($title_error) echo $title_error; ?>
				</div>
			</div>
			<?php echo form_button(array('id' => 'expand_button', 'content' => 'search the web', 'class' =>
				'form_submit')); ?>
			<div <?php if(!$failed || $title_error) echo 'style="display:none"';?> id="details_div">
				<div class="instructions" id="details_instructions">Is this what you were looking for?</div>
				<img id="sample_image" src="<?php echo base_url('/images/default_album.jpg'); ?>"
					onerror="this.src = '<?php echo base_url('/images/default_album.jpg'); ?>'"></img>
				<div class="form">
					<div class="form_name">
						3. Cover Art URL:
					</div>
					<div class="form_field">
						<?php echo form_input(array('name' => 'art_url', 'size' => 30, 'maxlength' => 200,
							'value' => set_value('art_url'), 'id' => 'album_art_url')); ?>
						<?php if($art_url_error) echo $art_url_error; ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						&nbsp&nbsp&nbspor Upload:
					</div>
					<div class="form_field">
						<input class="file_form" name="album_art" id="album_art" type='file' size='30' />
						<?php if($errors) echo $errors; ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						4. Label:
					</div>
					<div class="form_field">
						<?php echo form_input(array('name' => 'label', 'size' => 30, 'maxlength' => 50,
							'value' => set_value('label'), 'id' => 'album_label')); ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						5. Release Date:
					</div>
					<div class="form_field">
						<?php echo form_input(array('name' => 'date', 'size' => 30, 'maxlength' => 50,
							'value' => set_value('date'), 'id' => 'album_date')); ?>
					</div>
				</div>
				<div class="form">
					<div class="form_name">
						6. Tags:
					</div>
					<div class="form_field">
						<?php echo form_input(array('name' => 'tags', 'size' => 30,
							'value' => set_value('tags'), 'id' => 'album_tags', 'class' => 'defaultText',
							'title' => 'tag, another tag, yet another tag')); ?>
					</div>
				</div>
				<?php echo form_submit(array('name' => 'submit', 'id' => 'form_submit', 
					'class' => 'form_submit', 'value' => 'looks good')); ?>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	
	<script type="text/javascript" language="javascript">
	
		$('#album_artist').autocomplete({
			minLength:2,
			source: function(request, response){
				$.ajax({
					url:"<?php echo site_url('autocomplete/artists');?>",
					dataType: 'json',
					type: 'POST',
					data: {term: $('#album_artist').val()},
					success: function(data){
						response(data);
					}
				});
			},
			selectFirst:true
		});
		
		$('#expand_button').click(get_album_data);
		//$('#album_artist').change(get_album_data);
		$('#album_artist').keydown(hide_details);
		$('#album_title').change(get_album_data);
		$('#album_title').keydown(hide_details);
		
		function get_album_data(){
			$('#artist_error').replaceWith('');
			$('#title_error').replaceWith('');
			var success = true;
			var artist = $('#album_artist').val().replace('&', 'and');
			var title = $('#album_title').val().replace('&', 'and');
			if(!artist)
			{
				$('#album_artist').after('<div id="artist_error" class="error_text">The artist field is required.</div>');
				success = false;
			}
			if(!title)
			{
				$('#album_title').after('<div id="title_error" class="error_text">The title field is required.</div>');
				success = false;
			}
			if(!success)
			{
				return false;
			}
			
			$('#album_date').val('');
			$('#album_label').val('');
			$('#album_art_url').val('');
			$('#album_art_url').change();
			
			var lastfm_url = "http://ws.audioscrobbler.com/2.0/?method=album.getinfo&api_key=<?php echo LASTFM_KEY; ?>&artist=" + artist + "&album=" + title + "&autocorrect=1";
			
			$('body').toggleClass('wait', true);
			
			$.ajax({
				type: 'GET',
				url:lastfm_url,
				dataType: 'xml',
				success: function(xml){
					var corrected_artist = $(xml).find('artist').eq(0).text();
					var corrected_title = $(xml).find('name').eq(0).text();
					var image_url;
					$(xml).find('image').each(function()
					{
						if($(this).attr('size') == 'extralarge')
						{
							image_url = $(this).text();
						}
					});
					$('#album_artist').val(corrected_artist);
					$('#album_title').val(corrected_title);
					$('#album_art_url').val(image_url);
					$('#album_art_url').change();
					
					var mbid = $(xml).find('mbid').eq(0).text();
					$.ajax({
						type: 'GET',
						url: "http://musicbrainz.org/ws/2/release/" + mbid + "?inc=labels",
						dataType: 'xml',
						success: function(xml){
							var label = '';
							var nonlatin = /[^\u0000-\u007f]/;
							$(xml).find('name').each(function()
							{
								if(!nonlatin.test($(this).text()) && label == '')
								{
									label = $(this).text();
								}
							});
							
							var release_date = $(xml).find('date').eq(0).text();
							var actual_date = new Date(release_date); // convert to actual date
							var new_date = new Date(actual_date.getFullYear(), actual_date.getMonth(), actual_date.getDate()+1); // create new increased date
							$('#album_date').datepicker("setDate", new_date);
							
							if(!label)
							{
								label = corrected_artist;
							}
							$('#album_label').val(label);
							$('#details_instructions').html('Is this what you were looking for?');
							$('#details_div').show();
							$('body').toggleClass('wait', false);
						},
						error: function(){
							$('#details_instructions').html('This album is missing some information. If you can, please fill it in below.');
							$('#details_div').show();
							$('body').toggleClass('wait', false);
						}
					});
				},
				error: function(){
					$('#details_instructions').html('We couldn\'t find that album on the web. Check your spelling and try again, or fill in the following details to continue.');
					$('#details_div').show();
					$('body').toggleClass('wait', false);
				}
			});
		}
		
		function hide_details()
		{
			$('#details_div').hide();
		}
		
		$('#album_art_url').change(function(){
			var url = $('#album_art_url').val();
			if(!url)
			{
				$('#sample_image').attr('src', "<?php echo base_url('/images/default_album.jpg'); ?>");
				return;
			}
			$('#sample_image').attr('src', $('#album_art_url').val());
		});
		
		$('#album_art_url').change();
		
		$('#album_label').autocomplete({
			minLength:2,
			source: function(request, response){
				$.ajax({
					url:"<?php echo site_url('autocomplete/labels');?>",
					dataType: 'json',
					type: 'POST',
					data: {term: $('#album_label').val()},
					success: function(data){
						response(data);
					}
				});
			},
			selectFirst:true
		});
	
		$('#album_date').datepicker({
			changeMonth:true,
			changeYear:true,
			yearRange:'1950:<?php echo date("Y") + 1; ?>'
		});
		
		$('#new_album_form').submit(function() {
			$('#artist_error').replaceWith('');
			$('#title_error').replaceWith('');
			$('#label_error').replaceWith('');
			$('#date_error').replaceWith('');
			$('#art_error').replaceWith('');
			
			var artist = $('#album_artist').val();
			var title = $('#album_title').val();
			var label = $('#album_label').val();
			var date = $('#album_date').val();
			var album_art = $('#album_art').val();
			var album_art_url = $('#album_art_url').val();			
			var success = true;
			
			if(!artist)
			{
				$('#album_artist').after('<div id="artist_error" class="error_text">The artist field is required.</div>');
				success = false;
			}
			if(artist.length > 100)
			{
				$('#album_artist').after('<div id="artist_error" class="error_text">The artist can\'t be more than 100 characters.</div>');
				success = false;
			}
			if(!title)
			{
				$('#album_title').after('<div id="title_error" class="error_text">The title field is required.</div>');
				success = false;
			}
			if(title.length > 150)
			{
				$('#album_title').after('<div id="title_error" class="error_text">The title can\'t be more than 150 characters.</div>');
				success = false;
			}
			if(!label)
			{
				$('#album_label').after('<div id="label_error" class="error_text">The label field is required.</div>');
				success = false;
			}
			if(label.length > 50)
			{
				$('#album_label').after('<div id="label_error" class="error_text">The label can\'t be more than 50 characters.</div>');
				success = false;
			}
			if(!date)
			{
				$('#album_date').after('<div id="date_error" class="error_text">The release date field is required.</div>');
				success = false;
			}
			
			var date_regex = /^((0?[1-9]|1[012])[- /.](0?[1-9]|[12][0-9]|3[01])[- /.](19|20)?[0-9]{2})*$/;
			
			if(!date_regex.test(date))
			{
				$('#album_date').after('<div id="date_error" class="error_text">Please use the mm/dd/yyyy format.</div>');
				success = false;
			}
	
			if(!album_art && !album_art_url)
			{
				$('#album_art').after('<div id="art_error" class="error_text">Please specify cover art for this album.</div>');
				success = false;
			}
			
			if(success)
			{
				$('body').toggleClass('wait', true);
				
				$(".defaultText").each(function() {
					if($(this).val() == this.title) {
						$(this).val("");
					}
				});
			}
			
			return success;
		});
		
		
		//for default text in tags box
		
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
</html>