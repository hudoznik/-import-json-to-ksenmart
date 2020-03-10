<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

/**
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.registry.registry');
jimport('joomla.application.component.controller');

class KsenMartControllerload1c extends JControllerLegacy {

    protected $row;
    protected $type;
    protected $result;
    protected $load;
    protected $error;
    protected $data;
    protected $msg;

    private function getToken(){
        return  md5(date('Y-m-d')."Кодовая фраза");
    }

    public function getTokens(){
        echo  md5(date('Y-m-d')."Кодовая фраза");
    }

    private function verifiautch(){
        $app = JFactory::getApplication();
        if (JRequest::getMethod() != "POST"){
            $app->redirect(JUri::root());
        }
        $header = getallheaders();
        if (isset($header['authorization'])
            && $header['authorization'] == $this->getToken() )  return;
        if (isset($header['Authorization'])
            && $header['Authorization'] == $this->getToken() )  return;
        echo new JResponseJson("", "Авторизация не пройдена",true);
        JFactory::getApplication()->close();
    }

    private function price_old(){
        $this->verifiautch();
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $result = new stdClass();
        $result->susses = "ok";
        $error = false;
        $msg = "";
        JFactory::getDocument()->setMimeEncoding('application/json');
        $load = new JRegistry($app->input->json->getRaw());
        ( !$error )? $type = $load->get('type',true) : $error = true;
        ( isset($type) && $type === true ) ? $this->display($type, " Тип загрузки не существует") : "";
        ( isset($type) && $type === true ) ? $error = true : "";
        ( !$error )? $list = $load->get('list',true) : $error = true;
        (isset($list) && $list === true || count($list) == 0 ) ? $this->display($list, " Список данных пустой или не существует") : $this->row = $list;
        $this->result =  array("err"=>$error,"msg"=>null,"data"=>null);
        if (!$this->result['err']){
            try{
                $this->{"load$type"}();
            } catch (Exception $e) {
                echo "catch";
                $result['err'] = true;
                $result['msg'] = $e->getMessage();
            }
        }

//         if ($result['err']){
//             $error = true;
//             $msg =
//         }

//         print_r($msg);
//         $this->display($this->result['err'], $this->result['msg'], $this->result['data']);
    }

    public function display( $err = false, $msg = null, $data = ""){

//         Если нужен лог для тестирования
//         file_put_contents(JPATH_CACHE . "/log.php", file_get_contents('php://input'), FILE_APPEND);
//         file_put_contents(JPATH_CACHE . "/log.php", "\n", FILE_APPEND);

        $this->verifiautch();
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();
        $result = new stdClass();
        $result->susses = "ok";
        $error = false;
        $msg = "";
//         Загружаем JSON данные
        $this->load = new JRegistry($app->input->json->getRaw());
//         Находим тип загрузки
        $type = null;
        ( !$error )? $type = $this->load->get('type',true) : $error = true;
        $this->type = $type;
        if (method_exists($this, $type)){
            $this->$type();
        }
        echo new JResponseJson($this->data,$this->msg,$this->error);
        JFactory::getApplication()->close();

    }


    private function closesite(){
//         Если на время обновления надо закрыть сайт
//         Коментируем строку ниже и в
        return;
        $this->verifiautch();
        date_default_timezone_set('Europe/Moscow');
        $temp = JFactory::getConfig();
        $config = new JRegistry(new JConfig());
        $config->set('offline',1);
        $config->set('offline_message',"Сайт закрыт на техническое обслуживание.<br />Оринтеровочное время возобновления работы ". date('Y-m-d H:i', time() + 60*60) );
        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.file');
        $file = JPATH_CONFIGURATION . '/configuration.php';
        chmod($file, 0755);
        $configuration = $config->toString('PHP', array('class' => 'JConfig', 'closingtag' => false));
        $fp = fopen($file, 'w');
        fwrite($fp, $configuration);
        chmod($file, 0644);
        fclose($fp);
    }

    private function opensite(){
        $this->verifiautch();
        $temp = JFactory::getConfig();
        $config = new JRegistry(new JConfig());
        $config->set('offline',0);

        jimport('joomla.filesystem.path');
        jimport('joomla.filesystem.file');
        $file = JPATH_CONFIGURATION . '/configuration.php';
        chmod($file, 0755);
        $configuration = $config->toString('PHP', array('class' => 'JConfig', 'closingtag' => false));
        $fp = fopen($file, 'w');
        fwrite($fp, $configuration);
        chmod($file, 0644);
        fclose($fp);
    }

    private function price(){
    $this->verifiautch();
        $this->products();
    }

    private function clearstock(){
//         Если обнуление склада не нужно, то либо раскоментировать строку, либо вообще не реализовывать вызов
//         return;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__ksenmart_products')
              ->set("in_stock = 0");
        $db->setQuery($query);
        $db->execute();
    }
    private function products() {
        ( !$this->error )? $this->row = $this->load->get('row',null) : $this->error = true;
//         print_r($this->row);
        if (!$this->error && $this->row != null){
            $db = JFactory::getDbo();
            $model = $this->getModel('Load1s', 'KsenmartModel');
            foreach($this->row as $r ){
                $this->data[] = $model->products($r);
            }
        }

    }

//     Получение всех цен для сравнения если нужно
    private function getAllPrice(){
        $this->verifiautch();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('price,guid')
            ->from('#__ksenmart_products')
            ->where('in_stock > 0');
        $db->setQuery($query);
        $result = $db->loadObjectList();
        echo new JResponseJson($result);
        JFactory::getApplication()->close();

    }


    public function loadprice() {
        $this->verifiautch();
        $app = JFactory::getApplication();
        if (count($this->row) == 0) throw new Exception('Список данных пустой или не существует');
        $db = JFactory::getDbo();
        $db->replacePrefix('guid');
        $n = 0;
        foreach ($this->row as $i=>$v){
//         print_r($v);
//         print_r($this->row);
//             try {
            $query = $db->getQuery(true);
            $v->price = str_replace(",", ".", $v->price);
            $query = $db->getQuery(true);
            $query->select('*')
                ->from('#__ksenmart_products')
                ->where("guid = '".$v->guid."'");
            $db->setQuery($query);
            $row = $db->loadObject();
            if ($row == null ){
                $query = $db->getQuery(true);
                $v->{'price_type'} = "1";
                $result = $db->insertObject("#__ksenmart_products", $v, 'guid');
                $n = +1;
            }else {
                $result = $db->updateObject("#__ksenmart_products", $v, 'guid');
            }
//             print_r($v);
            if (!$result) $app->enqueueMessage("Строка ".($i+1)." не загрузилась");
        }
        if ($n >0) $app->enqueueMessage("Загружено $n новых товаров");
        return true;
    }
}
