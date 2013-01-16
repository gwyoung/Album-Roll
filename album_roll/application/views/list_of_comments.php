<!DOCTYPE html>
<html>
	<div id="list_of_comments">
		<?php if (empty($comments)) { ?>
			<div class="list_item">
				<div class="instructions">No comments here.<?php if($user_id) echo ' Click + to comment!'?></div>
			</div>
		<?php } ?>	
		<?php foreach($comments as $comment){ ?>
			<div class="list_item">
				<div class="list_item_table">
					<div class="medium_image_div">
						<a href="<?php echo site_url('users/profile/'.$comment->user->user_id);?>">
							<img class="comment_image" src="<?php echo $comment->user->image_url(); ?>"/>
						</a>
					</div>
					<div class="list_item_metadata">
						<div class="list_item_blurb">
						<?php echo anchor('users/profile/'.$comment->user->user_id, $comment->user->name).' says ('.date('n/j/y g:iA', $comment->timestamp).'):'; ?>
						<?php if($can_remove || $user_id == $comment->user->user_id) echo '<img class="remove_comment_icon" src="'.base_url().'images/x_icon.jpg" alt='.$comment->comment_id.' />'; ?>
						</div>
						<div class="list_item_blurb"><?php echo $comment->text; ?></div>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if($pageable) { ?>
			<div class="page_navigation">
				<div class="back_button">
					<?php if(back_available($offset)) { ?>
					<img id='comment_back_button' class='back_next_icon' src='<?php echo base_url('images/back_icon.jpg'); ?>'/> 
					<?php } ?>
				</div>
				<div class="page_number"><?php echo current_page($offset); ?></div>
				<div class="next_button">
					<?php if(next_available($offset, $count)) { ?>
					<img id='comment_next_button' class='back_next_icon' src='<?php echo base_url('images/next_icon.jpg'); ?>'/> 
					<?php } ?>
				</div>
			</div>
			<script type="text/javascript" language="javascript">
				
				$('#comment_back_button').click(function(){
					$.post("<?php echo $post_url; ?>",
						{'offset':<?php echo $offset - PAGED_LIST_SIZE; ?>, <?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#list_of_comments').replaceWith(result);
							}
						}
					)
				});
				
				$('#comment_next_button').click(function(){
					$.post("<?php echo $post_url; ?>",
						{'offset':<?php echo $offset + PAGED_LIST_SIZE; ?>, <?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#list_of_comments').replaceWith(result);
							}
						}
					)
				});
				
			</script>
		<?php } ?>
	</div>
</html>