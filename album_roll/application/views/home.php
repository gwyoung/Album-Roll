<!DOCTYPE html>
<html>
	<div class="content_header">
		welcome!
	</div>
	<div class="list_items">
		<div class="list_item">
			<div class="instructions">
				Thanks for beta testing!
				<br />
				<br />
				<?php echo $user_count; ?> <?php echo anchor('home/random_user', 'users', array('title' => 'random user')); ?>, <?php echo $list_count; ?> <?php echo anchor('home/random_list', 'lists', array('title' => 'random list')); ?>, <?php echo $album_count; ?> <?php echo anchor('home/random_album', 'albums', array('title' => 'random album')); ?> and counting
			</div>
		</div>
		<div class="sub_content_header">
			album of the week
		</div>
		<div class="list_item" id="album_of_the_week_background">
			<div id="album_of_the_week">
				<a href="<?php echo site_url('albums/album/'.$album_of_the_week->album_id); ?>">
					<img class="huge_image" src="<?php echo $album_of_the_week->image_url(); ?>" />
				</a>
			</div>
			<div class="list_item_title">
				<?php echo anchor('search/artist/'.$album_of_the_week->artist->artist_id, 
					$album_of_the_week->artist->name); ?>
				&nbsp-&nbsp
				<?php echo anchor('albums/album/'.$album_of_the_week->album_id, $album_of_the_week->title); ?>
			</div>
			<div class="star_rating" id="star_rating"></div>
			<div id="aotw_average_rating" class="average_rating">
				<?php echo $album_of_the_week->rating; ?> &nbsp&nbsp(<?php echo $album_of_the_week->rating_count; ?> vote<?php echo $album_of_the_week->rating_count == 1 ? '' : 's'; ?>)
			</div>
			<script type="text/javascript" language="javascript">

				$('#star_rating').raty({
					half:false,
					score:<?php echo $album_of_the_week->rating; ?>,
					readOnly:<?php echo $this->session->userdata('user_id') ? 'false' : 'true'; ?>,
					path:"<?php echo base_url().'images/'; ?>",
					hints:['hate', 'dislike', 'meh', 'like', 'love'],
					click: function(score, evt){
						$.post("<?php echo site_url().'/albums/rate'; ?>",
						{'rating':score, 'album_id':<?php echo $album_of_the_week->album_id; ?>},
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
									$('#aotw_average_rating').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' vote)');
								}
								else
								{
									$('#aotw_average_rating').html(results[0] + ' &nbsp&nbsp(' + results[1] + ' votes)');
								}
							}
						});
					}
				});
			
			</script>
			<div class="list_item_blurb">
				<div id="blurb_text">
					<?php echo $album_of_the_week->description(); ?>
				</div>
			</div>
		</div>
		<div class="sub_content_header">
			trendy tags
		</div>
		<div class="list_item">
			<div class="instructions">
				<?php foreach($trendy_tags as $tag){ ?>
					<?php echo anchor('search/tag/'.$tag->tag_id, $tag->name); ?>
					<?php if($tag != end($trendy_tags)) echo "&nbsp&nbsp&nbsp"; ?>
				<?php } ?>
			</div>
		</div>
		<div class="sub_content_header">
			top-rated albums
		</div>
		<?php echo $top_albums; ?>
		<div class="sub_content_header">
			top-rated lists
		</div>
		<?php echo $top_lists; ?>
	</div>
</html>