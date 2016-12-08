<?
class Language
{
    public $incomplete = array('cn'=>'请把表单填写完整', 'en'=>'Please fill up all the forms than click submit');
    public $form_submit_successful = array('cn'=>'提交成功，管理员稍后查看', 'en'=>'Successful, the administrator to view the message later');
    public $form_submit_failed = array('cn'=>'提交失败，请尝试重新提交', 'en'=>'Failed, please try again wait');

    public function GetValue($str){
        if($str){
            $Array = $this->$str;
            return $Array[LANGUAGE] ? $Array[LANGUAGE] : '未找到标识符';
        };
    }
}
?>