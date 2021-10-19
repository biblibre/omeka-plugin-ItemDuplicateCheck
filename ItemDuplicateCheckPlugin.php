<?php

class ItemDuplicateCheckPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'initialize',
        'define_acl',
        'admin_head',
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

    public function hookDefineAcl($args)
    {
        // Restrict menu access to super and admin users
        $args['acl']->addResource('ItemDuplicateCheck_Rules');
    }
    
    public function hookAdminHead()
    {
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();

		if ($controller == 'items') {
			queue_js_file('item_duplicate_check');
			queue_js_string('Omeka.WEB_DIR = ' . js_escape(WEB_DIR) . ';');
			queue_css_file('item_duplicate_check');
		}
    }

    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Item Duplicate Check'),
            'uri' => url('item-duplicate-check/rules/list'),
            'resource' => 'ItemDuplicateCheck_Rules'
        );
        return $nav;
    }
}

function item_duplicate_check_get_duplicates($item)
{
    $db = get_db();
    $rules = $db->getTable('ItemDuplicateCheckRule')->findAll();

    $duplicates = array();
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

        $joins_added = array();
        foreach ($elements as $element) {
            $element_id = $element->id;

            foreach ($item['Elements'][$element_id] as $value) {
                $text = $value['text'];
                if (0 != strlen(trim($text)) && function_exists('element_types_format')) {
                    $text = element_types_format($element_id, $text);
                }

                if (!isset($joins_added[$element_id])) {
                    $select->joinLeft(
                        array("et_$element_id" => $db->ElementText),
                        "i.id = et_{$element_id}.record_id AND et_{$element_id}.record_type = 'Item' AND et_{$element_id}.element_id = {$element_id}"
                    );
                    $joins_added[$element_id] = true;
                }

                $select->where("et_{$element_id}.text = ?", $text);
            }
        }

        $where = $select->getPart(Zend_Db_Select::WHERE);
        if (empty($where)) {
            # This will just return the whole items table, abort
            continue;
        }

        if (isset($item->id)) {
            $select->where("i.id != ?", $item->id);
        }
        $select->limit(10);
        $item_ids = $db->fetchCol($select);
        foreach ($item_ids as $item_id) {
            $duplicates[] = array(
                'item' => $db->getTable('Item')->find($item_id),
                'rule' => $rule,
            );
        }
    }

    return $duplicates;
}
