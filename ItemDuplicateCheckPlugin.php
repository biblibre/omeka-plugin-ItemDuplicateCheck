<?php

class ItemDuplicateCheckPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'initialize',
        'before_save_item',
    );

    protected $_filters = array(
        'admin_navigation_main',
    );

    public function hookInstall()
    {
        $db = $this->_db;
        $sql = "
            CREATE TABLE IF NOT EXISTS {$db->ItemDuplicateCheckRule} (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                item_type_id int(10) unsigned NULL DEFAULT NULL,
                element_ids text NOT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY (item_type_id) REFERENCES {$db->ItemType} (id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ";
        $db->query($sql);
    }

    public function hookUninstall()
    {
        $db = $this->_db;
        $db->query("DROP TABLE IF EXISTS {$db->ItemDuplicateCheckRule}");
    }

    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    public function hookBeforeSaveItem($args)
    {
        error_log('before_save_item');
        $item = $args['record'];
        $post = $args['post'];
        $db = $this->_db;

        $rules = $db->getTable('ItemDuplicateCheckRule')->findAll();
        foreach ($rules as $rule) {
            if ($rule->item_type_id && $item->item_type_id != $rule->item_type_id) {
                continue;
            }

            $elements = $rule->getElements();
            if (empty($elements)) {
                continue;
            }

            $select = $db
                ->select()
                ->from(array('i' => $db->Item), array('item_id' => 'id'));

            foreach ($elements as $element) {
                $element_id = $element->id;

                foreach ($post['Elements'][$element_id] as $value) {
                    $text = $value['text'];
                    if (function_exists('element_types_format')) {
                        $text = element_types_format($element_id, $text);
                    }

                    $select->joinLeft(
                        array("et_$element_id" => $db->ElementText),
                        "i.id = et_{$element_id}.record_id AND et_{$element_id}.record_type = 'Item' AND et_{$element_id}.element_id = {$element_id}"
                    );

                    $select->where("et_{$element_id}.text = ?", $text);
                }
            }

            if ($item->id) {
                $select->where("i.id != ?", $item->id);
            }
            $item_ids = $db->fetchCol($select);
            if (!empty($item_ids)) {
                $item->addError(null, __("Found duplicate items") . ' (ids: ' . implode(', ', $item_ids) . ')');
            }
        }
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Item Duplicate Check'),
            'uri' => url('item-duplicate-check/rules/list'),
        );
        return $nav;
    }
}
