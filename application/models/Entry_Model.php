<?php

class Entry_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

    }


    /*
     * @param 
     *  $content    要搜索的词条名
     * 
     * @return
     *  $row || FALSE
     *
     */
    public function search_entry($content)
    {/*{{{*/

        if(isset($content))
        {
            $result = array();

            // 搜索符合条件的词条
            $query_entry = $this->db->select('*')
                        ->from('entry')
                        ->like('name', $content)
                        ->get();

            $result = $query_entry->result();

            if(!isset($result))
            {
                return FALSE;

            } else 
            {
                return $result;

            }

        } else
        {
            return FALSE;
        }
    }/*}}}*/


    /*
     * @param 
     *  $content    要搜索释义的词条id
     * 
     * @return
     *  $row || FALSE
     *
     */
    public function search_inte($content)
    {/*{{{*/

        if(isset($content))
        {
            $result = array();

            // 搜索符合条件的释义
            $query_inte = $this->db->select('interpretation.*, user.username')
                    ->from('interpretation')
                    ->where('id_entry', $content)
                    ->join('user', 'interpretation.id_user=user.id_user')
                    ->get();

            $result['inte'] = $query_inte->result();

            // 搜索每个词条的点赞数
            $sql_like = "select id_interpretation, sum(like_status)  as like_total".
                        " from `like` group by id_interpretation order by like_total desc";

            $query_like = $this->db->query($sql_like);
            $result['like'] = $query_like->result();


            if(!isset($result['inte']))
            {
                return FALSE;

            } else 
            {
                return $result;

            }

        } else
        {
            return FALSE;
        }
    }/*}}}*/


    /*
     * @param
     *  $data['id_user']
     *  $data['id_inte']
     *  $data['datetime']
     *
     * @return
     *  $result['result']
     *      'success'
     *      'failure'
     *  $result['error_msg']
     */
    public function like($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['id_user']) && isset($data['id_inte']))
        {
            // 先查询是否有过点赞或点灭记录
            $query = $this->db->select('like_status')
                    ->from('like')
                    ->where('id_user', $data['id_user'])
                    ->where('id_interpretation', $data['id_inte'])
                    ->get();

            $row = $query->row();

            if(isset($row))
            {
                $like_history = $row->like_status;

                // 已经点过赞,提示
                if($like_history == 1)
                {
                    $result['result'] = 'failure';
                    $result['error_msg'] = '你已点过赞！';
                    return $result;

                // 之前点过灭,删除记录
                }else if($like_history == -1)
                {
                    $this->db->delete('like', array('id_user'=>$data['id_user'], 'id_interpretation'=>$data['id_inte']));
                }
            } else
            {
                // 未有过记录，进行插入点赞
                $insert_data = array(
                    'id_user' => $data['id_user'],
                    'id_interpretation' => $data['id_inte'],
                    'like_status' => 1,
                    'datetime' => $data['datetime']
                );
                $this->db->insert('like', $insert_data);

                $result['result'] = 'success';
                return $result;
            }

        } else
        {
            $result['result'] = 'failure';
            $result['error_msg'] = '用户或词条id不存在，写入错误';
            return $result;
        }
    }/*}}}*/


    /*
     * @param
     *  $data['id_user']
     *  $data['id_inte']
     *  $data['datetime']
     *
     * @return
     *  $result['result']
     *      'success'
     *      'failure'
     *  $result['error_msg']
     */
    public function dislike($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['id_user']) && isset($data['id_inte']))
        {
            // 先查询是否有过点赞或点灭记录
            $query = $this->db->select('like_status')
                    ->from('like')
                    ->where('id_user', $data['id_user'])
                    ->where('id_interpretation', $data['id_inte'])
                    ->get();

            $row = $query->row();

            if(isset($row))
            {
                $like_history = $row->like_status;

                // 已经点过赞,删除记录
                if($like_history == 1)
                {
                    $this->db->delete('like', array('id_user'=>$data['id_user'], 'id_interpretation'=>$data['id_inte']));

                // 之前点过灭,提示
                }else if($like_history == -1)
                {
                    $result['result'] = 'failure';
                    $result['error_msg'] = '你已点过灭！';
                    return $result;

                } 
            }else
            {
                // 未有过记录，进行插入点灭
                $insert_data = array(
                    'id_user' => $data['id_user'],
                    'id_interpretation' => $data['id_inte'],
                    'like_status' => -1,
                    'datetime' => $data['datetime']
                );
                $this->db->insert('like', $insert_data);

                $result['result'] = 'success';
                return $result;
            }


        } else
        {
            $result['result'] = 'failure';
            $result['error_msg'] = '用户或词条id不存在，写入错误';
            return $result;
        }
    }/*}}}*/


    /*
     * @param
     *  $data['entry_name']
     *  $data['entry_id']
     *
     * @return
     *  $result['result']
     *      'success'
     *      'failure'
     *  $result['error_msg']
     */
    public function new_entry_request($data)
    {/*{{{*/

        $result = array();

        // 查询词条是否存在
        $query = $this->db->select('is_open','request')
                ->where('name', $data['entry_name'])
                ->or_where('id_entry', $data['entry_id'])
                ->get('entry');

        $row = $query->row();

        // 词条存在
        if(isset($row))
        {
            // 差1次请求即可开放词条
            if($row->request >= 9)
            {
                // 开放词条并增加请求
                $this->db->set('is_open', 1)
                    ->set('request', 'request+1', false)
                    ->where('name', $data['entry_name'])
                    ->or_where('id_entry', $data['entry_id'])
                    ->update('entry');

            }else
            {
                // 单纯增加请求
                $this->db->set('request', 'request+1', false)
                    ->where('name', $data['entry_name'])
                    ->or_where('id_entry', $data['entry_id'])
                    ->update('entry');

                $result['result'] = 'success';
                return $result;
            }

        } else
        {
            // 词题不存在
            $result['result'] = 'failure';
            $result['error_msg'] = '词条不存在';
            return $result;
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
    public function new_entry($data)
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
                    'is_open' => 1,
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

            // 查询词条与用户是否存在
            $query_entry = $this->db->select('is_open')
                    ->where('id_entry', $data['id_entry'])
                    ->get('entry');
            $row_entry = $query_entry->row();
            $query_user = $this->db->select('id_user')
                    ->where('id_user', $data['id_user'])
                    ->get('user');
            $row_user = $query_user->row();

            if(!isset($row_entry))
            {
                // 词条不存在
                $result['result'] = 'failure';
                $result['error_msg'] = '词条不存在';
                return $result;

            } else if(!isset($row_user))
            {
                // 用户不存在
                $result['result'] = 'failure';
                $result['error_msg'] = '用户不存在，请尝试重新登陆';
                return $result;

            } else if($row_entry->is_open == 0)
            {
                // 查询词条是否已开放
                
                    // 词条未开放，不允许插入释义
                    $result['result'] = 'failure';
                    $result['error_msg'] = '词条暂未开放，无法插入释义';
                    return $result;

            }else
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
            $query_entry = $this->db->select('id_entry')
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


    /*
     * @param
     *  $data:
     *    entry_id
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
    public function delete_entry($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['entry_id']) && isset($data['user_identity'])
            && isset($data['id_user']))
        {

            // 查询词条及用户是否存在
            $query_entry = $this->db->select('id_entry')
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
                // 确认删除词条
                $this->db->where('id_entry', $data['entry_id'])
                    ->delete('entry');

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


    /*
     * @param
     *  $data:
     *    inte_id
     *    inte
     *    resource
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
    public function edit_inte($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['inte_id']) && isset($data['inte'])
            && isset($data['id_user']) && isset($data['user_identity']))
        {

            // 查询释义及用户是否存在
            $query_inte = $this->db->select('id_interpretation')
                        ->where('id_interpretation', $data['inte_id'])
                        ->get('interpretation');
            $row_inte = $query_inte->row();
            $query_user = $this->db->select('id_user')
                        ->where('id_user', $data['id_user'])
                        ->get('user');
            $row_user = $query_user->row();

            /*
             * TODO:
             *  检测用户身份权限
             */

            // 词条不存在
            if(!isset($row_inte))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '释义不存在';
                return $result;

            } else if(!isset($row_user))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '用户不存在，请尝试重新登陆';
                return $result;

            } else
            {
                // 释义及用户均存在
                // 修改释义
                $edit_data = array(
                    'id_user' => $data['id_user'],
                    'interpretation' => $data['inte'],
                    'resource' => $data['resource'],
                    'datetime' => $data['datetime']
                );
                $this->db->set($edit_data)
                    ->where('id_interpretation', $data['inte_id'])
                    ->update('interpretation');

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


    /*
     * @param
     *  $data:
     *    inte_id
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
    public function delete_inte($data)
    {/*{{{*/

        $result = array();

        // 参数非空检查
        if(isset($data['inte_id']) && isset($data['user_identity'])
            && isset($data['id_user']))
        {

            // 查询释义及用户是否存在
            $query_inte = $this->db->select('id_interpretation')
                        ->where('id_interpretation', $data['inte_id'])
                        ->get('interpretation');
            $row_inte = $query_inte->row();
            $query_user = $this->db->select('id_user')
                        ->where('id_user', $data['id_user'])
                        ->get('user');
            $row_user = $query_user->row();

            /*
             * TODO:
             *  检测用户身份权限
             */

            // 词条不存在
            if(!isset($row_inte))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '释义不存在';
                return $result;

            } else if(!isset($row_user))
            {
                $result['result'] = 'failure';
                $result['error_msg'] = '用户不存在，请尝试重新登陆';
                return $result;

            } else
            {
                // 释义及用户均存在
                // 确认删除释义
                $this->db->where('id_interpretation', $data['inte_id'])
                    ->delete('interpretation');

                $result['result'] = 'success';
                return $result;
            }


        } else
        {
            $result['result'] = 'failure';
            $result['error_msg'] = '释义id，用户id需要非空，写入错误';
            return $result;
        }
    }/*}}}*/





}

?>
