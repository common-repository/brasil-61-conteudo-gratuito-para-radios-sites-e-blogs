<?php
function br61_ativacao_html(){

	$pagina = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

	echo '<section class="b61_container flex page_'.esc_html($pagina).'">';

		include dirname(__FILE__)."/static/menu.php";

		echo '<div class="b61_body">';
?>

			<div class="flex b61_ativacao_container">

				<div class="b61_ativacao_body text-center">

					<div class="b61_ativacao_header">
						<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/brasil-61.svg'); ?>" alt="Brasil 61">
					</div>

					<div class="b61_ativacao_form">

						<?php if( get_option( 'br61_key' ) ){ ?>

							<div class="text-center b61_ativacao_success">
								<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/icons/success.png'); ?>" alt="Chave ativada com sucesso">
								<h2>Chave ativada com sucesso.</h2>
							</div>

						<?php }else{ ?>

							<div class="text-center">
								<h2>Ativação de conta</h2>
								<p>Insira sua chave para ativar sua conta do Brasil 61</p>
							</div>

							<form id="b61_activation_form">
								<input type="text" id="b61_token" name="b61_token">
								<?php wp_nonce_field( 'br61_salvar_token' ); ?>
								<button type="submit" id="b61_ativacao_form_submit" data-label="Validar chave">Validar chave</button>

								<div class="form_response display-none"></div>
							</form>

							<a data-fancybox data-type="iframe" href="https://brasil61.com/plugin_forms/new">Solicitar chave de ativação</a>

						<?php } ?>

					</div>

				</div>

			</div>


<?php
		echo '</div>';        

	echo '</section>';        

}