<!DOCTYPE html>
<html>
	<div id="list_of_albums">
		<?php if (empty($albums)) { ?>
			<div class="list_item">
				<div class="instructions">No albums here.<?php if($can_reorder) echo ' Click + to add some!'?></div>
			</div>
		<?php } else if($sortable) { ?>
			<div class="sort_bar">
				<div class="sort_text">order by:</div>
				<div class="sort_options">
					<?php echo form_open(); ?>
					<?php echo form_dropdown('album_direction', array('asc' => 'ascending', 'desc' =>
						'descending'), $direction, 'id="album_direction"'); ?>
					<?php echo form_dropdown('album_order_by', Album::displayed_sort_options(), $order_by, 
						'id="album_order_by"'); ?>
					<?php echo form_close(); ?>
				</div>
			</div>
			<script type="text/javascript" language="javascript">
			
				function refresh_sorting()
				{
					var direction = $('#album_direction').val();
					var order_by = $('#album_order_by').val();
					$.post("<?php echo $post_url; ?>",
						{'offset':0, 'direction':direction, 'order_by':order_by, 
							<?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#list_of_albums').replaceWith(result);
							}
						}
					);
				}
			
				$('#album_direction').change(refresh_sorting);
				
				$('#album_order_by').change(refresh_sorting);
			
			</script>
		<?php } ?>
		<?php for($i = 0; $i < count($albums); $i++){ ?>
			<div class="album_list_item" <?php if($can_reorder) echo 'style="cursor:move" '; ?>>
				<div class="album_list_item_table">
					<div class="ordinal">
						<?php if($pageable) echo ($i + 1 + $offset); else echo ($i + 1); echo '.'; ?>
						<?php if($can_remove) echo '<img class="remove_album_icon" src="'.base_url().'images/x_icon.jpg" alt='.$albums[$i]->album_id.' />'; ?>
					</div>
					<div class="medium_image_div">
						<a href="<?php echo site_url('albums/album/'.$albums[$i]->album_id); ?>">
							<img class="medium_image" src="<?php echo $albums[$i]->image_url(); ?>"/>
						</a>
					</div>
					<div class="album_list_item_metadata">
						<div class="list_item_title">
							<?php echo anchor('search/artist/'.$albums[$i]->artist->artist_id, 
								$albums[$i]->artist->name); ?>
							&nbsp-&nbsp
							<?php echo anchor('albums/album/'.$albums[$i]->album_id, $albums[$i]->title); ?>
						</div>
						<div class="star_rating" id="star_rating_<?php echo "album_".$i; ?>"></div>
						<div id="average_rating_<?php echo "album_".$i; ?>" class="average_rating">
							<?php echo $albums[$i]->rating; ?> &nbsp&nbsp(<?php echo 
								$albums[$i]->rating_count; ?> vote<?php echo $albums[$i]->rating_count 
								== 1 ? '' : 's'; ?>)
						</div>
						<script type="text/javascript" language="javascript">
	
							$('#star_rating_<?php echo "album_".$i; ?>').raty({
								half:false,
								score:<?php echo $albums[$i]->rating; ?>,
								readOnly:<?php echo $this->session->userdata('user_id') ? 
									'false' : 'true'; ?>,
								path:"<?php echo base_url().'images/'; ?>",
								hints:['hate', 'dislike', 'meh', 'like', 'love'],
								click: function(score, evt){
									$.post("<?php echo site_url().'/albums/rate'; ?>",
									{'rating':score, 'album_id':<?php echo $albums[$i]->album_id; ?>},
									function(result){
										if(result == '<?php echo SESSION_EXPIRED; ?>')
										{
											location.reload(true);
											return false;
										}
										if(result)
										{
											var results = result.split(":");
											$('#star_rating_<?php echo "album_".$i; ?>').raty('score', 
												results[0]);
											if(results[1] == 1)
											{
												$('#average_rating_<?php echo "album_".$i; ?>').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' vote)');
											}
											else
											{
												$('#average_rating_<?php echo "album_".$i; ?>').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' votes)');
											}
										}
									});
								}
							});
						
						</script>
						<div class="list_item_blurb">
							<div id="blurb_text_<?php echo $i; ?>">
								<?php echo $albums[$i]->description(); ?>
								<?php if($can_reorder) echo '<div style="margin-top:5px"><img id="edit_blurb_button_'.$i.'" src="'.base_url().'images/pencil_icon.jpg" class="edit_icon" /></div>'; ?>
							</div>
							<?php if($can_reorder) { ?>
								<div class="blurb_text_area" style="display:none" id="blurb_edit_<?php echo $i; ?>">
									<?php echo form_open(); ?>
									<?php echo form_textarea(array('name' => 'blurb', 'rows' => 4, 'cols' => 44,'id' => 'blurb_text_area_'.$i, 'style' => 'font-size:.9em')); ?>
									<div>
										<img id='save_blurb_button_<?php echo $i; ?>' src="<?php echo base_url().'images/save_icon.jpg'; ?>" class='save_icon' />
									</div>
									<?php echo form_close(); ?>
								</div>
								<script type="text/javascript" language="javascript">
								
									$('#edit_blurb_button_<?php echo $i; ?>').click(function(){
										if($('#blurb_edit_<?php echo $i; ?>').is(':hidden'))
										{
											$('#blurb_text_<?php echo $i; ?>').hide();
											$('#blurb_edit_<?php echo $i; ?>').show();
											$('#blurb_text_area_<?php echo $i; ?>').val('<?php echo addslashes($albums[$i]->blurb); ?>');
											$('#blurb_text_area_<?php echo $i; ?>').select();
										}
									});
									
									$('#save_blurb_button_<?php echo $i; ?>').click(function(){
										var blurb = jQuery.trim($('#blurb_text_area_<?php echo $i; ?>').val());
										$('#blurb_error_<?php echo $i; ?>').replaceWith('');
										if(blurb.length > 150)
										{
											$('#blurb_text_area_<?php echo $i; ?>').after('<div id="blurb_error_<?php echo $i; ?>" class="error_text">Keep it short. 150 characters max.</div>');
											return false;
										}
										$.post("<?php echo site_url().'/lists/edit_album_blurb'; ?>",
											{'blurb':blurb, 'album_id':<?php echo $albums[$i]->album_id; ?>, 'list_id':<?php echo $can_reorder; ?>},
											function(result)
											{
												$('#blurb_error_<?php echo $i; ?>').replaceWith('');
												if(result == '<?php echo SESSION_EXPIRED; ?>')
												{
													location.reload(true);
													return false;
												}
												if(result)
												{
													$('#list_of_albums').html(result);
												}
											});
									});
							
								</script>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if($can_reorder){ ?>
			<script type="text/javascript" language="javascript">
				$('#list_of_albums').sortable({
					opacity:0.7,
					axis:'y',
					cursor:'move',
					start:function(event, ui){
						old_index = $(ui.item).index();
					},
					stop:function(event, ui){
						if(busy)
						{
							return false;
						}
						var new_index = $(ui.item).index();
						if(new_index == old_index)
						{
							return true;
						}
						busy = true;
						$('body').toggleClass('wait', busy);
						$.post("<?php echo site_url().'/lists/reorder'; ?>",
						{'old_ordinal':old_index + 1, 'new_ordinal':new_index + 1, 
							'list_id':<?php echo $can_reorder; ?> },
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
					}
				});
			</script>
		<?php } ?>
		<?php if($pageable) { ?>
			<div class="page_navigation">
				<div class="back_button">
					<?php if(back_available($offset)) { ?>
					<img id='album_back_button' class='back_next_icon' src='<?php echo base_url('images/back_icon.jpg'); ?>'/> 
					<?php } ?>
				</div>
				<div class="page_number"><?php echo current_page($offset); ?></div>
				<div class="next_button">
					<?php if(next_available($offset, $count)) { ?>
					<img id='album_next_button' class='back_next_icon' src='<?php echo base_url('images/next_icon.jpg'); ?>'/> 
					<?php } ?>
				</div>
			</div>
			<script type="text/javascript" language="javascript">
			
				function refresh_albums(offset)
				{
					$.post("<?php echo $post_url; ?>",
						{'offset':offset, 
							<?php if($sortable) echo "'direction':'".$direction."', 'order_by':'".$order_by."', " ?>
							<?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#list_of_albums').replaceWith(result);
							}
						}
					);
				}
				
				$('#album_back_button').click(function(){
					refresh_albums(<?php echo $offset - PAGED_LIST_SIZE; ?>);
				});
				
				$('#album_next_button').click(function(){
					refresh_albums(<?php echo $offset + PAGED_LIST_SIZE; ?>);
				});
				
			</script>
		<?php } ?>
		<?php if($can_remove && !$can_reorder){ ?>
			<script type="text/javascript" language="javascript">
			
				var busy = false;
				
			
				$('.remove_album_icon').live('click', function(){
					if(busy)
					{
						return false;
					}
					var album_id = $(this).attr('alt');
					busy = true;
					$('body').toggleClass('wait', busy);
					$.post("<?php echo site_url().'/search/hide_recommendation'; ?>",
						{'album_id':album_id},
						function(result){
							if(result == 'SESSION_EXPIRED')
							{
								location.reload(true);
								return false;
							}
							if(result)
							{
								location.reload(true);
								busy = false;
							}
						});
				});
			
			</script>
		<?php } ?>
	</div>
</html>