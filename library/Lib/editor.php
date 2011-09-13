<?php
class editor_Lib
{
	var $Height = 260;
	var $Width = 550;
	var $id = 'content';

	function __construct($id='content',$w='700',$h='260') {
		$this->id = $id;
		$this->Width = $w;
		$this->Height = $h;
	}
	
	function editor ($id='content',$w='700',$h='260') { 
		$this->id = $id;
		$this->Width = $w;
		$this->Height = $h;
	}

	function create ($text = '',$tool = 'full',$upimgext = 'jpg,jpeg,gif,png',$other='') { 
		//Separator���ָ��� BtnBr��ǿ�ƻ��� Cut������ Copy������ Paste��ճ�� Pastetext���ı�ճ�� Blocktag�������ǩ Fontface������ FontSize�������С Bold������ Italic��б�� Underline���»��� Strikethrough���л��� FontColor��������ɫ BackColor�����屳��ɫ SelectAll��ȫѡ Removeformat��ɾ�����ָ�ʽ Align������ List���б� Outdent���������� Indent���������� Link�������� Unlink��ɾ������ Img��ͼƬ Flash��Flash���� Media��Windows media player��Ƶ Emot������ Table����� Source���л�Դ����ģʽ Print����ӡ Fullscreen���л�ȫ��ģʽ 
		$appmodel = str_replace(array('/','\\'),'',APP_MODUL_NAME);
		$uploadurl = $appmodel == 'admin' ? 'http://admin'.WEB_DOMAIN.'/admin/uploadeditor.do' : BASE_URL.'/uploadeditor.do';
		if($appmodel == 'admin'){
			$editorhtml = '<script src="/xheditor/xheditor-zh-cn.min.js" type="text/javascript"></script>
			<script type="text/javascript">
			$(pageInit);
			function pageInit(){
			$("#'.$this->id.'").xheditor({tools:"'.$tool.'",skin:"default",upImgUrl:"'.$uploadurl.'",upImgExt:"'.$upimgext.'"'.($other ? ','.$other : '').'});
			}
			</script>
			<textarea id="'.$this->id.'" name="'.$this->id.'" style="width: '.$this->Width.'px;height: '.$this->Height.'px">'.$text.'</textarea>';
		}else{
			$editorhtml = '<script src="/xheditor/xheditor-zh-cn.min.js" type="text/javascript"></script>
			<script type="text/javascript">
			$(pageInit);
			function pageInit(){
			$("#'.$this->id.'").xheditor({tools:"BtnBr,Cut,Copy,Paste,Pastetext,Blocktag,Fontface,FontSize,Bold,Italic,Underline,Strikethrough,FontColor,BackColor,SelectAll,Removeformat,Align,List,Outdent,Indent,Link,Unlink",skin:"default",upImgUrl:"'.$uploadurl.'",upImgExt:"'.$upimgext.'"'.($other ? ','.$other : '').'});
			}
			</script>
			<textarea id="'.$this->id.'" name="'.$this->id.'" style="width: '.$this->Width.'px;height: '.$this->Height.'px">'.$text.'</textarea>';
		}
		return $editorhtml;
	}
}
?>