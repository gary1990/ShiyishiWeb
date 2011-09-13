<?php
/****
* SMTP ���ŷ�����
*/
//error_reporting(E_ALL);
class mtsms_Model extends Model{
	
	function init(){
		$this->mtsms = $this->conf = Load::conf('mtsms');
		$this->mtsmso = new SoapClient($this->mtsms['url']);//����
	}
	/***
	 * ���ŷ���  (ע:�˷�������Ϊ�ѵ�¼״̬�·��ɲ���)
	 * 
	 * @param string $mobiles		�ֻ���, �� 159xxxxxxxx,�����Ҫ����ֻ���Ⱥ��,159xxxxxxxx,159xxxxxxx2 
	 * @param string $content		�������� GBK
	 * @param string $sendTime		��ʱ����ʱ�䣬��ʽΪ yyyymmddHHiiss, ��Ϊ ����������������ʱʱ�ַ�����,����:20090504111010 ����2009��5��4�� 11ʱ10��10��
	 * 								�������Ҫ��ʱ���ͣ���Ϊ'' (Ĭ��)
	 */
	function send($mobiles,$smscontent,$sendtime=''){
		$message = array('phoneNumber'=>$mobiles,'content'=>$smscontent,);
		//$content = file_get_contents('http://3tong.cn:8082/ema_new/http/SendSms?Account=88010110&Password=cbff36039c3d0212b3e34c23dcde1456&SubCode=&Phone=13811058234&Content=adsfassda sdf ');
		$res = $this->mtsmso->sendSms($this->mtsms['account'],$this->mtsms['password'], $message,$sendResMsg, $errMsg);
		if($res->errMsg > -1){
			$this->logstr("����".$smscontent."��".$mobiles."�ɹ�");
			return 'success';
		}else{
			$this->logstr("����".$smscontent."��".$mobiles."ʧ��");
			return 'fail';
		}
	}

	function httpsend($phone,$str){
		$content = file_get_contents('http://3tong.cn:8082/ema_new/http/SendSms?Account='.$this->mtsms['account'].'&Password='.$this->mtsms['password'].'&SubCode=&Phone='.$phone.'&Content='.$str);
		if($content>0){
			return 'success';
		}else{
			return 'fail';
		}
	}


	function logstr($str){
		$james=fopen($this->mtsms['logName'],"a+");
		fwrite($james,"\r\n".date("Y-m-d H:i:s").$str);
		fclose($james);
	}
}
?>