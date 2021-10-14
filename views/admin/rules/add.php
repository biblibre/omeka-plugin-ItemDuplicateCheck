<?php echo head(array('title' => __('Add a new rule'))); ?>

<?php echo flash(); ?>

<form action="<?php echo url('item-duplicate-check'); ?>/rules/save" method="post">
    <section class="seven columns alpha">
		<p>
			<?php echo __('Choose an <strong>Item Type</strong> to which to apply the rule, then the <strong>Element</strong> (or combination of <strong>Elements</strong>) that make up the rule and cannot be duplicated.'); ?>
		</p>

        <div class="field">
            <div class="two columns alpha">
                <label for="item_type_id"><?php echo __('Item Type'); ?></label>
            </div>
            <div class="five columns omega">
                <div class="inputs">
                    <select id="item_type_id" name="item_type_id">
                        <option value="">** <?php echo __('All types'); ?> **</option>
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
                <?php
                    echo $this->formSelect(
                        "element_ids[]",
                        @$_POST['element_ids'],
                        array(
                            'id' => 'element_ids',
                            'multiple' => true,
                            'size' => 10,
                        ), 
                        $elements
                    );
                ?>
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
