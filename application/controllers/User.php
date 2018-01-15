
<?php 

class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->Model('User_Model');
    }


    public function login()
    {/*{{{*/

        // ajax返回数组
        $ajax_result = array();

        $data['email'] = $this->input->post('email');
        $data['phone'] = $this->input->post('phone');
        $data['password'] = $this->input->post('password');


        // validate
        if($data['email'] == null && $data['phone'] == null)/*{{{*/
        {
            // 邮箱与手机号非空检验
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '邮箱与手机号不能均为空';
            echo json_encode($ajax_result);
            exit;
        }
        if($data['password'] == null)
        {
            // 密码非空检验
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '密码不能为空';
            echo json_encode($ajax_result);
            exit;
        }
        if($data['email'] != null)
        {
            // 用邮箱登录
            $data['option'] = 'email';
        }else if($data['phone'] != null)
        {
            // 用手机号登录
            $data['option'] = 'phone';
        }else
        {
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '无法获取邮箱或用户名';
            echo json_encode($ajax_result);
            exit;
        }/*}}}*/


        // database
        $row = $this->User_Model->login($data);
        if(isset($row))
        {
            /* 密码正确 */

            $ajax_result['result'] = 'success';
            $ajax_result['username'] = $row->username;
            echo json_encode($ajax_result);
            exit;
        } else 
        {
            /* 密码错误 */

            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '密码错误';
            echo json_encode($ajax_result);
            exit;
        }
    }/*}}}*/


    public function signup()
    {/*{{{*/

        $ajax_result = array();

        // get data
        $data['username'] = $this->input->post('username');
        $data['password'] = $this->input->post('password');
        $data['email'] = $this->input->post('email');
        $data['phone'] = $this->input->post('phone');
        $data['gender'] = $this->input->post('gender');
        $data['profile'] = $this->input->post('profile');
        // time format adapted to MySQL format
        $data['datetime'] = date('Y-m-d H:i:s', time());


        // validate
        if($data['username'] == null || $data['password'] == null
            || $data['gender'] == null)
        {
            // 用户名,密码, 与性别非空
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '用户名，密码或性别不能为空';
            echo json_encode($ajax_result);
            exit;
        }
        if($data['email'] == null && $data['phone'] == null)
        {
            // 邮箱与手机任一个
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '邮箱和手机号至少写一个吧！';
            echo json_encode($ajax_result);
            exit;
        }
        if($data['gender'] != 0 && $data['gender'] != 1)
        {
            // 性别内容
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '性别出错，请重试';
            echo json_encode($ajax_result);
            exit;
        }


        // database
        if($this->User_Model->signup($data))
        {
            // 注册成功
            $ajax_result['result'] = 'success';
            echo json_encode($ajax_result);
            exit;
        }else
        {
            // 注册失败
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '注册失败，请重试';
            echo json_encode($ajax_result);
            exit;
        }

    }/*}}}*/


    public function getInfo()
    {/*{{{*/

        $data['username'] = $this->input->get('username');

        if($data['username'] == null)
        {
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '获取信息失败，请重试';
            echo json_encode($ajax_result);
            exit;
        }else
        {
            $data = $this->User_Model->getInfo($data);

            $ajax_result['result'] = 'success';
            $ajax_result['data'] = $data;
            echo json_encode($ajax_result);
            exit;
        }

    }/*}}}*/


    public function pwEdit()
    {/*{{{*/

        $ajax_result = array();

        // get data
        $data['username'] = $this->input->post('username');
        $data['pw_origin'] = $this->input->post('pw_origin');
        $data['pw_new_1'] = $this->input->post('pw_new_1');
        $data['pw_new_2'] = $this->input->post('pw_new_2');

        // validate
        if($data['pw_origin']==null || $data['pw_new_1']==null
            || $data['pw_new_2']==null )
        {
            // 三个密码非空
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = '三个密码非空';
            echo json_encode($ajax_result);
            exit;
        }
        /*
         * TODO:
         *  验证新旧密码不同
         *  验证新密码相同
         *  验证新密码合法
         */


        // database
        $result = $this->User_Model->pwEdit($data);
        if($result['result'] == true)
        {
            // 注册成功
            $ajax_result['result'] = 'success';
            echo json_encode($ajax_result);
            exit;
        }else
        {
            // 注册失败
            $ajax_result['result'] = 'failure';
            $ajax_result['error_msg'] = $result['err_msg'].'。\n注册失败，请重试';
            echo json_encode($ajax_result);
            exit;
        }

    }/*}}}*/

}

?>
