<?php

class Popup extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url'));
        $this->load->model('Entry_Model');
    }

    public function search($content)
    {

        // ajax请求
        $result = array();
        $query_result = array();
        // $result['content'] = urldecode($content);

        $query = $this->Entry_Model->search_for(urldecode($content));

        foreach($query->result() as $row)
        {
            $query_result[] = $row->name;
        }

        $result['total'] = $query->num_rows();
        $result['entry_name'] = $query_result;

        echo json_encode($result);
        exit;
    }
}

?>
