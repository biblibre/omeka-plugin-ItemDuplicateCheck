<?php echo head(array('title' => __('Edit rule'))); ?>
<?php echo flash(); ?>

<form action="<?php echo url('item-duplicate-check'); ?>/rules/save" method="post">
    <section class="seven columns alpha">
        <div class="field">
            <div class="two columns alpha">
                <label for="item_type_id"><?php echo __('Item Type'); ?></label>
            </div>
            <div class="five columns omega">
                <div class="inputs">
                    <select id="item_type_id" name="item_type_id">
                        <?php foreach ($itemTypes as $itemType): ?>
                            <?php $selected = ($itemType->id == $rule->item_type_id); ?>
                            <option value="<?php echo $itemType->id; ?>" <?php if ($selected):?>selected="selected"<?php endif; ?>>
                                <?php echo $itemType->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="field">
            <div class="two columns alpha">
                <label for="element_ids"><?php echo __('Elements'); ?></label>
            </div>
            <div class="five columns omega">
                <div class="inputs">
                    <select id="element_ids" name="element_ids[]" multiple="multiple" size="10">
                        <?php $rule_element_ids = unserialize($rule->element_ids); ?>
                        <?php foreach ($elements as $element): ?>
                            <?php $selected = in_array($element->id, $rule_element_ids); ?>
                            <option value="<?php echo $element->id; ?>" <?php if ($selected) { echo 'selected="selected"'; } ?>>
                                <?php echo $element->getElementSet()->name . ' : ' . $element->name; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="hidden" name="rule_id" value="<?php echo $rule->id; ?>">
            <input type="submit" class="submit big green button" value="<?php echo __('Save'); ?>">
        </div>
    </section>
</form>



<?php echo foot(); ?>
