<?php
/**
 * @copyright   Copyright (C) 2019. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

KSSystem::import('models.modelksform');
class KsenMartModelLoad1s extends JModelList {
    var $row = null;

    public function __construct($row = null) {
        parent::__construct();
        $this->row = $row;

    }

    public function getTable($type = '', $prefix = 'KsenmartTable', $config = array()){
//         JTable::addIncludePath(JPATH_ROOT . '/components/com_api/tables');
	return JTable::getInstance($type, $prefix, $config);
    }

    public function products($row = null) {
        $lang = JLanguage::getInstance('ru-RU');
        $data = (array)$row;
        $data['alias'] = (!empty($data['alias']))?$lang->transliterate($data['alias']) : $lang->transliterate($data['product_code']);
        $data['price'] = str_replace(",",  ".", $data['price']);
        $data['price'] = str_replace(' ',  "", $data['price']);
// Заполнение категорий
if (isset($data['categories'])
        && !empty($data['categories'])
        ){
        $category = explode(",", $data['categories']);
        $data['categories'] = array();
        foreach($category as $i=>$c){
            $query = $this->_db->getQuery(true);
            $query->select('id')->from('#__ksenmart_categories')->where("title='" . $c ."'");
            $this->_db->setQuery($query);
            $v = $this->_db->loadResult();
            $data['categories'][] = $v;
            if ($i == max(array_keys($category))) $data['categories']['default'] =$v;
        }
    }
if (isset($data['properties'])
        && !empty($data['properties'])
        ){
// Заполнение свойств
    $data['properties'] = array();
    foreach($data['properti'] as $i=>$c){
        $query = $this->_db->getQuery(true);
        $query->select('id')->from('#__ksenmart_properties')->where("title='" . $c->s ."'");
        $this->_db->setQuery($query);
        $v = $this->_db->loadResult();
        if($v > 0){
            $data['properties'][$v]['text'] = $c->z;
        }
    }

}
// Заполнение производителя
    if (isset($data['manufacturer'])
        && !empty($data['manufacturer'])
        ){
//             print_r($data['manufacturer']);
            $query = $this->_db->getQuery(true);
            $query->select('id')->from('#__ksenmart_manufacturers')->where("title='" . $data['manufacturer'] ."'");
            $this->_db->setQuery($query);
            $v = $this->_db->loadResult();
            if($v > 0){
//                 print_r($v);
                $data['manufacturer']=$v;
            }
        }

        $config = array('dbo'=>array('_tbl'=>'#__ksenmart_products','_tbl_key'=>'guid'));
        $table = $this->getTable("load1s",'KsenmartTable',$config);
        $table->load($row->guid);
        $table->bind($row);
        $data['id'] = $table->id;
	$data['type'] = 'product';
	$data['is_parent'] = 0;
	$data['price_type'] = 1;
	$data['old_price'] = 0;
	$data['purchase_price'] = 0;
	$data['alias'] =  $lang->transliterate($data['product_code']);

	if (isset($data['files'])
            && !empty($data['files'])
            ){
                $v = -1;
                $files = $data['files'];
                $query = $this->_db->getQuery(true);
                $query->select('id, filename')->from('#__ksenmart_files')->where("filename like'" . $data['files'] ."%'");
                $this->_db->setQuery($query);
                $mage = $this->_db->loadObject();
                //     echo $query;
                //     var_dump($mage);
                if($mage != null && $mage->id > 0){
//                     print_r($mage);
                    $v = $mage->id;
                    $files = $mage->filename;
                }

            $data['images'] = array();
            $data['images'][$v]['params']['title']="";
            $data['images'][$v]['params']['watermark'] = 1;
            $data['images'][$v]['params']['displace'] = 1;
            $data['images'][$v]['params']['halign'] = "center";
            $data['images'][$v]['params']['valign'] = "middle";
            $data['images'][$v]['ordering'] = "";
            $data['images'][$v]['task'] = "save";
            $data['images'][$v]['filename'] = $files;
        }
//         var_dump($table->alias);
        $table->alias = (!empty($table->alias))?$lang->transliterate($table->alias) : $lang->transliterate($table->product_code);
        $table->price = str_replace(",",  ".", $table->price);
	$table->price = str_replace(' ',  "", $table->price);
	$table->type = "product";
// file_put_contents(JPATH_CACHE . "/log.php", $data, FILE_APPEND);
// file_put_contents(JPATH_CACHE . "/log.php", "\n", FILE_APPEND);
//         print_r($table->price);
//        $result = $table->store();
//         echo "-------";
//         $table->load($row->guid);
//         print_r($table->price);

        $result = $this->saveProduct($data);
        return $result;
    }

    function saveProduct($data) {
//         $this->onExecuteBefore('saveProduct', array(&$data));

        $data['alias'] = KSFunctions::CheckAlias($data['alias'], $data['id']);
        $data['alias'] = $data['alias'] == '' ? KSFunctions::GenAlias($data['title']) : $data['alias'];
        $data['new'] = isset($data['new']) ? $data['new'] : 0;
        $data['promotion'] = isset($data['promotion']) ? $data['promotion'] : 0;
        $data['hot'] = isset($data['hot']) ? $data['hot'] : 0;
        $data['recommendation'] = isset($data['recommendation']) ? $data['recommendation'] : 0;
        $table = $this->getTable('products');

        if(empty($data['id'])) {
            $query = $this->_db->getQuery(true);
            $query->update('#__ksenmart_products')->set('ordering=ordering+1');
            $this->_db->setQuery($query);
            $this->_db->query();
            $data['date_added'] = JFactory::getDate()->toSql();
        }

        if(!$table->bindCheckStore($data)) {
            $this->setError($table->getError());
            return false;
        }
//         print_r($table);
//         exit();
        $id = $table->id;
        KSMedia::saveItemMedia($id, $data, 'product', 'products');

//         $tagsObserver = $table->getObserverOfClass('JTableObserverTags');
//         $result = $tagsObserver->setNewTags($data['tags'], true);
// Категории
if (isset($data['categories'])
        && !empty($data['categories'])
        ){
        JArrayHelper::toInteger($data['categories']);
        $default_category = 0;
        if(isset($data['categories']['default'])) {
            $default_category = $data['categories']['default'];
            unset($data['categories']['default']);
        }
        $in = array();
        foreach($data['categories'] as $category_id) {
            $table = $this->getTable('ProductCategories');
            $d = array(
                'product_id' => $id,
                'category_id' => $category_id,
                );
            if($table->load($d)) {
                $d['id'] = $table->id;
            }
            $d['is_default'] = ($category_id == $default_category) ? 1 : 0;
            if(!$table->bindCheckStore($d)) {
                $this->setError($table->getError());
                return false;
            }
            $in[] = $table->id;
        }
        $query = $this->_db->getQuery(true);
        $query->delete('#__ksenmart_products_categories')->where('product_id=' . $id);
        if(count($in)) {
            $query->where('id not in (' . implode(',', $in) . ')');
        }
        $this->_db->setQuery($query);
        $this->_db->query();
}
// Свойства
        $values = array();
if (isset($data['properties'])
        && !empty($data['properties'])
        ){
        foreach($data['properties'] as $property_id => $property) {
            $property_id = (int)$property_id;
            $query = $this->_db->getQuery(true);
            $query->select('type')->from('#__ksenmart_properties')->where('id=' . $property_id);
            $this->_db->setQuery($query);
            $type = $this->_db->loadResult();
            if(empty($type)) {
                $this->setError(JText::_('KSM_CATALOG_PRODUCT_INVALID_PROPERTY_DATA'));
                return false;
            }
            switch($type) {
                case 'text':
					if(!empty($property['text'])){
						$property['product_id'] = $id;
						$property['property_id'] = $property_id;
						$text = $this->_db->quote($property['text']);
						$query = $this->_db->getQuery(true);
						$query->select('*')->from('#__ksenmart_property_values')->where('title=' . $text);
						$this->_db->setQuery($query);
						$value_row = $this->_db->loadObject();
						if(empty($value_row)) {
							$p_alias = KSFunctions::GenAlias($text);
							$query = $this->_db->getQuery(true);
							$query->insert('#__ksenmart_property_values')->columns('property_id,title,alias')->values($property_id . ',' . $text . ',' . $this->_db->quote($p_alias));
							$this->_db->setQuery($query);
							$this->_db->query();
							$property['value_id'] = $this->_db->insertid();
						} else {
							$property['value_id'] = $value_row->id;
						}
						$values[] = $property;
					}
                    break;
                case 'select':
                    foreach($property as $tmpkey => $tmpvalue) {
                        if(array_key_exists('id', $tmpvalue) && $tmpvalue['id'] == $tmpkey) {
                            unset($tmpvalue['id']);
                            $tmpvalue['product_id'] = $id;
                            $tmpvalue['property_id'] = $property_id;
                            $tmpvalue['value_id'] = $tmpkey;
                            $values[] = $tmpvalue;
                        }
                    }
                    break;
            }
        }
}
        $in = array();
        foreach($values as $value) {
            $table = $this->getTable('ProductPropertiesValues');
            $d = array('product_id' => $value['product_id'], 'property_id' => $value['property_id']);
            if(array_key_exists('value_id', $value)) {
                $d['value_id'] = $value['value_id'];
            }
            if($table->load($d)) {
                $value['id'] = $table->id;
            }
            if(!$table->bindCheckStore($value)) {
                $this->setError($table->getError());
                return false;
            }
            $in[] = $table->id;
        }
        $query = $this->_db->getQuery(true);
        $query->delete('#__ksenmart_product_properties_values')->where('product_id=' . $id);
        if(count($in)) {
            $query->where('id not in (' . implode(',', $in) . ') ');
        }
        $this->_db->setQuery($query);
        $this->_db->query();

        $query = $this->_db->getQuery(true);
        $query->delete('#__ksenmart_products_relations')->where('product_id=' . $id)->where('relation_type=' . $this->_db->quote('relation'));
        $this->_db->setQuery($query);
        $this->_db->query();
//         foreach($data['relative'] as $k => $v) {
//             $v['product_id'] = $id;
//             $v['relation_type'] = 'relation';
//             $table = $this->getTable('ProductRelations');
//             if(!$table->bindCheckStore($v)) {
//                 $this->setError($table->getError());
//                 return false;
//             }
//         }

//         foreach($data['childs'] as $k => $v) {
//             $v['published'] = isset($v['published']) ? $v['published'] : 0;
//             $table = $this->getTable('products');
//             if(!$table->bindCheckStore($v)) {
//                 $this->setError($table->getError());
//                 return false;
//             }
//         }

//         $child_groups = JRequest::getVar('child_groups', array());
//         foreach($child_groups as $k => $v) {
//             if($k != 0) {
//                 $table = $this->getTable('ProductsChildGroups');
//                 if(!$table->bindCheckStore($v)) {
//                     $this->setError($table->getError());
//                     return false;
//                 }
//             }
//         }

        $on_close = 'window.parent.ProductsList.refreshList();';
        $return = array('id' => $id, 'on_close' => $on_close);

//         $this->onExecuteAfter('saveProduct', array(&$return));
        return $return;
    }
}
