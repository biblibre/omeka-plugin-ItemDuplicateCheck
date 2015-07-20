<?php echo head(array('title' => __('Delete rule'))); ?>
<?php echo flash(); ?>

<?php
    $element_names = array();
    foreach ($rule->getElements() as $element) {
        $element_names[] = $element->name;
    }
?>
<h2><?php echo __('Are you sure you want to delete rule %1$s (%2$s) ?', $rule->getItemType()->name, implode(', ', $element_names)); ?></h2>

<form action="<?php echo url('item-duplicate-check'); ?>/rules/delete" method="post">
    <input type="hidden" name="rule_id" value="<?php echo $rule->id; ?>">
    <input type="hidden" name="confirm" value="1">
    <input type="submit" value="<?php echo __('Delete'); ?>">
    <a href="<?php echo url('item-duplicate-check'); ?>/rules/list"><?php echo __('Cancel'); ?></a>
</form>

<?php echo foot(); ?>
