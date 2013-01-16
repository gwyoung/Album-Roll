<!DOCTYPE html>
<html>
	<div class="sidebar_header_text">my stuff</div>
	<div class="sidebar_image_div">
		<a href="<?php echo site_url('users/profile/'.$current_user->user_id); ?>">
			<img class="sidebar_image" src="<?php echo $current_user->image_url(); ?>"
				alt="<?php echo $current_user->name; ?>" title="<?php echo $current_user->name; ?>">
			</img>
		</a>
	</div>
	<div class="sidebar_options">
		<div class="sidebar_text">
			<?php echo anchor('users/profile/'.$user_id, '1. Profile'); ?>
		</div>
		<div class="sidebar_text">
			<?php echo anchor('lists/user/'.$user_id, '2. My Lists'); ?>
		</div>
		<div class="sidebar_text">
			<?php echo anchor('lists/roll/'.$favorites_id, '3. Favorites'); ?>
		</div>
		<div class="sidebar_text">
			<?php echo anchor('lists/roll/'.$current_rotation_id, '4. Current Rotation'); ?>
		</div>
		<div class="sidebar_text">
			<?php echo anchor('lists/create', '5. New List'); ?>
		</div>
		<div class="sidebar_text">
			<?php echo anchor('users/options', '6. Options'); ?>
		</div>
		<div class="sidebar_text">
			<?php echo anchor('users/getting_started/'.$user_id, '7. Getting Started'); ?>
		</div>
		<div class="sidebar_text">
			<?php echo anchor('users/signout', '8. Sign Out'); ?>
		</div>
	</div>
	<div class="list_items">
		<div class="sidebar_header_text"><?php echo $recommended_header; ?></div>
		<?php foreach($recommended as $album){ ?>
			<div class="sidebar_image_div">
				<a href="<?php echo site_url('albums/album/'.$album->album_id); ?>">
					<img class="sidebar_image" src="<?php echo $album->image_url(); ?>"
						alt="<?php echo $album; ?>" title="<?php echo $album; ?>">
					</img>
				</a>
			</div>
		<?php } ?>
	</div>
</html>