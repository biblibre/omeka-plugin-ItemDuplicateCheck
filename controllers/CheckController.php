<?php

class ItemDuplicateCheck_CheckController extends Omeka_Controller_AbstractActionController {

    public function checkAction()
    {
        $id = $this->_getParam('id');
        if ($id) {
            $item = $this->_helper->db->getTable('Item')->find($id);
        } else {
            $item = new Item;
        }

        $post = $_POST;
        $item->setPostData($post);

        $duplicates = item_duplicate_check_get_duplicates($item);
        $this->view->duplicates = $duplicates;
    }
}
