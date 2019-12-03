<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 15.06.19
 * Time: 22:59
 *
 * @var \yii\web\View           $this
 * @var \common\ar\Bid          $model
 * @var array                   $errors
 * @var array                   $grid
 * @var array                   $header
 * @var array                   $headerDescription
 * @var array                   $mapHeader
 *
 *
 */


use common\widgets\AppForm;

?>
<?php if(!empty($errors)):?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i>Ошибки в загружаемых данных</h4>
        <?=\common\helpers\Helper::ArrayToUl($errors)?>
    </div>
<?php endif;?>

<div class="box">

    <div class="box-header">
        <h3 class="box-title"><?=Yii::t('app', 'Import table template')?></h3>
    </div>


    <div class="box-body no-padding">

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <?php foreach ($header as $attr):?>
                        <th><?=$model->getAttributeLabel($attr)?></th>
                    <?php endforeach;?>
                </tr>
                <tr>
                    <?php foreach ($header as $attr):?>
                        <td><?=isset($headerDescription[$attr])?$headerDescription[$attr]:''?></td>
                    <?php endforeach;?>
                </tr>
            </thead>
        </table>

    </div>

</div>

<div class="box">

    <div class="box-body">

        <?php $form = AppForm::begin([
            'id' => 'upload_file_'.$model->formName(),
            'type' => AppForm::TYPE_VERTICAL,
            'options' => [
                'enctype' => 'multipart/form-data',
            ],

        ]); ?>

        <div class="form-group">
            <label for="file"><?=Yii::t('app', 'File')?></label>
            <?= \yii\helpers\Html::fileInput('file')?>
        </div>

        <?php AppForm::end(); ?>

    </div>

</div>

<?php if(!empty($grid)):?>
    <div class="box">

        <div class="box-header">
            <h3 class="box-title"><?=Yii::t('app', 'Check import data')?></h3>
        </div>

        <?php $form = AppForm::begin([
            'id' => 'upload_grid_'.$model->formName(),
            'type' => AppForm::TYPE_VERTICAL,

        ]); ?>

        <div class="box-body no-padding">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <?php foreach ($mapHeader as $attr=>$ix):?>
                            <th><?=$model->getAttributeLabel($attr)?></th>
                        <?php endforeach;?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grid as $idx=>$row):?>
                        <?php
                            if($idx == 0) continue;
                        ?>
                    <tr>
                        <?php foreach ($mapHeader as $attr=>$ix):?>
                            <td>
                                <?php if(isset($row[$ix])):?>
                                    <?=\yii\helpers\Html::textInput('Import['.$idx.']['.$attr.']', $row[$ix], [
                                        'class' => 'form-control'
                                    ])?>
                                <?php else:?>
                                    <?=\yii\helpers\Html::textInput('Import['.$idx.']['.$attr.']', null, [
                                        'class' => 'form-control'
                                    ])?>
                                <?php endif;?>
                            </td>
                        <?php endforeach;?>
                    </tr>
                    <?php endforeach;?>
                </tbody>
            </table>



        </div>

        <div class="box-footer clearfix">
            <?php AppForm::end(); ?>
        </div>

    </div>
<?php endif;?>
