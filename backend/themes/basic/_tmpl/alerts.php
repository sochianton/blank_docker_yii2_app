<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 04.03.18
 * Time: 22:08
 */
/* @var $this \yii\web\View */

?>
<?php foreach (Yii::$app->session->getAllFlashes() as $key => $message):?>
    <?php
        if(in_array($key, ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark']))
        if(is_array($message)) $message = implode("<br />", $message);
    ?>
    <div class="alert alert-<?=$key?> alert-dismissible fade show" role="alert" style="font-size:16px">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <?=$message?>
    </div>
<?php endforeach;?>