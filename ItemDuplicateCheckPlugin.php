<?php

require_once dirname(__FILE__) . '/helpers/ItemDuplicateCheckFunctions.php';

class ItemDuplicateCheckPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'initialize',
        'define_acl',
        'config',
        'config_form',
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
                collection_id int(10) unsigned NULL DEFAULT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY (item_type_id) REFERENCES {$db->ItemType} (id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (collection_id) REFERENCES {$db->Collection} (id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ";
        $db->query($sql);

        set_option('item_duplicate_check_list_layout', 'default');
    }

    public function hookUninstall()
    {
        $db = $this->_db;
        $db->query("DROP TABLE IF EXISTS {$db->ItemDuplicateCheckRule}");

        delete_option('item_duplicate_check_list_layout');
    }
    
    public function hookUpgrade()
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];

        if (version_compare($oldVersion, '1.1', '<')) {
            $db = $this->_db;
            $sql = "
                ALTER TABLE {$db->ItemDuplicateCheckRule}
                ADD collection_id int(10) unsigned NULL DEFAULT NULL;
            ";
            $db->query($sql);

            $sql = "
                ALTER TABLE {$db->ItemDuplicateCheckRule}
                ADD FOREIGN KEY (collection_id) REFERENCES {$db->Collection} (id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ";
            $db->query($sql);
        }
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
    
    public function hookConfig($args)
    {
        $post = $args['post'];
        set_option('item_duplicate_check_list_layout', $post['item_duplicate_check_list_layout']);
    }
    
    public function hookConfigForm()
    {
        include 'config_form.php';
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
