<?php
/** 
* ���䣬֧�ֶϵ������� 
* 2g���ϳ����ļ�Ҳ��Ч 
* @author Guoli 
*/
class download_Lib { 
/** 
* ���嵥Ԫ 
*/
	const BUFF_SIZE = 5120; // 1024 * 5 
	/** 
	* �ļ���ַ 
	* @var <String> 
	*/
	private $filePath; 
	/**
	* �ļ���С
	*/
	private $fileSize; 
	/** 
	* �ļ����� 
	* @var <String> 
	*/
	private $mimeType; 
	/** 
	* �������򣨷�Χ�� 
	* @var <String> 
	*/
	private $range; 
	/** 
	* �Ƿ�д����־ 
	* @var <Boolean> 
	*/
	private $isLog = false; 

	/** 
	* 
	* @param <String> $filePath �ļ�·�� 
	* @param <String> $mimeType  �ļ����� 
	* @param <String> $range �������򣨷�Χ�� 
	*/
	function __construct($filePath, $range = null) { 
		$this->filePath = $filePath; 
		$this->fileSize = sprintf('%u',filesize($filePath));
		$mimeType = $this->mimes_types(substr($filePath,strrpos($filePath,".")+1));
		//echo $mimeType;exit;
		$this->mimeType = ($mimeType != null)?$mimeType:"application/octet-stream";
		$this->range = trim($range); 
	}
	/** 
	*  ��ȡ�ļ����� 
	* @return <Map> {'start':long,'end':long} or null 
	*/
	private function getRange() { 
		/** 
		*  Range: bytes=-128 
		*  Range: bytes=-128 
		*  Range: bytes=28-175,382-399,510-541,644-744,977-980 
		*  Range: bytes=28-175n380 
		*  type 1 
		*  RANGE: bytes=1000-9999 
		*  RANGE: bytes=2000-9999 
		*  type 2 
		*  RANGE: bytes=1000-1999 
		*  RANGE: bytes=2000-2999 
		*  RANGE: bytes=3000-3999 
		*/
		if (!empty($this->range)) { 
			$range = preg_replace('/[s|,].*/','',$this->range); 
			$range = explode('-',substr($range,6)); 
			if (count($range) < 2 ) { 
				$range[1] = $this->fileSize; // Range: bytes=-100 
			} 
			$range = array_combine(array('start','end'),$range); 
			if (empty($range['start'])) { 
				$range['start'] = 0; 
			} 
			if (!isset ($range['end']) || empty($range['end'])) { 
				$range['end'] = $this->fileSize; 
			} 
			return $range; 
		} 
		return null; 
	} 
	/** 
	* ��ͻ��˷����ļ� 
	*/
	public function send() { 
		$fileHande = fopen($this->filePath, 'rb'); 
		if ($fileHande) { 
			// setting 
			ob_end_clean();// clean cache 
			ob_start(); 
			ini_set('output_buffering', 'Off'); 
			ini_set('zlib.output_compression', 'Off'); 
			$magicQuotes = get_magic_quotes_gpc(); 
			set_magic_quotes_runtime(0); 
			// init 
			$lastModified = gmdate('D, d M Y H:i:s', filemtime($this->filePath)).' GMT'; 
			$etag = sprintf('w/"%s:%s"',md5($lastModified),$this->fileSize); 
			$ranges = $this->getRange(); 
			// headers 
			header(sprintf('Last-Modified: %s',$lastModified)); 
			header(sprintf('ETag: %s',$etag)); 
			header(sprintf('Content-Type: %s',$this->mimeType)); 
			$disposition = 'attachment'; 
			if (strpos($this->mimeType,'image/') !== FALSE) { 
				$disposition = 'inline'; 
			}
			header(sprintf('Content-Disposition: %s; filename="%s"',$disposition,basename($this->filePath))); 

			if ($ranges != null) { 
				if ($this->isLog) { 
					$this->log($ranges.' '.$_SERVER['HTTP_RANGE']); 
				} 
				header('HTTP/1.1 206 Partial Content'); 
				header('Accept-Ranges: bytes'); 
				header(sprintf('Content-Length: %u',$ranges['end'] - $ranges['start'])); 
				header(sprintf('Content-Range: bytes %s-%s/%s', $ranges['start'], $ranges['end'],$this->fileSize)); 
				fseek($fileHande, sprintf('%u',$ranges['start'])); 
			}else { 
				header("HTTP/1.1 200 OK"); 
				header(sprintf('Content-Length: %s',$this->fileSize)); 
			}
			// read file 
			$lastSize = 0; 
			while(!feof($fileHande) && !connection_aborted()) { 
				$lastSize = sprintf("%u", bcsub($this->fileSize,sprintf("%u",ftell($fileHande)))); 
				if (bccomp($lastSize,self::BUFF_SIZE) > 0) { 
					$lastSize = self::BUFF_SIZE; 
				} 
				echo fread($fileHande, $lastSize); 
				flush(); 
				ob_flush(); 
			} 
			set_magic_quotes_runtime($magicQuotes); 
			ob_end_flush(); 
		} 
		if ($fileHande != null) { 
			fclose($fileHande); 
		} 
	} 
	function mimes_types($mime){
		$mimes = array(
			'hqx'	=>	'application/mac-binhex40',
			'cpt'	=>	'application/mac-compactpro',
			'csv'	=>	'application/vnd.msexcel',
			'bin'	=>	'application/macbinary',
			'dms'	=>	'application/octet-stream',
			'lha'	=>	'application/octet-stream',
			'lzh'	=>	'application/octet-stream',
			'exe'	=>	'application/octet-stream',
			'rar'	=>	'application/octet-stream',
			'class'	=>	'application/octet-stream',
			'psd'	=>	'application/x-photoshop',
			'so'	=>	'application/octet-stream',
			'sea'	=>	'application/octet-stream',
			'dll'	=>	'application/octet-stream',
			'oda'	=>	'application/oda',
			'pdf'	=>	'application/pdf',
			'ai'	=>	'application/postscript',
			'eps'	=>	'application/postscript',
			'ps'	=>	'application/postscript',
			'smi'	=>	'application/smil',
			'smil'	=>	'application/smil',
			'mif'	=>	'application/vnd.mif',
			'xls'	=>	'application/excel',
			'ppt'	=>	'application/powerpoint',
			'wbxml'	=>	'application/wbxml',
			'wmlc'	=>	'application/wmlc',
			'dcr'	=>	'application/x-director',
			'dir'	=>	'application/x-director',
			'dxr'	=>	'application/x-director',
			'dvi'	=>	'application/x-dvi',
			'gtar'	=>	'application/x-gtar',
			'gz'	=>	'application/x-gzip',
			'php'	=>	'application/x-httpd-php',
			'php4'	=>	'application/x-httpd-php',
			'php3'	=>	'application/x-httpd-php',
			'phtml'	=>	'application/x-httpd-php',
			'phps'	=>	'application/x-httpd-php-source',
			'js'	=>	'application/x-javascript',
			'swf'	=>	'application/x-shockwave-flash',
			'sit'	=>	'application/x-stuffit',
			'tar'	=>	'application/x-tar',
			'tgz'	=>	'application/x-tar',
			'xhtml'	=>	'application/xhtml+xml',
			'xht'	=>	'application/xhtml+xml',
			'zip'	=>  'application/x-zip',
			'mid'	=>	'audio/midi',
			'midi'	=>	'audio/midi',
			'mpga'	=>	'audio/mpeg',
			'mp2'	=>	'audio/mpeg',
			'mp3'	=>	'audio/mpeg',
			'aif'	=>	'audio/x-aiff',
			'aiff'	=>	'audio/x-aiff',
			'aifc'	=>	'audio/x-aiff',
			'ram'	=>	'audio/x-pn-realaudio',
			'rm'	=>	'audio/x-pn-realaudio',
			'rpm'	=>	'audio/x-pn-realaudio-plugin',
			'ra'	=>	'audio/x-realaudio',
			'rv'	=>	'video/vnd.rn-realvideo',
			'wav'	=>	'audio/x-wav',
			'bmp'	=>	'image/bmp',
			'gif'	=>	'image/gif',
			'jpeg'	=>	'image/jpeg',
			'jpg'	=>	'image/jpeg',
			'jpe'	=>	'image/jpeg',
			'png'	=>	'image/png',
			'tiff'	=>	'image/tiff',
			'tif'	=>	'image/tiff',
			'css'	=>	'text/css',
			'html'	=>	'text/html',
			'htm'	=>	'text/html',
			'shtml'	=>	'text/html',
			'txt'	=>	'text/plain',
			'text'	=>	'text/plain',
			'log'	=>	'text/x-log',
			'rtx'	=>	'text/richtext',
			'rtf'	=>	'text/rtf',
			'xml'	=>	'text/xml',
			'xsl'	=>	'text/xml',
			'mpeg'	=>	'video/mpeg',
			'mpg'	=>	'video/mpeg',
			'mpe'	=>	'video/mpeg',
			'qt'	=>	'video/quicktime',
			'mov'	=>	'video/quicktime',
			'avi'	=>	'video/x-msvideo',
			'movie'	=>	'video/x-sgi-movie',
			'doc'	=>	'application/msword',
			'docx'	=>	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'xlsx'	=>	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'word'	=>	'application/msword',
			'xl'	=>	'application/excel',
			'eml'	=>	'message/rfc822'
		);
        return (!isset($mimes[$mime])) ? FALSE : $mimes[$mime];
    }
	/** 
	* ���ü�¼ 
	* @param <Boolean> $isLog  �Ƿ��¼ 
	*/
	public function setIsLog($isLog = true) { 
		$this->isLog = $isLog; 
	} 
	/** 
	* ��¼ 
	* @param <String> $msg  ��¼��Ϣ 
	*/
	private function log($msg) { 
		try { 
			$handle = fopen('transfer_log.txt', 'a'); 
			fwrite($handle, sprintf('%s : %s',date('Y-m-d H:i:s'),$msg)); 
			fclose($handle); 
		}catch(Exception $e) { 
			// null; 
		} 
	} 
}
?>