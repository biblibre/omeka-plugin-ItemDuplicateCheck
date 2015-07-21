<?php
require_once CONTROLLER_DIR . '/ItemsController.php';

class ItemDuplicateCheck_ItemsController extends ItemsController {
    /*
     * This method is a copy of ItemsController::addAction()
     * with call to parent::addAction() inlined and some changes surrounded by
     * "CHANGES START" and "CHANGES END" comments".
     */
    public function addAction()
    {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getItemElementSets();
        if (!Zend_Registry::isRegistered('file_derivative_creator') && is_allowed('Settings', 'edit')) {
            $this->_helper->flashMessenger(__('The ImageMagick directory path has not been set. No derivative images will be created. If you would like Omeka to create derivative images, please set the path in Settings.'));
        }

        $class = $this->_helper->db->getDefaultModelName();
        $varName = $this->view->singularize($class);

        if ($this->_autoCsrfProtection) {
            $csrf = new Omeka_Form_SessionCsrf;
            $this->view->csrf = $csrf;
        }

        $record = new $class();
        if ($this->getRequest()->isPost()) {
            if ($this->_autoCsrfProtection && !$csrf->isValid($_POST)) {
                $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
                $this->view->$varName = $record;
                return;
            }

            $record->setPostData($_POST);

            // CHANGES START
            if (!$this->_getParam('force')) {
                $duplicates = $this->_getDuplicates($_POST, $record->id);
                if (!empty($duplicates)) {
                    $this->view->duplicates = $duplicates;
                    $this->view->post = $_POST;
                    $this->view->$varName = $record;
                    return;
                }
            }
            // CHANGES END

            if ($record->save(false)) {
                $successMessage = $this->_getAddSuccessMessage($record);
                if ($successMessage != '') {
                    $this->_helper->flashMessenger($successMessage, 'success');
                }
                $this->_redirectAfterAdd($record);
            } else {
                $this->_helper->flashMessenger($record->getErrors());
            }
        }
        $this->view->$varName = $record;
    }

    /*
     * This method is a copy of ItemsController::editAction()
     * with call to parent::editAction() inlined and some changes surrounded by
     * "CHANGES START" and "CHANGES END" comments".
     */
    public function editAction() {
        // Get all the element sets that apply to the item.
        $this->view->elementSets = $this->_getItemElementSets();
        if (!Zend_Registry::isRegistered('file_derivative_creator') && is_allowed('Settings', 'edit')) {
            $this->_helper->flashMessenger(__('The ImageMagick directory path has not been set. No derivative images will be created. If you would like Omeka to create derivative images, please set the path in Settings.'));
        }

        $varName = $this->view->singularize($this->_helper->db->getDefaultModelName());

        $record = $this->_helper->db->findById();

        if ($this->_autoCsrfProtection) {
            $csrf = new Omeka_Form_SessionCsrf;
            $this->view->csrf = $csrf;
        }

        if ($this->getRequest()->isPost()) {
            if ($this->_autoCsrfProtection && !$csrf->isValid($_POST)) {
                $this->_helper->_flashMessenger(__('There was an error on the form. Please try again.'), 'error');
                $this->view->$varName = $record;
                return;
            }

            // CHANGES START
            if (!$this->_getParam('force')) {
                $duplicates = $this->_getDuplicates($_POST, $record->id);
                if (!empty($duplicates)) {
                    $this->view->duplicates = $duplicates;
                    $this->view->post = $_POST;
                    $this->view->$varName = $record;
                    return;
                }
            }
            // CHANGES END

            $record->setPostData($_POST);
            if ($record->save(false)) {
                $successMessage = $this->_getEditSuccessMessage($record);
                if ($successMessage != '') {
                    $this->_helper->flashMessenger($successMessage, 'success');
                }
                $this->_redirectAfterEdit($record);
            } else {
                $this->_helper->flashMessenger($record->getErrors());
            }
        }

        $this->view->$varName = $record;
    }

    /**
     * Search for possible duplicates according to POST data and configured
     * rules.
     */
    protected function _getDuplicates($post, $id = null)
    {
        $db = get_db();
        $rules = $db->getTable('ItemDuplicateCheckRule')->findAll();
        $duplicates = array();
        foreach ($rules as $rule) {
            if ($rule->item_type_id && $post['item_type_id'] != $rule->item_type_id) {
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

                foreach ($post['Elements'][$element_id] as $value) {
                    $text = $value['text'];
                    if (function_exists('element_types_format')) {
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

            if (isset($id)) {
                $select->where("i.id != ?", $id);
            }
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

    protected function _redirectAfterAdd($record)
    {
        $this->_helper->redirector('browse', 'items', 'default');
    }

    protected function _redirectAfterEdit($record)
    {
        $this->_helper->redirector('show', 'items', 'default', array('id' => $record->id));
    }
}
