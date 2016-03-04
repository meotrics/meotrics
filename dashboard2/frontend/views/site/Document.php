<?php
/* @var $this yii\web\View */
/* @var $doc string */
$this->title = 'Document';
?>

<div class="row">
    <iframe src="<?= $doc ?>" style="border: none; width: 100%; height: 100%; margin-top:-15px;">
        <p>Go <a href="<?= $doc ?>">here</a> to see documentation</p>
    </iframe>
</div>


