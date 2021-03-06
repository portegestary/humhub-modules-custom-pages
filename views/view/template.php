<?php

use yii\helpers\Url;

if(version_compare(Yii::$app->version, '1.2', '<')) {
    humhub\assets\Select2ExtensionAsset::register($this);
}

if($editMode) {
    \humhub\modules\custom_pages\InlineEditAsset::register($this);
}

$cssClass = ($page->hasAttribute('cssClass') && !empty($page->cssClass)) ? $page->cssClass : 'custom-pages-page';
?>

<div id="templatePageRoot" class="container <?= $cssClass ?>">
    <div class="row">
        <div class="col-md-12">
            <?= $html; ?>
        </div>
    </div>
</div>

<?php if ($canEdit && $editMode): ?>
    <script>
        // See inlineEditor.js
        var editConfig = {
            $templatePageRoot: $('#templatePageRoot'),
            editTemplateText: '<?= Yii::t('CustomPagesModule.views_view_template', 'Edit Template') ?>',
            toggleOnText: '<?= Yii::t('CustomPagesModule.base', 'On') ?>',
            toggleOffText: '<?= Yii::t('CustomPagesModule.base', 'Off') ?>',

            elementEditUrl: '<?= Url::to(['/custom_pages/template/owner-content/edit']) ?>',
            elementDeleteUrl: '<?= Url::to(['/custom_pages/template/owner-content/delete']) ?>',

            createContainerUrl: '<?= Url::to(['/custom_pages/template/container-content/create-container']) ?>',

            itemDeleteUrl: '<?= Url::to(['/custom_pages/template/container-content/delete-item']) ?>',
            itemEditUrl: '<?= Url::to(['/custom_pages/template/container-content/edit-item']) ?>',
            itemAddUrl: '<?= Url::to(['/custom_pages/template/container-content/add-item']) ?>',
            itemMoveUrl: '<?= Url::to(['/custom_pages/template/container-content/move-item']) ?>',
        };
    </script>
<?php endif; ?>
