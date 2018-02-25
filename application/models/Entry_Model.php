<?php

class Entry_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

    }


    public function search($content)
    {/*{{{*/

        if(isset($content))
        {
            $result = array();

            // 搜索符合条件的词条
            $query_entry = $this->db->select('*')
                        ->from('entry')
                        ->like('entry.name', $content)
                        ->get();

            $row = $query_entry->row();
            if(!isset($row))
            {
                return FALSE;
            }

            // 搜索每个词条的所有释义
            foreach($query_entry->result() as $row_entry)
            {
                // 词条存入数组
                $result["entry"][] = $row_entry;

                $query_inte = $this->db->select('*')
                        ->from('interpretation')
                        ->where('id_entry', $row_entry->id_entry)
                        ->get();

                // 将词条的释义遍历并存入数组
                foreach($query_inte->result() as $row_inte)
                {
                    $result["inte"][] = $row_inte;
                }
            }

            return $result;

        } else
        {
            return FALSE;
        }
    }/*}}}*/


    /*
     * @param
     *  $data['entry_name')
     *  $data['datetime']
     *
     * @return
     *  $result['result']
     *      'success'
     *      'failure'
     *  $result['error_msg']
     */
    public function create($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['entry_name']) && isset($data['datetime']))
        {

            // 查询词条是否存在
            $query = $this->db->select('id_entry','name')
                    ->where('name', $data['entry_name'])
                    ->get('entry');
            $row = $query->row();

            // 词条已存在
            if(isset($row))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '词条已存在';
                return $result;

            } else
            {
                // 词题不存在
                // 插入词条
                $insert_data = array(
                    'name' => $data['entry_name'],
                    'datetime' => $data['datetime']
                );
                $this->db->insert('entry', $insert_data);

                $result['result'] = 'success';
                return $result;
            }


        } else
        {
            $result['result'] = 'failure';
            $result['error_msg'] = '词条名或日期不存在，写入错误';
            return $result;
        }
    }/*}}}*/


    /*
     * @param
     *  $data:
     *   id_entry
     *   id_user
     *   inte
     *   resource
     *   datetime
     * 
     * @return
     *  $result['result']
     *      'success'
     *      'failure'
     *  $result['error_msg']
     *
     */
    public function insert_inte($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['id_entry']) && isset($data['id_user'])
            && isset($data['inte']))
        {

            // 查询词条是否存在
            $query_entry = $this->db->select('id_entry')
                    ->where('id_entry', $data['id_entry'])
                    ->get('entry');
            $row_entry = $query_entry->row();
            $query_user = $this->db->select('id_user')
                    ->where('id_user', $data['id_user'])
                    ->get('user');
            $row_user = $query_user->row();
            // 词条不存在
            if(!isset($row_entry))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '词条不存在';
                return $result;

            } else if(!isset($row_user))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '用户不存在，请尝试重新登陆';
                return $result;

            } else
            {
                // 词条及用户均存在
                // 插入释义至词条
                $insert_data = array(
                    'id_entry' => $data['id_entry'],
                    'id_user' => $data['id_user'],
                    'interpretation' => $data['inte'],
                    'resource' => $data['resource'],
                    'datetime' => $data['datetime']
                );
                $this->db->insert('interpretation', $insert_data);

                $result['result'] = 'success';
                return $result;
            }


        } else
        {
            $result['result'] = 'failure';
            $result['error_msg'] = '词条id，用户id及释义需要非空，写入错误';
            return $result;
        }
    }/*}}}*/
    

    /*
     * @param
     *  $data:
     *    entry_id
     *    entry_name
     *    id_user
     *    user_identity
     * 
     * @return
     *  $result['result']
     *      'success'
     *      'failure'
     *  $result['error_msg']
     *
     */
    public function edit_entry($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['entry_id']) && isset($data['entry_name'])
            && isset($data['id_user']) && isset($data['user_identity']))
        {

            // 查询词条及用户是否存在
            $query_entry = $this->db->select('id_entry', 'name')
                        ->where('id_entry', $data['entry_id'])
                        ->get('entry');
            $row_entry = $query_entry->row();
            $query_user = $this->db->select('id_user')
                        ->where('id_user', $data['id_user'])
                        ->get('user');
            $row_user = $query_user->row();

            /*
             * TODO:
             *  检测用户身份权限
             */

            // 词条不存在
            if(!isset($row_entry))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '词条不存在';
                return $result;

            } else if(!isset($row_user))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '用户不存在，请尝试重新登陆';
                return $result;

            } else
            {
                // 词条及用户均存在
                // 插入释义至词条
                $edit_data = array(
                    'name' => $data['entry_name'],
                    'datetime' => $data['datetime']
                );
                $this->db->set($edit_data)
                    ->where('id_entry', $data['entry_id'])
                    ->update('entry');

                $result['result'] = 'success';
                return $result;
            }


        } else
        {
            $result['result'] = 'failure';
            $result['error_msg'] = '词条id，用户id及词条名需要非空，写入错误';
            return $result;
        }
    }/*}}}*/


}

?>
