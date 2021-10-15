<?php if (!empty($duplicates)): ?>
    <div id="item_duplicate_check" class="flash alert">
        <?php
            $countDuplicates = count($duplicates);
            echo __(plural("Warning: a duplicate of this Item has been found!", "Warning: %s duplicates of this Item have been found!", $countDuplicates), $countDuplicates);
        ?>
        <ul style="list-style-type: none; margin-left: 0; padding-left: 0";>
            <?php foreach ($duplicates as $duplicate): ?>
                <?php $dItem = $duplicate['item']; ?>
                <?php $dRule = $duplicate['rule']; ?>
                <li>
                    #<?php echo $dItem->id; ?>: <a href="<?php echo url('items') . '/show/' . $dItem->id; ?>"><?php echo metadata($dItem, array('Dublin Core', 'Title')); ?></a>
                    <ul style="list-style-type: none";>
                        <?php foreach ($dRule->getElements() as $element): ?>
                            <li>
                                <b><?php echo __($element->name); ?></b>
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
        <?php echo $this->formLabel('item_duplicate_check_ignore', __('Ignore all duplicates when saving')); ?>
    </div>
<?php endif; ?>
