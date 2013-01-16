<!DOCTYPE html>
<html>
	<div id="list_of_lists" >
		<?php if (empty($lists)) { ?>
			<div class="list_item">
				<div class="instructions">No lists here.</div>
			</div>
		<?php } else if($sortable) { ?>
			<div class="sort_bar">
				<div class="sort_text">order by:</div>
				<div class="sort_options">
					<?php echo form_open(); ?>
					<?php echo form_dropdown('list_direction', array('asc' => 'ascending', 'desc' => 
						'descending'), $direction, 'id="list_direction"'); ?>
					<?php echo form_dropdown('list_order_by', AlbumList::displayed_sort_options(), 
						$order_by, 'id="list_order_by"'); ?>
					<?php echo form_close(); ?>
				</div>
			</div>
			<script type="text/javascript" language="javascript">
			
				function refresh_sorting()
				{
					var direction = $('#list_direction').val();
					var order_by = $('#list_order_by').val();
					$.post("<?php echo $post_url; ?>",
						{'offset':0, 'direction':direction, 'order_by':order_by, 
							<?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#list_of_lists').replaceWith(result);
							}
						}
					);
				}
			
				$('#list_direction').change(refresh_sorting);
				
				$('#list_order_by').change(refresh_sorting);
			
			</script>
		<?php } ?>		
		<?php foreach($lists as $list){ ?>
			<div class="list_item">
				<div class="list_item_table">
					<div class="medium_image_div">
						<a href="<?php echo site_url('lists/roll/'.$list->list_id); ?>">
							<table>
								<tr>
									<td>
									<?php if($list->image_urls[0]){ ?>
										<img class="quarter_image" src="<?php echo
											Album::full_image_url($list->image_urls[0]); ?>"/>
									<?php } else { ?>
										<img class="number_square" src="<?php echo base_url('images/1.jpg'); ?>"></img>
									<?php } ?>
									</td>
									<td>
									<?php if($list->image_urls[1]){ ?>
										<img class="quarter_image" src="<?php echo
											Album::full_image_url($list->image_urls[1]); ?>"/>
									<?php } else { ?>
										<img class="number_square" src="<?php echo base_url('images/2.jpg'); ?>"></img>
									<?php } ?>
									</td>
								</tr>
								<tr>
									<td>
									<?php if($list->image_urls[2]){ ?>
										<img class="quarter_image" src="<?php echo
											Album::full_image_url($list->image_urls[2]); ?>"/>
									<?php } else { ?>
										<img class="number_square" src="<?php echo base_url('images/3.jpg'); ?>"></img>
									<?php } ?>
									</td>
									<td>
									<?php if($list->image_urls[3]){ ?>
										<img class="quarter_image" src="<?php echo
											Album::full_image_url($list->image_urls[3]); ?>"/>
									<?php } else { ?>
										<img class="number_square" src="<?php echo base_url('images/4.jpg'); ?>"></img>
									<?php } ?>
									</td>
								</tr>
							</table>
						</a>
					</div>
					<div class="list_item_metadata">
						<div class="list_item_title"><?php echo anchor('lists/roll/'.$list->list_id, 
							$list->title); ?></div>
						<div class="star_rating" id="star_rating_<?php echo "list_".$list->list_id; ?>"></div>
						<div id="average_rating_<?php echo "list_".$list->list_id; ?>" 
							class="average_rating">
							<?php echo $list->rating; ?> &nbsp&nbsp(<?php echo $list->rating_count; ?> vote<?php echo $list->rating_count == 1 ? '' : 's'; ?>)
						</div>
						<script type="text/javascript" language="javascript">
		
								$('#star_rating_<?php echo "list_".$list->list_id; ?>').raty({
									half:false,
									score:<?php echo $list->rating; ?>,
									readOnly:<?php echo $this->session->userdata('user_id') ? 
										'false' : 'true'; ?>,
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
												$('#star_rating_<?php echo "list_".$list->list_id; ?>')
													.raty('score', results[0]);
												if(results[1] == 1)
												{
													$('#average_rating_<?php echo "list_".$list->list_id; ?>').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' vote)');
												}
												else
												{
													$('#average_rating_<?php echo "list_".$list->list_id; ?>').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' votes)');
												}
											}
										});
									}
								});
							
						</script>
						<div class="list_item_blurb"><?php echo $list->blurb; ?></div>
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if($pageable) { ?>
			<div class="page_navigation">
				<div class="back_button">
					<?php if(back_available($offset)) { ?>
					<img id='list_back_button' class='back_next_icon' src='<?php echo base_url('images/back_icon.jpg'); ?>'/> 
					<?php } ?>
				</div>
				<div class="page_number"><?php echo current_page($offset); ?></div>
				<div class="next_button">
					<?php if(next_available($offset, $count)) { ?>
					<img id='list_next_button' class='back_next_icon' src='<?php echo base_url('images/next_icon.jpg'); ?>'/> 
					<?php } ?>
				</div>
			</div>
			<script type="text/javascript" language="javascript">
			
				function refresh_lists(offset)
				{
					$.post("<?php echo $post_url; ?>",
						{'offset':offset, 
							<?php if($sortable) echo "'direction':'".$direction."', 'order_by':'".$order_by."', " ?>
							<?php echo $post_values; ?>},
						function(result)
						{
							if(result)
							{
								$('#list_of_lists').replaceWith(result);
							}
						}
					);
				}
				
				$('#list_back_button').click(function(){
					refresh_lists(<?php echo $offset - PAGED_LIST_SIZE; ?>);
				});
				
				$('#list_next_button').click(function(){
					refresh_lists(<?php echo $offset + PAGED_LIST_SIZE; ?>);
				});
				
			</script>
		<?php } ?>
	</div>
</html>