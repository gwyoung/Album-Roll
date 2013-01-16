<!DOCTYPE html>
<html>
	<div class="content_header">
		<?php echo $search_title; ?>
	</div>
	<div class="list_items">
		<?php if($albums){ ?>
			<?php if($lists){ ?>
				<div class="sub_content_header">albums</div>
			<?php } ?>
			<?php echo $albums; ?>
		<?php } ?>
		<?php if($lists){ ?>
			<?php if($albums){ ?>
				<div class="sub_content_header">lists</div>
			<?php } ?>
			<?php echo $lists; ?>
		<?php } ?>
	</div>
</html>