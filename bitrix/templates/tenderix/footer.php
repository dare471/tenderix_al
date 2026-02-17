<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
		<a href="#top" class="back-to-top" aria-label="Вернуться к началу страницы"><i class="fas fa-chevron-up" aria-hidden="true"></i></a>
	</div>
		<? if ($USER->IsAuthorized()):?>
			<footer class="footer" role="contentinfo">
				<div class="container-fluid">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<div class="footer-content">
									<div class="row">
										<div class="col">
											<span class="footer-author">© 2016–<?=date('Y')?> ТОО «Лев Интеграция»</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</footer>
		<? endif; ?>
		</div>
		
	</body>
</html>