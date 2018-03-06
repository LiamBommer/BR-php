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
     *  $result['result']  
     *      empty           没有搜索结果时，返回此项empty
     *
     *  OR
     *
     *  $result             query返回的所有词条数组
     *
     */
    public function search_entry()
    {/*{{{*/

        // ajax返回结果数组
        $result = array();

        $content = $this->input->get('search_content');

        // 查询内容非空检查
        if(isset($content))
        {
            $query = $this->Entry_Model->search_entry($content);
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
     *  $entry_id
     * 
     * @return
     *  $result['result']  
     *      empty           没有搜索结果时，返回此项empty
     *
     *  OR
     *
     *  $result['inte']     query返回的所有词条数组
     *  $result['like']     每个词条的like数
     *
     */
    public function search_inte()
    {/*{{{*/

        // ajax返回结果数组
        $result = array();

        $content = $this->input->get('entry_id');

        // 查询内容非空检查
        if(isset($content))
        {
            $query = $this->Entry_Model->search_inte($content);
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
     *  $POST
     *    id_user
     *    id_interpretation
     * 
     * @return
     *  $result['result']  成功与否
     *      = 'success'
     *      = 'failure'
     *
     */
    public function like()
    {/*{{{*/

        // ajax返回结果数组
        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['id_user'] = $this->input->post('id_user');
        $data['id_inte'] = $this->input->post('id_inte');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        // validate
        // 非空

        $db_result = $this->Entry_Model->like($data);

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
     *  $entry_name
     * 
     * @return
     *  result['result']  成功与否
     *      = 'success'
     *      = 'failure'
     *
     */
    public function new_entry()
    {/*{{{*/

        // ajax返回结果数组
        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['entry_name'] = $this->input->get('entry_name');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        // validate
        // not null

        $db_result = $this->Entry_Model->new_entry($data);

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

        // validate
        // not null

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

        // validate
        // not null

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


    /*
     * @param
     *  POST:
     *    entry_id
     *    id_user
     *    user_identity
     * 
     * @return
     *  $result:
     *    result 'failure' || 'success'
     *    error_msg
     *
     */
    public function delete_entry()
    {/*{{{*/

        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['entry_id'] = $this->input->post('entry_id');
        $data['id_user'] = $this->input->post('id_user');
        $data['user_identity'] = $this->input->post('user_identity');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        // validate
        // not null

        $db_result = $this->Entry_Model->delete_entry($data);

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
     *    inte_id
     *    inte
     *    resource
     *    id_user
     *    user_identity
     * 
     * @return
     *  $result:
     *    result 'failure' || 'success'
     *    error_msg
     *
     */
    public function edit_inte()
    {/*{{{*/

        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['inte_id'] = $this->input->post('inte_id');
        $data['inte'] = $this->input->post('inte');
        $data['resource'] = $this->input->post('resource');
        $data['id_user'] = $this->input->post('id_user');
        $data['user_identity'] = $this->input->post('user_identity');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        $db_result = $this->Entry_Model->edit_inte($data);

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
     *    inte_id
     *    id_user
     *    user_identity
     * 
     * @return
     *  $result:
     *    result 'failure' || 'success'
     *    error_msg
     *
     */
    public function delete_inte()
    {/*{{{*/

        $result = array();
        $data = array();

        // 写入数据库信息填写
        $data['inte_id'] = $this->input->post('inte_id');
        $data['id_user'] = $this->input->post('id_user');
        $data['user_identity'] = $this->input->post('user_identity');
        $data['datetime'] = date('Y-m-d H:i:s', time());

        $db_result = $this->Entry_Model->delete_inte($data);

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
