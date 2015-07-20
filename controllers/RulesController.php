<?php

class ItemDuplicateCheck_RulesController extends Omeka_Controller_AbstractActionController
{
    public function listAction()
    {
        $this->view->rules = $this->_getRules();
    }

    public function addAction()
    {
        $this->view->itemTypes = $this->_getItemTypes();
        $this->view->elements = $this->_getElements();
    }

    public function editAction()
    {
        $rule_id = $this->_getParam('rule_id');

        $this->view->rule = $this->_getRule($rule_id);
        $this->view->itemTypes = $this->_getItemTypes();
        $this->view->elements = $this->_getElements();
    }

    public function deleteAction()
    {
        $rule_id = $this->_getParam('rule_id');
        $rule = $this->_getRule($rule_id);

        $confirm = $this->_getParam('confirm');
        if ($confirm) {
            $rule->delete();
            $this->_helper->redirector('list');
            return;
        }

        $this->view->rule = $rule;
    }

    public function saveAction()
    {
        $rule_id = $this->_getParam('rule_id');
        $item_type_id = $this->_getParam('item_type_id');
        $element_ids = $this->_getParam('element_ids', array());

        $rule = $this->_getRule($rule_id);
        if (!isset($rule)) {
            $rule = new ItemDuplicateCheckRule;
        }
        $rule->item_type_id = $item_type_id;
        $rule->element_ids = serialize($element_ids);
        $rule->save();

        $this->_helper->redirector('list');
    }

    protected function _getRules()
    {
        return get_db()
            ->getTable('ItemDuplicateCheckRule')
            ->findAll();
    }

    protected function _getRule($rule_id)
    {
        return get_db()
            ->getTable('ItemDuplicateCheckRule')
            ->find($rule_id);
    }

    protected function _getItemTypes()
    {
        return get_db()
            ->getTable('ItemType')
            ->findAll();
    }

    protected function _getElements()
    {
        return get_db()
            ->getTable('Element')
            ->findAll();
    }
}
