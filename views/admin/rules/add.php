<?php echo head(array('title' => __('Add a new rule'))); ?>
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
                            <option value="<?php echo $itemType->id; ?>">
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
                        <?php foreach ($elements as $element): ?>
                            <option value="<?php echo $element->id; ?>">
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
            <input type="submit" class="submit big green button" value="<?php echo __('Save'); ?>">
        </div>
    </section>
</form>

<?php echo foot(); ?>
