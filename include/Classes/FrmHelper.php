<?php
namespace Classes;
use Classes\FrmModel;
use Classes\FrmImageCdn;
if(!defined('ENVIRONMENT')) die('Direct access not permitted');

class FrmHelper
{

	/**
	 * user ip address
	 * @return string
	 */
	public static function getClientIpAddr() {
		$ipaddress = '';
	     if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
	         $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	     else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	         $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	     else if(isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED']))
	         $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	     else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && !empty($_SERVER['HTTP_FORWARDED_FOR']))
	         $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	     else if(isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED']))
	         $ipaddress = $_SERVER['HTTP_FORWARDED'];
	     else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
	         $ipaddress = $_SERVER['REMOTE_ADDR'];
	     else
	         $ipaddress = '0.0.0.0';

	     return $ipaddress;
	}



	/**
	 * Modifies a string to remove all non ASCII characters and spaces.
	 */
	static public function slugify($str, $cleanMsg=false, $replace=array(), $delimiter='-')
	{
		setlocale(LC_ALL, 'en_US.UTF8');
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}

		$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		if($cleanMsg){
			$clean = preg_replace("/[^a-zA-Z0-9\[\]. -]/", '', $clean);

			setlocale(LC_ALL, 'es_ES.UTF8');
			return $clean;
		}
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		setlocale(LC_ALL, 'es_ES.UTF8');
		return $clean;
	}

	static public function formElement($type, $name, $label, $object, $help=false, $modelCondition = array())
	{
		$html = '';
		switch ($type) {
			case 'email':
				$html .= '<div class="input-prepend"><span class="add-on">@</span><input id="frm_'.$name.'" name="'.$name.'" size="16" type="text" value="'.$object->$name.'"></div>';
				break;
			case 'checkbox':
				$method = '_'.$name;
				foreach ($object->$method as $k=>$v){
					$checked = ($object->$name==$v) ? 'checked="checked"' : '';
					$html.='<label class="checkbox inline"><input type="checkbox" id="frm_'.$name.'_'.$v.'"  id="frm_'.$name.'" name="'.$name.'['.$v.']" value="'.$v.'" '.$checked.'> '.\App::translate($v,'msg').'</label>';
				}
				break;
			case 'radio':
				$method = '_'.$name;
				foreach ($object->$method as $v){
					$checked = ($object->$name==$v) ? 'checked="checked"' : '';
					$html.='<label class="checkbox inline"><input type="radio" id="frm_'.$name.'_'.$v.'"  id="frm_'.$name.'" name="'.$name.'" value="'.$v.'" '.$checked.'> '.\App::translate($v,'msg').'</label>';
				}
				break;
			case 'fileupload':
				$html .= '<input type="file" id="frm_'.$name.'" name="'.$name.'">';
                if($object->image != '' && $object->image != null){
                    $html .= '<img style="margin:10px;" width="200" src="'.self::getCdnImage($object->image).'" />';
                }
				break;
			case 'text':
				$html .='<input type="text" id="frm_'.$name.'" name="'.$name.'" value="'.$object->$name.'">';
				break;
			case 'date':
				$v = ($object->$name) ? date('Y-m-d',strtotime($object->$name)) : '';
				$html .='<input class="datepicker"  data-date-format="yyyy-mm-dd" type="text" id="frm_'.$name.'" name="'.$name.'" value="'.$v.'">';
				break;
			case 'textarea':
				$html .='<textarea id="frm_'.$name.'" name="'.$name.'">'.$object->$name.'</textarea>';
				break;
            case 'image':
                $v = $object->image(cdn_img_gallery_preview_width, cdn_img_gallery_preview_height);
                $html .= '<img src="'.$v.'" />';
                break;
            case 'image_name':
                $v = $object->filename;
                $html .= '<input class="input-xxlarge" type="text" id="frm_'.$name.'" name="'.$name.'" value="'.$v.'">';
                break;
            case 'image_name_readonly':
                $v = $object->filename;
                $html .= '<input class="input-xxlarge" type="text" id="frm_'.$name.'" name="'.$name.'" value="'.$v.'" readonly="readonly">';
                break;
            case 'model':
                $annotations = self::getAnnotation($object, $name);
                if(isset($annotations['FrmModel']))
                {
                    $model = $annotations['FrmModel'];
                    include  __DIR__."\..\Models\\$model.php";
                    $model = "\Models\\$model";
                    $objectModel = new $model();

                    $frmModel = new FrmModel();

                    $options = $frmModel->getList($objectModel, $modelCondition);

                    $html .= '<select id="frm_'.$name.'" name="'.$name.'">';
                    $html .= '<option></option>';
                    foreach($options as $option){
                        if($option[$annotations['FrmModelId']] == $object->$name)
                            $selected = "selected='selected'";
                        else
                            $selected = "";
                        $html .='<option '.$selected.' value="'.$option[$annotations['FrmModelId']].'">'.$option[$annotations['FrmModelString']].'</option>';
                    }
                    $html .= '</select>';
                }
                break;
		}

		if ( $help ) $html .= '<p class="help-block">'.$help.'</p>';

		return '<div class="control-group">'.
				'<label class="control-label" for="frm_'.$name.'">'.$label.'</label>'.
				'<div class="controls">'.
					$html.
				'</div></div>';

	}

