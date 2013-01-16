<!DOCTYPE html>
<html>
	<div class="content_header">
		create list
	</div>
	<div class="list_items">
		<div class="list_item">
			<?php echo form_open('lists/create', array('id' => 'new_list_form')); ?>
			<div class="form">
				<div class="form_name">
					1. Title:
				</div>
				<div class="form_field">
					<?php echo form_input(array('name' => 'title', 'size' => 30, 'maxlength' => 50,
						'value' => set_value('title'), 'id' => 'new_list_title')); ?>
					<?php if($title_error) echo $title_error; ?>
				</div>
			</div>
			<div class="form">
				<div class="form_name">
					2. Blurb:
				</div>
				<div class="form_field">
					<?php echo form_textarea(array('name' => 'blurb', 'rows' => 5, 'cols' => 32, 
						'value' => set_value('blurb'), 'id' => 'list_blurb')); ?>
				</div>
			</div>
			<div class="form">
				<div class="form_name">
					3. Type:
				</div>
				<div class="form_field">
					<?php echo form_dropdown('type', array('Themed' => 'Themed', 'Year' => 'Year', 'Decade'
						=> 'Decade'), set_value('type'), 'id="list_type"'); ?>
				</div>
			</div>
			<div class="form" id="year_form" <?php if(set_value('type') != 'Year') 
				echo 'style="display:none"'?>>
				<div class="form_name">
					4. Year:
				</div>
				<div class="form_field">
					<?php echo form_input(array('name' => 'year', 'size' => 4, 'maxlength' => 5,
						'value' => set_value('year'), 'id' => 'list_year')); ?>
				</div>
			</div>
			<div class="form" id="decade_form" <?php if(set_value('type') != 'Decade') 
				echo 'style="display:none"'?>>
				<div class="form_name">
					4. Decade:
				</div>
				<div class="form_field">
					<?php echo form_dropdown('decade', array('2010s' => '2010s', '2000s' => '2000s', 
						'1990s' => '1990s', '1980s' => '1980s', '1970s' => '1970s', '1960s' => '1960s'), 
						set_value('decade'), 'id="list_decade"'); ?>
				</div>
			</div>
			<?php echo form_submit(array('name' => 'submit', 'id' => 'form_submit', 
				'class' => 'form_submit', 'value' => 'create')); ?>
			<?php echo form_close(); ?>
		</div>
	</div>
	
	<script type="text/javascript" language="javascript">
	
		var year_required = false;
		var decade_required = false;
		
		$('#list_type').change(function(){
			$('#year_form').hide();
			$('#decade_form').hide();
			year_required = false;
			decade_required = false;
			
			if($('#list_type').val() == 'Year')
			{
				$('#year_form').show();
				year_required = true;
			}
			else if($('#list_type').val() == 'Decade')
			{
				$('#decade_form').show();
				decade_required = true;
			}
		});
		
		$('#new_list_form').submit(function() {
			$('#title_error').replaceWith('');
			$('#blurb_error').replaceWith('');
			$('#type_error').replaceWith('');
			$('#year_error').replaceWith('');
			$('#decade_error').replaceWith('');
			
			var title = $('#new_list_title').val();
			var blurb = $('#list_blurb').val();
			var type = $('#list_type').val();
			var year = $('#list_year').val();
			var decade = $('#list_decade').val();
			
			var success = true;
			
			if(!title)
			{
				$('#new_list_title').after('<div id="title_error" class="error_text">The title field is required.</div>');
				success = false;
			}
			if(title.length > 50)
			{
				$('#new_list_title').after('<div id="title_error" class="error_text">The title can\'t be more than 50 characters.</div>');
				success = false;
			}
			if(blurb.length > 150)
			{
				$('#list_blurb').after('<div id="blurb_error" class="error_text">The blurb can\'t be more than 150 characters.</div>');
				success = false;
			}
			if(!type)
			{
				$('#list_type').after('<div id="type_error" class="error_text">The type field is required.</div>');
				success = false;
			}
			if(!year && year_required)
			{
				$('#list_year').after('<div id="year_error" class="error_text">The year field is required.</div>');
				success = false;
			}
			else if(year_required && (!(year.length == 4) || isNaN(year) || 
				parseInt(year) > new Date().getFullYear()))
			{
				$('#list_year').after('<div id="year_error" class="error_text">You must enter a valid 4-digit year.</div>');
				success = false;
			}
			if(!decade && decade_required)
			{
				$('#list_year').after('<div id="decade_error" class="error_text">The decade field is required.</div>');
				success = false;
			}
			
			return success;
		});
		
	</script>
</html>