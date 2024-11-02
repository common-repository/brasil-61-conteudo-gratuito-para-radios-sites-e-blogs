<?php
function br61_dashboard_html(){

	$pagina = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

		echo '<section class="b61_container flex page_'.esc_html($pagina).'">';

		include dirname(__FILE__)."/static/menu.php";

		echo '<div class="b61_body">';

			$avisos = br61_getB61Notes();
	?>


			<div class="b61_dashboard_header">
				<div class="flex flex-space">
					<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/brasil-61.svg'); ?>" alt="Brasil 61">
					<h2><strong>Todo</strong> nosso conteúdo é gratuito e de <strong>livre</strong> reprodução.</h2>
				</div>
			</div>

			<div class="b61_content">

				<?php if( !empty($avisos) ){ ?>
					<div class="b61_avisos flex">

						<?php foreach( $avisos as $aviso ){ ?>

							<div class="b61_avisos_item">

								<div class="flex flex-space">
									<h3><?php echo esc_html($aviso['title']); ?></h3>
									<span><?php echo esc_html(date("d/m/Y", strtotime($aviso['updated_at']))); ?></span>
								</div>

								<p><?php echo esc_html(strip_tags($aviso['content'])); ?></p>

							</div>

						<?php } ?>

					</div>
				<?php } ?>

			</div>


<?php
		echo '</div>';        

	echo '</section>';        

}