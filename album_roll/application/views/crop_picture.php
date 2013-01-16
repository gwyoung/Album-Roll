<!DOCTYPE html>
<html>
	<div class="content_header">
		upload
	</div>
	<div class="list_items">
		<div class="list_item">
			<div class="instructions">
				You'll want to crop the image so it displays nicely on the site.
			</div>
			<div class="uploaded_image_div">
				<img src="<?php echo $upload_path; ?>" id="uploaded_image"><img>
			</div>
			<div class="instructions">
				This is how the image will appear:
			</div>
			<div class="crop_preview_div">
				<img src="<?php echo $upload_path; ?>" id="crop_preview"></img>
			</div>
			<div class="crop_image_form">
				<?php echo form_open($destination); ?>
				<input type="hidden" name="x" id="x" />
	            <input type="hidden" name="y" id="y" />
	            <input type="hidden" name="x2" id="x2" />
	            <input type="hidden" name="y2" id="y2" />
	            <input type="hidden" name="w" id="w" />
	            <input type="hidden" name="h" id="h" />
				<?php echo form_hidden('file_path', $file_path); ?>
				<?php echo form_hidden('upload_path', $upload_path); ?>
				<?php echo form_hidden('actual_width', $actual_width); ?>
				<?php if($errors) echo $errors; ?>
				<?php echo form_submit(array('id' => 'crop_submit', 'class' => 'form_submit', 
					'value' => 'crop')); ?>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	<script type="text/javascript" language="javascript">
	
		var img_width = $('#uploaded_image').width();
		var img_height = $('#uploaded_image').height();
		
		$('#uploaded_image').Jcrop({
			onSelect: show_preview,
			onChange: show_preview,
			aspectRatio: 1,
			setSelect: [0, 0, img_width, img_width]
		});
		
		function show_preview(coords)
		{
			img_width = $('#uploaded_image').width();
			img_height = $('#uploaded_image').height();
			
			var rx = 200 / coords.w;
			var ry = 200 / coords.h;
			
			var thumb_width = Math.round(rx * img_width);
			var thumb_height = Math.round(ry * img_height);
			var thumb_x = Math.round(rx * coords.x);
			var thumb_y = Math.round(ry * coords.y);
			
			$('#w').val(coords.w);
			$('#h').val(coords.h);
			$('#x').val(coords.x);
			$('#y').val(coords.y);
		
			$('#crop_preview').css({
				width: thumb_width + 'px',
				height: thumb_height + 'px',
				marginLeft: '-' + thumb_x + 'px',
				marginTop: '-' + thumb_y + 'px'
			});
		}
		
	</script>
</html>