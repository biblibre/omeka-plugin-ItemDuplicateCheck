<?php echo head(array('title' => __('Add a rule'))); ?>

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
                        <option value="">** <?php echo __('Any type'); ?> **</option>
                        <?php foreach ($itemTypesForSelect as $index => $value): ?>
                            <option value="<?php echo $index; ?>">
                                <?php echo $value; ?>
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

        <div class="field">
            <div class="two columns alpha">
                <label for="collection_id"><?php echo __('Collection'); ?></label>
            </div>
            <div class="five columns omega">
                <div class="inputs">
                    <select id="collection_id" name="collection_id">
                        <option value="">** <?php echo __('Any collection'); ?> **</option>
                        <?php foreach ($collectionsForSelect as $index => $value): ?>
                            <option value="<?php echo $index; ?>">
                                <?php echo $value; ?>
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
