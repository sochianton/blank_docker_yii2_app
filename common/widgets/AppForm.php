<?php


namespace common\widgets;


use kartik\form\ActiveForm;
use Yii;
use yii\base\InvalidCallException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;


class AppForm extends ActiveForm
{

    public $box;
    public $generateSbmtBtn = true;
    public $requireConfirm = true;

    public function init()
    {

        parent::init();

        if(false AND $this->requireConfirm){
            $this->options['data-require-confirm'] = true;
            $this->registerJs();
        }

    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        $content = ob_get_clean();
        $begin = Html::beginForm($this->action, $this->method, $this->options);
        $end = Html::endForm();

        $btns = '';
        if($this->generateSbmtBtn){
            $btns = self::generateSubmitButton();
        }




        if ($this->enableClientScript) {
            $this->registerClientScript();
        }

        if(isset($this->box) AND is_array($this->box)){

            $conf = ArrayHelper::merge([
                'class' => BoxWidget::class,
                'beginForm' => $begin,
                'content' => $content,
                'footer' => $btns,
                'endForm' => $end,
            ], $this->box);

            /** @var  $box BoxWidget */
            $box = \Yii::createObject($conf);

            return $box->run();
        }

        return $begin.$content.$btns.$end;


    }

    public function registerJs(){
        $this->view->registerJs(<<<JS
        
function ValidateForm(){
    
    var formChanged = false;
    
    const form = document.querySelector('form[data-require-confirm]');
    if (!form) return;
    
    console.log({form:form});
    console.log({formChanged:formChanged});
    
    /** Select2 support **/
    
    const _jQuery = window.$ || window.jQuery;
    
    if (_jQuery) {
        [].forEach.call(form.querySelectorAll('span.select2'), function(el) {
          const select = el.parentNode.querySelector('select');
        
          _jQuery(select).on('change', function() {
              formChanged = true;
          });
        });
    }
    
    form.addEventListener('submit', function (e) {
        formChanged = false;
    });
    
    window.addEventListener('beforeunload', function (e) {
    if (!formChanged) {
      return true;
    }
    
    
    var confirmationMessage = 'It looks like you have been editing something. '
        + 'If you leave before saving, your changes will be lost.';
    
    (e || window.event).returnValue = confirmationMessage; //Gecko + IE
        return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
    });
};

window.onload = ValidateForm;

JS
       , View::POS_END);
    }

    /**
     * @return string
     */
    static function generateSubmitButton(){
        return Html::submitButton(Yii::t('app', 'Ok'), ['class' => 'btn btn-primary']);
    }



}