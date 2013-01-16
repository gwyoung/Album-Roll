<!DOCTYPE html>
<html>
	<div class="content_header">
		getting started
	</div>
	<div class="list_items">
		<div class="list_item">
			<div class="instructions">
				Welcome, <?php echo $name; ?>! Navigating around your stuff should be pretty self-explanatory with that sidebar on the left, but here are a few tips to get you on your way. For more information about the site in general, click on that "about the site" link at the bottom of the page.
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header">
				<?php echo anchor('users/options', '1. Upload a Profile Picture'); ?>
			</div>
			<div class="instructions">
				Upload a new image on the account options page.
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header">
				<?php echo anchor('users/profile/'.$user_id, '2. Customize Your Profile'); ?>
			</div>
			<div class="instructions">
				Edit your information or add links to your blogs or favorite sites on your profile.
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header">
				<?php echo anchor('lists/roll/'.$favorites_id, '3. Share Your Favorite Albums'); ?>
			</div>
			<div class="instructions">
				Add items to your list of all-time favorites. These will appear on your profile. Once you have some albums on your lists, you'll start getting recommendations based on what you like.
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header">
				<?php echo anchor('lists/roll/'.$current_rotation_id, '4. Share What You\'re Listening To'); ?>
			</div>
			<div class="instructions">
				Add items to your current rotation. People can scroll through these on your profile as well.
			</div>
		</div>
		<div class="list_item">
			<div class="sub_header">
				<?php echo anchor('lists/create', '5. Create a Themed List'); ?>
			</div>
			<div class="instructions">
				Got an idea for a new list? Make it! Some examples: "Great Summer Albums", "Best of 2011", or "Highlights of 90's Post-Rock".
			</div>
		</div>
		<!--
		<div class="list_item">
			<div class="sub_header">
				<?php echo anchor('home/invite', '6. Invite Your Friends'); ?>
			</div>
			<div class="instructions">
				You can share your creations with anyone by copy-pasting the URL, but it'll be more fun if your friends can share too. Invite them!
			</div>
		</div>
		-->
	</div>
</html>