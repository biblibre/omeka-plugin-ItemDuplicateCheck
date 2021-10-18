<?php

require_once dirname(__FILE__) . '/helpers/ItemDuplicateCheckFunctions.php';

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
        queue_js_file('item_duplicate_check');
        queue_js_string('Omeka.WEB_DIR = ' . js_escape(WEB_DIR) . ';');
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
