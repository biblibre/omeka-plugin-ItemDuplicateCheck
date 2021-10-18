<?php	
	$view = get_view();
?>

<div class="field">
	<div class="two columns alpha">
		<?php echo $view->formLabel('item_duplicate_check_list_layout', __('Element List Layout')); ?>
	</div>
	<div class="inputs five columns omega">
		<p class="explanation">
			<?php echo __('How Elements are grouped and sorted in Element lists.'); ?>
		</p>
		<?php echo $view->formRadio('item_duplicate_check_list_layout',
			get_option('item_duplicate_check_list_layout'),
			null,
			array(
				'default' => __('Default order (as set in Admin / Settings)'),
				'el_alpha' => __('All Elements, alphabetical order'),
				'elset_alpha' => __('Elements grouped by Element Set, alphabetical order')
			)); 
		?>
	</div>
</div>
