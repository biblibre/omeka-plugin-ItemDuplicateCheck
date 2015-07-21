<?php

class ItemDuplicateCheckPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = array(
        'install',
        'uninstall',
        'initialize',
        'define_routes',
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

    /**
     * Overrides /items/add and /items/edit/:id paths
     */
    public function hookDefineRoutes($args)
    {
        if (!is_admin_theme()) {
            return;
        }

        $router = $args['router'];
        $paths = array(
            'add' => '/items/add',
            'edit' => '/items/edit/:id',
        );
        foreach (array('add', 'edit') as $action) {
            $router->addRoute(
                "item_duplicate_check_items_$action",
                new Zend_Controller_Router_Route(
                    $paths[$action],
                    array(
                        'module' => 'item-duplicate-check',
                        'controller' => 'items',
                        'action' => $action,
                    )
                )
            );
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
