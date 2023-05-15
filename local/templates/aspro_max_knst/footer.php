						<?CMax::checkRestartBuffer();?>
						<?IncludeTemplateLangFile(__FILE__);?>
							<?if(!$isIndex):?>
								<?if($isHideLeftBlock && !$isWidePage):?>
									</div> <?// .maxwidth-theme?>
								<?endif;?>
								</div> <?// .container?>
							<?else:?>

							<?endif;?>
						</div> <?// .middle?>
					<?//if(($isIndex && $isShowIndexLeftBlock) || (!$isIndex && !$isHideLeftBlock) && !$isBlog):?>
					<?if(($isIndex && ($isShowIndexLeftBlock || $bActiveTheme)) || (!$isIndex && !$isHideLeftBlock)):?>
						</div> <?// .right_block?>
					<?endif;?>
					</div> <?// .container_inner?>
				<?if($isIndex):?>
					</div>
				<?elseif(!$isWidePage):?>
					</div> <?// .wrapper_inner?>
				<?endif;?>
			</div> <?// #content?>
		</div><?// .wrapper?>

		<footer id="footer">
			<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/footer_include/under_footer.php'));?>
			<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/footer_include/top_footer.php'));?>
		</footer>
		<?include_once(str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/footer_include/bottom_footer.php'));?>
	</body>

<script src="<?=SITE_TEMPLATE_PATH;?>/scripts/jquery.maskedinput.js?ver=1241241244"></script>
<script src="<?=SITE_TEMPLATE_PATH;?>/scripts/jquery.fancybox.js?ver=1241241244"></script>
<script src="<?=SITE_TEMPLATE_PATH;?>/scripts/slick.js?ver=1241241244"></script>
<script src="<?=SITE_TEMPLATE_PATH;?>/scripts/jquery.matchHeight.js?ver=1241241244"></script>
<script src="<?=SITE_TEMPLATE_PATH;?>/scripts/init.js?ver=1241241244"></script>
<script src="<?=SITE_TEMPLATE_PATH;?>/scripts/custom.js"></script>
</html>