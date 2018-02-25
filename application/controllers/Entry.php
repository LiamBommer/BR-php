<?php

class Entry extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url'));
        $this->load->model('Entry_Model');
    }

    /*
     * @param 
     *  $content
     * 
     * @return
     *  result['entry']  名字符合的词条
     *  result['inte']    词条下面的解释
     *  result['result'] = 'empty'  没有结果
     */
    public function search()
    {/*{{{*/

        // ajax返回结果数组
        $result = array();

        $content = $this->input->get('search_content');

        // 查询内容非空检查
        if(isset($content))
        {
            $query = $this->Entry_Model->search($content);
            if($query != FALSE)
            {
                $result = $query;
                echo json_encode($result);
                exit;

            } else
            {
                $result['result'] = 'empty';
                echo json_encode($result);
                exit;
            }

        }

    }/*}}}*/


    /*
     * @param 
     *  $entry_name
     * 
     * @return
     *  result['result']  成功与否
     *      = 'success'
     *      = 'failure'
     *
     */
    public function create()
    {/*{{{*/

        // ajax返回结果数组
        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['entry_name'] = $this->input->get('entry_name');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        $db_result = $this->Entry_Model->create($data);

        if($db_result['result'] == 'failure')
        {
            $result['result'] = 'failure';
            $result['error_msg'] = $db_result['error_msg'];
        } else
        {
            $result['result'] = 'success';
        }

        echo json_encode($result);
        exit;

    }/*}}}*/


    /*
     * @param
     *  $data:
     *   id_entry
     *   id_user
     *   inte
     *   resource
     * 
     * @return
     *
     */
    public function insert_inte()
    {/*{{{*/

        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['id_entry'] = $this->input->post('id_entry');
        $data['id_user'] = $this->input->post('id_user');
        $data['inte'] = $this->input->post('inte');
        $data['resource'] = $this->input->post('resource');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        $db_result = $this->Entry_Model->insert_inte($data);

        if($db_result['result'] == 'failure')
        {
            $result['result'] = 'failure';
            $result['error_msg'] = $db_result['error_msg'];
        } else
        {
            $result['result'] = 'success';
        }

        echo json_encode($result);
        exit;

    }/*}}}*/


    /*
     * @param
     *  POST:
     *    entry_id
     *    entry_name
     *    id_user
     *    user_identity
     * 
     * @return
     *  $result:
     *    result 'failure' || 'success'
     *    error_msg
     *
     */
    public function edit_entry()
    {/*{{{*/

        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['entry_id'] = $this->input->post('entry_id');
        $data['entry_name'] = $this->input->post('entry_name');
        $data['id_user'] = $this->input->post('id_user');
        $data['user_identity'] = $this->input->post('user_identity');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        $db_result = $this->Entry_Model->edit_entry($data);

        if($db_result['result'] == 'failure')
        {
            $result['result'] = 'failure';
            $result['error_msg'] = $db_result['error_msg'];
        } else
        {
            $result['result'] = 'success';
        }

        echo json_encode($result);
        exit;

    }/*}}}*/


}

?>
