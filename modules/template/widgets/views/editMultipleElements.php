<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $model humhub\modules\custom_pages\modules\template\models\forms\TemplateElementForm */
?>
<div class="modal-dialog <?= (empty($model->contentMap)) ? 'modal-dialog-normal' : 'modal-dialog-large' ?>">
    <div class="modal-content media">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
        <?= Html::hiddenInput('editMultipleElements', true); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">
                <?= $title ?>
            </h4>
        </div>
        <div class="modal-body media-body template-edit-multiple"> 
            <?php $counter = 0 ?>
            <?php foreach ($model->contentMap as $key => $contentItem) : ?>
                
                <?php $isContainer = $contentItem->content instanceof humhub\modules\custom_pages\modules\template\models\ContainerContent; ?>
                
                <div class="panel panel-default">
                    <div class="template-edit-multiple-tab panel-heading" tabindex="0">
                        <strong>#<?= Html::encode($contentItem->ownerContent->element_name) ?>&nbsp;<i class="switchIcon fa fa-caret-down" aria-hidden="true"></i></strong>
                        <small class="pull-right">
                            <span class="label label-success"><?= $contentItem->ownerContent->label ?></span>
                        </small>
                        <?php if ($contentItem->content->isNewRecord): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-warning"><?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'Empty') ?></span>
                            </small>
                        <?php endif; ?>
                        <?php if ($isContainer && $contentItem->content->definition->allow_multiple): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-success"><?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'Multiple') ?></span>
                            </small>
                        <?php endif; ?>
                        <?php if ($isContainer && $contentItem->content->definition->is_inline): ?>
                            <small class="pull-right" style="margin-right: 2px">
                                <span class="label label-success"><?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'Inline') ?></span>
                            </small>
                        <?php endif; ?>
                    </div>
                    <?php // This was only set for container elements before. ?>
                    <div class="panel-body" data-element-index="<?= $counter ?>" style="<?= ($counter != 0) ? 'display:none' : '' ?>">
                        <?= $contentItem->content->renderForm($form); ?>
                    </div>
                    <div class="panel-footer">&nbsp;</div>
                </div>
                <?php $counter++ ?>
            <?php endforeach; ?>

            <?php if (empty($model->contentMap)) : ?>
                <div class="text-center">
                    <?= Yii::t('CustomPagesModule.widgets_views_editMultipleElements', 'This template does not contain any elements yet.') ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <?php if (!empty($model->contentMap)) : ?>
                <button id="editTemplateSubmit" class="btn btn-primary" data-ui-loader><?= Yii::t('CustomPagesModule.base', 'Save'); ?></button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Cancel'); ?></button>
            <?php else: ?>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('CustomPagesModule.base', 'Back'); ?></button>
            <?php endif; ?>

        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script type="text/javascript">
    $('.template-edit-multiple-tab:first').focus();

    $('.template-edit-multiple-tab').on('keyup', function (e) {
        switch (e.which) {
            case 13:
                e.preventDefault();
                $(this).trigger('click');
                break;
            case 39:
            case 40:
                e.preventDefault();
                if (!$(this).next('.panel-body').is(':visible')) {
                    $(this).trigger('click');
                }
                break;
            case 37:
            case 38:
                e.preventDefault();
                if ($(this).next('.panel-body').is(':visible')) {
                    $(this).trigger('click');
                }
                break;
        }
    });

    $('.template-edit-multiple-tab').on('click', function () {
        $(this).next('.panel-body').slideToggle('fast');
        var $switchIcon = $(this).find('.switchIcon');
        if($switchIcon.hasClass('fa-caret-down')) {
            $switchIcon.removeClass('fa-caret-down');
            $switchIcon.addClass('fa-caret-up');
        } else {
            $switchIcon.removeClass('fa-caret-up');
            $switchIcon.addClass('fa-caret-down');
        }
    });
    
    $('#editTemplateSubmit').on('click', function (evt) {
        evt.preventDefault();

        var $form = $(this).closest('form');

        var $disabled = $form.find(':disabled');

        // TODO: This is rather hacky, we do not want to save the definition fields in this cas
        // Should rather be handled in the backend!
        $disabled.each(function () {
            var name = $(this).attr('name');
            $form.find('[name="' + name + '"]').remove();
        });

        var action = $form.attr('action');

        $('textarea.ckeditorInput').each(function () {
            var $textarea = $(this);
            $textarea.val(CKEDITOR.instances[$textarea.attr('id')].getData());
        });

        $.ajax(action, {
            type: 'POST',
            dataType: 'json',
            data: $form.serialize(),
            success: function (json) {
                if (json.success) {
                    $(document).trigger('templateMultipleElementEditSuccess', [json]);
                } else {
                    $('#globalModal').html(json.content);
                }
            },
            error: function () {
                $(document).trigger('templateElementEditError');
            }
        });
    });
</script>