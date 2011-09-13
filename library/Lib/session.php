<?php
/**
 * session������
 * ʹ�÷���
 * $mmsession = Load::lib('session');   $mmsession->initSess(); ��������ֵ�Ϳ���
 * ���ʱ�䣺2009-8-25
 */
define('SESS_LIFTTIME', 3600);


class session_Lib
{ 
    static  $sessSavePath;
    static  $sessName;
    static  $cacheObj;

    //���캯��
    public function __construct()
    {
        //����cache����
        self::$cacheObj = @Load::lib('cache','mem');
    }
    //session open����
    public function sessOpen($save_path = '', $sess_name = '')
    {
        self::$sessSavePath    = $save_path;
        self::$sessName        = $sess_name;
        return TRUE;
    }
    
    //close����
    public function sessClose()
    {
        return TRUE;
    }
    
    //��ȡ����
    public function sessRead($sessId = '')
    {
        $data = self::$cacheObj->get($sessId);
		//$data = self::$cacheObj->get($sessId,'session');//��cacheΪcache_fileʱ����

        //�ȶ����ݣ����û�У��ͳ�ʼ��һ��
        if (!empty($data))
        {
            return $data;
        }
        else
        {
            //��ʼ��һ���ռ�¼
            $ret = self::$cacheObj->set($sessId,'',SESS_LIFTTIME);
			//$ret = self::$cacheObj->set($sessId,$data,'session');//��cacheΪcache_fileʱ����
            if (TRUE != $ret)
            {
                die("Fatal Error: Session ID $sessId init failed!");
                return FALSE;
            }
            return TRUE;
        }
    }
    
    //д����
    public function sessWrite($sessId = '', $data = '')
    {
		$ret = self::$cacheObj->set($sessId,$data,SESS_LIFTTIME);
		//$ret = self::$cacheObj->set($sessId,$data,'session');//��cacheΪcache_fileʱ����
        if (TRUE != $ret)
        {
            die("Fatal Error: SessionID $sessId Save data failed!");

            return FALSE;
        }

        return TRUE;
    }
    
    //ע������
    public function sessDestroy($sessId = '')
    {
        self::sessWrite($sessId);

        return FALSE;
    }
    //���ڻ��ջ���
    public function sessGc()
    {
        //����������,cache���Լ��Ĺ��ڻ��ջ���
        return TRUE;
    }
    //��ʼ��
    public function initSess()
    {
//�� session.save_handler ����Ϊ user��������Ĭ�ϵ� files
        ini_set('session.save_handler', 'user');

        $domain = WEB_DOMAIN;
        //��ʹ�� GET/POST ������ʽ
        ini_set('session.use_trans_sid',    0);

        //�������������������ʱ��
        ini_set('session.gc_maxlifetime',   SESS_LIFTTIME);

        //ʹ�� COOKIE ���� SESSION ID �ķ�ʽ
        ini_set('session.use_cookies',      1);
        ini_set('session.cookie_path',      '/');

        //������������ SESSION ID �� COOKIE
        ini_set('session.cookie_domain',    $domain);


        //���� SESSION �����������Ӧ�ķ�������
        session_set_save_handler(
                array(&$this, 'sessOpen'),   
                array(&$this, 'sessClose'),
                array(&$this, 'sessRead'),
                array(&$this, 'sessWrite'),
                array(&$this, 'sessDestroy'),
                array(&$this, 'sessGc')
                );

		session_start();
		header("Cache-control: private");
		return TRUE;
	}
}
?>