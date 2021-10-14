<?php echo head(array('title' => __('Rules') . (count($rules) > 0 ? ' ' . __('(%s total)', count($rules)) : ''))); ?>

<?php echo flash(); ?>

<a href="<?php echo html_escape(url('item-duplicate-check/rules/add')); ?>" class="add full-width-mobile button green"><?php echo __('Add a new rule'); ?></a>

<?php if (count($rules)): ?>
  <table>
    <thead>
      <tr>
        <th><?php echo __('Item Type'); ?></th>
        <th><?php echo __('Elements'); ?></th>
        <th><?php echo __('Actions'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rules as $rule): ?>
        <tr>
          <td>
            <?php
              $itemType = $rule->getItemType();
              echo $itemType ? $itemType->name : '** ' . __('All types') . ' **';
            ?>
          </td>
          <td>
            <?php
              $element_names = array();
              foreach ($rule->getElements() as $element) {
                $element_names[] = __($element->name);
              }
              echo implode(' + ', $element_names);
            ?>
          </td>
          <td>
            <a href="<?php echo url('item-duplicate-check') . "/rules/edit/rule_id/{$rule->id}"; ?>"><?php echo __('Edit'); ?></a>
            | <a href="<?php echo url('item-duplicate-check') . "/rules/delete/rule_id/{$rule->id}"; ?>"><?php echo __('Delete'); ?></a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php echo foot(); ?>
