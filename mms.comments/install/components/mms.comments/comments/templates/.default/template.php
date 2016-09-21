<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CommentsComponent $component */
$this->setFrameMode(true);
?>
<button class="btn btn-info btn-sm open-modal" data-parent=""><?=GetMessage("MMS_COMMENTS_TPL_CREATE_COMMENT");?></button>

<?if (!empty($arResult["ITEMS"])):?>

	<ul class="comments-list">

		<?$previousLevel = 1;?>

		<?foreach($arResult["ITEMS"] as $arItem):?>

			<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
				<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
			<?endif?>

		<li class="comment-item" data-id="<?=$arItem["ID"];?>">
			<div class="well comment-body">
				<p>
					<?=$arItem["DATE"];?>
				</p>
				<?if (!empty($arItem["USER"])):?>
					<p>
						<?=$arItem["USER"];?>
					</p>
				<?endif;?>
				<div class="comment-text">
					<?=$arItem["DETAIL_TEXT"];?>
				</div>
				<button class="btn btn-info btn-sm open-modal" data-parent="<?=$arItem["ID"];?>"><?=GetMessage("MMS_COMMENTS_TPL_CREATE_COMMENT");?></button>
			</div>
			<ul class="reply-comments-list">

			<?if (!$arItem["IS_PARENT"]):?>
				</ul></li>
			<?endif;?>

			<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

		<?endforeach?>

		<?if ($previousLevel > 1)://close last item tags?>
			<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
		<?endif?>

	</ul>

<?endif?>

<div id="commentModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><?=GetMessage("MMS_COMMENTS_TPL_INPUT_TEXT");?></h4>
			</div>
			<div class="modal-body">
				<form id="comment_form" method="POST" action="">
					<textarea id="comment_text" name="comment_text"></textarea>
					<input type="hidden" value="" name="parent_id" id="parent_comment_id" />
					<input type="hidden" name="params_hash" value="<?=$arResult["PARAMS_HASH"]?>" />
					<input type="submit" style="display: none;" />
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-default" id="addComment"><?=GetMessage("MMS_COMMENTS_TPL_ADD_COMMENT");?></button>
			</div>
		</div>
	</div>
</div>
