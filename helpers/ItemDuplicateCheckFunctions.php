<?php
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

            if ($rule->collection_id && $item->collection_id != $rule->collection_id) {
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
?>
