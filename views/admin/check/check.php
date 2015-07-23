<?php if (!empty($duplicates)): ?>
    <div id="item_duplicate_check" class="flash">
        <?php
            if (count($duplicates) == 1) {
                echo __('Duplicate found');
            } else {
                echo __('Duplicates found');
            }
        ?>:
        <ul>
            <?php foreach ($duplicates as $duplicate): ?>
                <?php $dItem = $duplicate['item']; ?>
                <?php $dRule = $duplicate['rule']; ?>
                <li>
                    <a href="<?php echo url('items') . '/show/' . $dItem->id; ?>"><?php echo metadata($dItem, array('Dublin Core', 'Title')); ?> (#<?php echo $dItem->id; ?>)</a>
                    <ul>
                        <?php foreach ($dRule->getElements() as $element): ?>
                            <li>
                                <?php echo __($element->name) ?>
                                <ul>
                                    <?php $texts = metadata($dItem, array($element->getElementSet()->name, $element->name), array('all' => true)); ?>
                                    <?php foreach ($texts as $text): ?>
                                        <li><?php echo $text ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php echo $this->formCheckbox('item_duplicate_check_ignore') ?>
        <?php echo $this->formLabel('item_duplicate_check_ignore', __('Ignore duplicates')); ?>
    </div>
<?php endif; ?>
