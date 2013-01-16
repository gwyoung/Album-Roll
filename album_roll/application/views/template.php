<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Pragma" content="no-cache" />
		
		<!-- my style -->
		<link rel="stylesheet" href="<?php echo base_url();?>css/style.css" type="text/css" 
			media="screen" />
			
		<!-- jquery ui style -->
		<link rel="stylesheet" href="<?php echo base_url();?>css/jquery-ui-1.8.21.custom.css" type="text/css" 
			media="screen" />
		
		<!-- jquery & Jcrop -->
		<script src="<?php echo base_url();?>js/jquery.min.js"></script>
		<script src="<?php echo base_url();?>js/jquery.Jcrop.min.js"></script>
		<link rel="stylesheet" href="<?php echo base_url();?>css/jquery.Jcrop.css" type="text/css" />
		
		<!-- jquery raty -->
		<script src="<?php echo base_url();?>js/jquery.raty.min.js"></script>
		
		<!-- jquery ui -->
		<script src="<?php echo base_url();?>js/jquery-ui.min.js"></script>
		
		<link rel="shortcut icon" href="<?php echo base_url(); ?>images/album_roll_icon.ico" />
		
		<link rel="image_src" href="<?php echo base_url(); ?>images/sharing_icon.jpg" />
		
		<meta name="description" content="Like a playlist, but for albums. List and explore your record collection!" />
		
		<title>album roll</title>
	</head>
	<body>
		<div id="header">
			<a href="<?php echo base_url();?>">
				<img src="<?php echo base_url();?>images/header.jpg" width="800" />
			</a>
		</div>
		<div id="content">
			<div id="left_column">
				<div id="left_column_content">
					<?php $this->load->view($left_column); ?>
				</div>
			</div>
			<div id="center"><?php $this->load->view($view); ?></div>
			<div id="right_column">
				<div id="right_column_content">
					<div class="sidebar_header_text">search</div>
					<div class="sidebar_field">
						<?php echo form_open('search/keyword', array('id' => 'search_form')); ?>
						<?php echo form_input(array('id' => 'search_box', 'name' => 'search', 'size' => 15,
							'maxlength' => 25)); ?>
						<?php echo form_hidden('current_url', current_url()); ?>
						<?php echo form_close(); ?>
						<img src="<?php echo base_url('images/search_icon.jpg'); ?>" class="search_icon" id="search_button" />
					</div>
					<div class="list_items">
						<div class="sidebar_header_text"><?php echo anchor('search/trendy', 'trendy'); ?></div>
						<?php foreach($trending_albums as $album){ ?>
							<div class="sidebar_image_div">
								<a href="<?php echo site_url('albums/album/'.$album->album_id); ?>">
									<img class="sidebar_image" src="<?php echo $album->image_url(); ?>"
										alt="<?php echo $album; ?>" title="<?php echo $album; ?>">
									</img>
								</a>
							</div>
						<?php } ?>
						<div class="sidebar_header_text"><?php echo anchor('search/recent', 'recent'); ?></div>
						<?php foreach($recent_albums as $album){ ?>
							<div class="sidebar_image_div">
								<a href="<?php echo site_url('albums/album/'.$album->album_id); ?>">
									<img class="sidebar_image" src="<?php echo $album->image_url(); ?>"
										alt="<?php echo $album; ?>" title="<?php echo $album; ?>">
									</img>
								</a>
							</div>
						<?php } ?>
						<div class="sidebar_header_text"><?php echo anchor('search/year/'.date('Y'), date('Y')); ?></div>
						<?php foreach($trending_by_year as $album){ ?>
							<div class="sidebar_image_div">
								<a href="<?php echo site_url('albums/album/'.$album->album_id); ?>">
									<img class="sidebar_image" src="<?php echo $album->image_url(); ?>"
										alt="<?php echo $album; ?>" title="<?php echo $album; ?>">
									</img>
								</a>
							</div>
						<?php } ?>
					</div>
				</div>
				<script>
					$('#search_box').autocomplete({
						minLength:3,
						source: function(request, response){
							$.ajax({
								url:"<?php echo site_url('autocomplete/all');?>",
								dataType: 'json',
								type: 'POST',
								data: {term: $('#search_box').val()},
								success: function(data){
									response(data);
								}
							});
						},
						select: function(event, ui){
							event.preventDefault();
							window.location = ui.item.value;
						}
					});
					
					$('#search_button').click(function(){
						if($('#search_box').val())
						{
							$('#search_form').submit();
						}
					});
					
					var content_top = $('#content').offset().top;
					
					var left = $('#left_column_content');
					var right = $('#right_column_content');
					var center = $('#center');
					
					var $window = $(window);
					
					var isMobile = {
					    Android: function() {
					        return navigator.userAgent.match(/Android/i) ? true : false;
					    },
					    BlackBerry: function() {
					        return navigator.userAgent.match(/BlackBerry/i) ? true : false;
					    },
					    iOS: function() {
					        return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;
					    },
					    Windows: function() {
					        return navigator.userAgent.match(/IEMobile/i) ? true : false;
					    },
					    any: function() {
					        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Windows());
					    }
					};
					
					if(!isMobile.any())
					{
						$(window).scroll(function() {
						    var window_top = $window.scrollTop();
						
						    left.toggleClass('sticky', window_top >= content_top);
							right.toggleClass('sticky', window_top >= content_top);
							center.toggleClass('sticky', window_top >= content_top);
						});
					}
					
				</script>
			</div>
		</div>
		<div id="footer">
			<a id="about_link" class="footer_link" href="<?php echo site_url('home/about');?>">about the site</a>
			<a id="contact_link" class="footer_link" href="<?php echo site_url('email/contact');?>">contact</a>
			<a id="invite_link" class="footer_link" href="<?php echo site_url('email/invite');?>">invite</a>
			<a id="bug_link" class="footer_link" href="<?php echo site_url('email/feedback');?>">give feedback</a>
		</div>
	</body>
	<head>
		<meta http-equiv="Pragma" content="no-cache" />
	</head>
</html>