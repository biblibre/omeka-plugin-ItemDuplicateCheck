<?php

class ItemDuplicateCheckRule extends Omeka_Record_AbstractRecord
{
    public $id;
    public $item_type_id;
    public $element_ids;

    public function getItemType() {
        return $this->_db->getTable('ItemType')->find($this->item_type_id);
    }

    public function getElements() {
        $elements = array();
        $table = $this->_db->getTable('Element');
        foreach (unserialize($this->element_ids) as $element_id) {
            $elements[] = $table->find($element_id);
        }
        return $elements;
    }
}
