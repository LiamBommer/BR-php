
<?php

class User_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    /*
     * 验证用户密码登录
     *
     * @param $data
     *  $data['email']
     *  $data['password']
     *  $data['option']
     *
     * 正确: return username, password
     * 错误: return FALSE
     */
    public function login($data)
    {/*{{{*/
        
        $option;
        if($data['option'] == 'email')
        {
            // 用邮箱登录
            $option = 'email';
        }else if($data['option'] == 'phone')
        {
            // 用手机登录
            $option = 'phone';
        }

        $query = $this->db->select('*')
                ->from('user')
                ->where("$option", $data["$option"])
                ->where('password', $data['password'])
                ->join('userInfo', 'userInfo.id_user=user.id_user')
                ->get();

        $row = $query->row();

        if(isset($row))
        {
            /* 结果不为空，密码正确 */

            return $row;
        } else
        {
            /* 结果为空，密码错误 */

            return FALSE;
        }
    }/*}}}*/


    /*
     * 查询邮箱是否存在
     *
     * @param 
     *  $email
     *
     * @return
     *  true | false
     */
    public function is_email_existed($email)
    {/*{{{*/

        if(isset($email))
        {
            $query = $this->db->select('id_user')
                    ->from('user')
                    ->where('email', $email)
                    ->get();
            
            $row = $query->row();

            if(isset($row))
            {
                return true;
            }else
            {
                return false;
            }
        }

    }/*}}}*/

    /*
     * 查询手机是否存在
     *
     * @param 
     *  $phone
     *
     * @return
     *  true | false
     */
    public function is_phone_existed($phone)
    {/*{{{*/

        if(isset($phone))
        {
            $query = $this->db->select('id_user')
                    ->from('user')
                    ->where('phone', $phone)
                    ->get();
            
            $row = $query->row();

            if(isset($row))
            {
                return true;
            }else
            {
                return false;
            }
        }

    }/*}}}*/

    /*
     * 查询用户名是否存在
     *
     * @param 
     *  $username
     *
     * @return
     *  true | false
     */
    public function is_username_existed($username)
    {/*{{{*/

        if(isset($username))
        {
            $query = $this->db->select('id_user')
                    ->from('user')
                    ->where('username', $username)
                    ->get();
            
            $row = $query->row();

            if(isset($row))
            {
                return true;
            }else
            {
                return false;
            }
        }

    }/*}}}*/


    /*
     * 注册用户
     *
     * @param $data
     *  $data['username']
     *  $data['password']
     *  $data['email']
     *  $data['phone']
     *  $data['gender']
     *  $data['profile']
     *  $data['datetime']
     *
     * 正确: return TRUE
     * 错误: return FALSE
     */
    public function signup($data)
    {/*{{{*/

        $result = array();

        // 插入user表
        $sql_to_user = "INSERT INTO user(email, phone, password, username)
            VALUES(".$this->db->escape($data['email']).", ".$this->db->escape($data['phone'])."
            , ".$this->db->escape($data['password']).", ".$this->db->escape($data['username']).")";

        $query= $this->db->query($sql_to_user);


        // 获取 id_user
        $sql_for_id = "SELECT id_user FROM user 
            WHERE username=".$this->db->escape($data['username']);

        $query= $this->db->query($sql_for_id);
        $row = $query->row();

        if(isset($row))
        {
            $id_user = $row->id_user;
        }else
        {
            $result['result'] = 'failure';
            $result['error_msg'] = '';
            return $result;
        }


        // 插入userInfo表
        $sql_to_userInfo = "INSERT INTO userInfo(id_user, username, gender, profile, datetime)
            VALUES(".$this->db->escape($id_user).", ".$this->db->escape($data['username'])."
            , ".$this->db->escape($data['gender']).", ".$this->db->escape($data['profile'])."
            , ".$this->db->escape($data['datetime']).")";

        $query= $this->db->query($sql_to_userInfo);
        
        $result['result'] = 'success';
        return $result;


    }/*}}}*/


    /*
     * 查询个人信息
     *
     * @param $data
     *  $data['username']
     *
     * return
     *  $result['email'] 
     *  $result['phone']
     *  $result['identity'] 
     *  $result['gender'] 
     *  $result['profile']
     *
     * 正确: return 用户信息数组
     * 错误: return FALSE
     */
    public function getInfo($data)
    {/*{{{*/

        $sql_user = "SELECT email, phone, identity FROM user
                WHERE username = ".$this->db->escape($data['username']).";";

        $sql_userInfo = "SELECT gender, profile FROM userInfo
                WHERE username = ".$this->db->escape($data['username']).";";

        $query_user = $this->db->query($sql_user);
        $query_userInfo = $this->db->query($sql_userInfo);

        $row_user = $query_user->row();
        $row_userInfo = $query_userInfo->row();

        $result = array();

        if(isset($row_user) Or isset($row_userInfo))
        {
            $result['email'] = $row_user->email;
            $result['phone'] = $row_user->phone;
            $result['identity'] = $row_user->identity;
            $result['gender'] = $row_userInfo->gender;
            $result['profile'] = $row_userInfo->profile;

            return $result;
        }else
        {
            return false;
        }

    }/*}}}*/


    /*
     * 修改密码
     *
     * @param $data
     *  $data['username']
     *  $data['pw_origin']
     *  $data['pw_new_1']
     *  $data['pw_new_2']
     *
     * return
     *  $result array
     *
     *
     * 正确: return $result['result'] = true
     * 错误: return $result['result'] = false
     *       return $result['err_msg']
     */
    public function pwEdit($data)
    {/*{{{*/

        // 查询原密码是否正确
        $sql_origin = "SELECT password FROM user
            WHERE username=".$this->db->escape($data['username']).";";

        $query= $this->db->query($sql_origin);
        $row = $query->row();

        $result = array();
        $result['result'] = false;
        if(!isset($row))
        {
            $result['result'] = false;
            return $result;
        }else
        {
            if($row->password != $data['pw_origin'])
            {
                $result['result'] = false;
                $result['err_msg'] = '原密码不正确';
                return $result;
            }
        }

        // 更新密码
        $sql_new = "UPDATE user SET password=".$this->db->escape($data['pw_new_1'])."
            WHERE username=".$this->db->escape($data['username']).";";

        $query_new = $this->db->query($sql_new);

        if($this->db->affected_rows() != 1)
        {
            $result['result'] = false;
            return $result;
        }else
        {
            $result['result'] = true;
            return $result;
        }

    }/*}}}*/


    public function search_user($id_user)
    {
        if(isset($id_user))
        {
            $query = $this->db->select('*')
                    ->from('user')
                    ->where('id_user', $id_user)
                    ->get();

            $row = $query->row();

            if(!isset($row))
            {
                return FALSE;

            } else 
            {
                return $row;

            }

        } else
        {
            return false;
        }

    }


}

?>