	static public function formErrors($errors)
	{
		if ( empty($errors)) return '';
		$html = '';

		foreach ($errors as $error)
			$html .= '<li>'.\App::translate($error, 'error').'</li>';


		return	'<div class="control-group">'.
			'<label class="control-label" for="frm_errors">Errores</label>'.
			'<div class="controls"><ul>'.
			$html.
			'</ul></div></div>';

	}

    /**
     * Show an alert message with custom:
     *
     *  - type: error=>'red', success=>'green', info=>'yellow', block=>'blue'
     *  - title: 'strong' message
     *  - msg: message
     *
     * @param $type
     * @param bool $title
     * @param bool $msg
     */
    static public function formAlertAction($type, $title=false, $msg=false)
    {
        $html = '';

        switch($type){
            case 'error':
                $title = ($title) ? $title : "Error!"; // TODO >> \App::translate($title,'msg');
                $msg = ($msg) ? $msg : "Change a few things up and try submitting again.";
                break;
            case 'success':
                $title = ($title) ? $title : "Success!"; // TODO >> \App::translate($title,'msg');
                $msg = ($msg) ? $msg : "You successfully read this important alert message.";
                break;
            case 'info':
                $title = ($title) ? $title : "Oops!"; // TODO >> \App::translate($title,'msg');
                $msg = ($msg) ? $msg : "This alert needs your attention, but it's not super important.";
                break;
            case 'block':
                $title = ($title) ? $title : "Info!"; // TODO >> \App::translate($title,'msg');
                $msg = ($msg) ? $msg : "This alert needs your attention, but it's not super important.";
                break;
        }

        $html .='<div class="alert alert-'.$type.'">
                    <button class="close" data-dismiss="alert" type="button">×</button>
                    <strong>'.$title.'</strong>
                    '.$msg.'
               </div>';

        return $html;
    }

	/**
	 * Referral web site
	 */
	static public function httpReferer()
	{
		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'direct';
		if ( $referer == $_SERVER['SERVER_NAME'] || $referer == $_SERVER['SERVER_NAME'].'/') $referer = 'direct';
		return $referer;
	}

    /**
     * upload image
     * @param $input_name
     * @param $object
     */
    static public function upload($input_name, $object, $targetFolder = false)
    {
        if(!$targetFolder)
            $targetFolder = '/'.$object; // Relative to the root
        elseif(substr($targetFolder, 0) !== '/')
            $targetFolder = '/'.$targetFolder;

        if(!is_dir(public_folder.$targetFolder)){
            mkdir(public_folder.$targetFolder, 0777, true);
        }

        if (isset($_FILES[$input_name]) && isset($_FILES[$input_name]['tmp_name']) && $_FILES[$input_name]['tmp_name'] != '') {
            $tempFile = $_FILES[$input_name]['tmp_name'];
            $targetPath = public_folder.$targetFolder;
            $targetFile = rtrim($targetPath,'/') . '/' . $object->id.'_'.$_FILES[$input_name]['name'];

            // Validate the file type
            $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
            $fileParts = pathinfo($_FILES[$input_name]['name']);

            if (in_array($fileParts['extension'],$fileTypes)) {
                move_uploaded_file($tempFile,$targetFile);

                return $targetFolder.'/'.$object->id.'_'.$_FILES[$input_name]['name'];
            } else {
                return false;
            }
        }
        else return false;
    }

    static function getAnnotation($object, $var) {
        $class = new \ReflectionClass(get_class($object));
        $variable = $class->getProperty($var);
        $comments = $variable->getDocComment();
        $comments = str_replace('/*', '', $comments);
        $comments = str_replace('*/', '', $comments);
        $comments = trim(str_replace('*', '', $comments));
        $aTags = explode('@', $comments);

        $annotations = array();
        foreach($aTags as $tag){
            $keyvalue = explode(' ', $tag);
            if(count($keyvalue) >= 2)
                $annotations[trim($keyvalue[0])] = trim($keyvalue[1]);
        }

        return $annotations;
    }
    
    public static function getCdnImage($filename)
    {
    	/*$http = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    	$isDevel = strpos($_SERVER['HTTP_HOST'],'localhost');
    	$env = ( false!==$isDevel ) ? '/'.cdn_devel : '';
    	return $http . cdn_host . '/' . cdn_user . $env . '/' . $filename;*/
    }

    /**
     * Formatear string de fechas
     *
     * @param $str_datetime
     * @param bool $str_format
     * @return bool|string
     */
    static public function getFormatDatetime($str_datetime, $str_format=false)
    {
        $time = strtotime($str_datetime);
        $format = ($str_format) ? $str_format : 'd/m/Y - H:i:s';

        if( !date($format, $time) ){
             return $str_datetime;
        }

        return date($format, $time);
    }


}
?>