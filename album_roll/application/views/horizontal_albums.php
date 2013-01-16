<!DOCTYPE html>
<html>
	<div id="horizontal_content">
		<?php if(empty($albums)) { ?>
			<div class="instructions" style="width:420px">No albums here.</div>
		<?php } else if(!pageable($count)){?>
			<div class="instructions" style="width:420px">
				<?php foreach($albums as $album) { ?>
					<a href="<?php echo site_url('albums/album/'.$album->album_id); ?>">
						<img class='small_image_no_block' src="<?php echo $album->image_url(); ?>"
							alt="<?php echo $album; ?>" title="<?php echo $album; ?>" />
						<?php if($album != end($albums)) echo "&nbsp"; ?>
					</a>
				<?php } ?>
			</div>
		<?php } else { ?>
		<div class="back_button">
			<?php if(pageable($count) && back_available($offset)) { ?>
			<img id='horizontal_back_button' class='back_next_icon' src='<?php echo base_url('images/back_icon.jpg'); ?>'/> 
			<?php } ?>
		</div>
		<div class="horizontal_display">
			<?php foreach($albums as $album) { ?>
				<div class="horizontal_display_item">
					<a href="<?php echo site_url('albums/album/'.$album->album_id); ?>">
						<img class='small_image' src="<?php echo $album->image_url(); ?>"
							alt="<?php echo $album; ?>" title="<?php echo $album; ?>" />
					</a>
				</div>
			<?php } ?>
		</div>
		<div class="next_button">
			<?php if(pageable($count) && next_available($offset, $count)) { ?>
			<img id='horizontal_next_button' class='back_next_icon' src='<?php echo base_url('images/next_icon.jpg'); ?>'/> 
			<?php } ?>
		</div>
		<?php if(pageable($count)){ ?>
			<script type="text/javascript" language="javascript">
					
				$('#horizontal_back_button').click(function(){
					$.post("<?php echo $post_url; ?>",
						{'offset':<?php echo $offset - PAGED_LIST_SIZE; ?>, <?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#horizontal_content').replaceWith(result);
							}
						}
					)
				});
				
				$('#horizontal_next_button').click(function(){
					$.post("<?php echo $post_url; ?>",
						{'offset':<?php echo $offset + PAGED_LIST_SIZE; ?>, <?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#horizontal_content').replaceWith(result);
							}
						}
					)
				});
					
			</script>
		<?php } ?>
		<?php } ?>
	</div>
</html>