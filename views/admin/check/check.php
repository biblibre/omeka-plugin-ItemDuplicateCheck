<?php if (!empty($duplicates)): ?>
    <div id="item_duplicate_check" class="flash alert">
        <span id="item_duplicate_check_toggle" class="item_duplicate_check-collapsible"></span>
        <p class="item_duplicate_check-warning"><?php
            $duplicatesCount = count($duplicates);
            echo __(plural("Warning: a duplicate of this Item has been found!", "Warning: %s duplicates of this Item have been found!", $duplicatesCount), ($duplicatesCount >= 10 ? __('at least') . ' ' . $duplicatesCount : $duplicatesCount));
        ?></p>
        <ul id="item_duplicate_check_duplicates">
            <?php foreach ($duplicates as $duplicate): ?>
                <?php $dItem = $duplicate['item']; ?>
                <?php $dRule = $duplicate['rule']; ?>
                <li>
                    #<?php echo $dItem->id; ?>: <a href="<?php echo url('items') . '/show/' . $dItem->id; ?>"><?php echo metadata($dItem, array('Dublin Core', 'Title')); ?></a>
                    <ul class="duplicate">
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
        <div>
            <?php echo $this->formCheckbox('item_duplicate_check_ignore') ?>
            <?php echo $this->formLabel('item_duplicate_check_ignore', __('Ignore all duplicates when saving')); ?>
        </div>
    </div>
<?php endif; ?>
