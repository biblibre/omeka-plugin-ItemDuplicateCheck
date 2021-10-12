<?php echo head(array('title' => __('Delete rule'))); ?>

<?php echo flash(); ?>

<?php
    $element_names = array();
    foreach ($rule->getElements() as $element) {
        $element_names[] = $element->name;
    }
?>

<p>
    <?php echo __('Are you sure you want to delete the rule applied to all Items %1$s and checking for duplicates with %2$s as parameters?', 
                  ($rule->getItemType()->name != '' ? __('identified by the Item Type') . ' <b>' . $rule->getItemType()->name .'</b>' : ''), 
                  implode(' and ', array_map(function($val) { return '<b>' . $val . '</b>'; }, $element_names))); 
    ?>
</p>

<form action="<?php echo url('item-duplicate-check'); ?>/rules/delete" method="post">
    <input type="hidden" name="rule_id" value="<?php echo $rule->id; ?>">
    <input type="hidden" name="confirm" value="1">
    <input type="submit" value="<?php echo __('Delete'); ?>">
    <a href="<?php echo url('item-duplicate-check'); ?>/rules/list"><?php echo __('Cancel'); ?></a>
</form>

<?php echo foot(); ?>
