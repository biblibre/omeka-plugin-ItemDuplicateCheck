<?php

class ItemDuplicateCheck_RulesController extends Omeka_Controller_AbstractActionController
{
    public function listAction()
    {
        $this->view->rules = $this->_getRules();
    }
    
    public function addAction()
    {
        $this->view->itemTypesForSelect = $this->_getItemTypesForSelect();
        $this->view->elements = $this->_getElements();
    }
    
    public function editAction()
    {
        $rule_id = $this->_getParam('rule_id');
        $this->view->rule = $this->_getRule($rule_id);
        $this->view->itemTypesForSelect = $this->_getItemTypesForSelect();
        $this->view->elements = $this->_getElements();
    }
    
    public function deleteAction()
    {
        $rule_id = $this->_getParam('rule_id');
        $rule = $this->_getRule($rule_id);
        $confirm = $this->_getParam('confirm');
        if ($confirm) {
            $rule->delete();
            $this->_helper->flashMessenger(__('The rule was successfully deleted.'), 'success');
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
        if (empty($element_ids)) {
            $this->_helper->flashMessenger(__('No element was chosen. Please try again, choosing at least one element.', $action), 'error');
            $this->_helper->redirector('list');
            return;
        }
        $rule = $this->_getRule($rule_id);
        if (!isset($rule)) $rule = new ItemDuplicateCheckRule;
        $rule->item_type_id = $item_type_id ? $item_type_id : null;
        $rule->element_ids = serialize($element_ids);
        $rule->save();
        $action = (isset($rule_id) ? __('edited') : __('added'));
        $this->_helper->flashMessenger(__('The rule was successfully %s.', $action), 'success');
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
    
    protected function _getItemTypesForSelect()
    {
        return get_db()
            ->getTable('ItemType')
            ->findPairsForSelectForm();
    }
    
    protected function _getElements()
    {
        $show_element_set_headings = get_option('show_element_set_headings');
        $db = get_db();
        $table = $db->getTable('Element');
        $options = $table->findPairsForSelectForm(array(
            'record_types' => array('Item', 'All'),
            'sort' => ($show_element_set_headings ? 'alphaBySet' : 'alpha')
        ));
        $options = apply_filters('elements_select_options', $options);

        if ($show_element_set_headings) {
            foreach ($options as $option) {
                $optGroup = $option['item_type_name']
                    ? __('Item Type') . ': ' . __($option['item_type_name'])
                    : __($option['element_set_name']);
                $value = __($option['element_name']);
                if ($value != '') $options[$optGroup][$option['element_id']] = $value;
            }
            // sort alphabetically element names in each element set
            foreach ($options as &$option) {
                asort($option);
            }
        } else {
			// sort alphabetically all element names
            asort($options);
        }

        return $options;
    }
}
