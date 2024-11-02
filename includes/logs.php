<?php
function br61_logs_html(){

	$pagina = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

		echo '<section class="b61_container flex page_'.esc_html($pagina).'">';

		include dirname(__FILE__)."/static/menu.php";

		echo '<div class="b61_body">';
	?>

			<h3>Logs do sistema</h3>
			<br>

			<table class="b61_table">
				<tbody>
					<tr>
						<td>WP CRON</td>
						<td><?php if( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON == true ){ echo 'ERRO: É necessário ativar o CRON.'; }else{ echo 'OK'; } ?></td>
					</tr>
					<tr>
						<td>Limite de Memória</td>
						<td><?php echo esc_html(WP_MEMORY_LIMIT); ?></td>
					</tr>
				</tbody>
			</table>


<?php
		echo '</div>';        

	echo '</section>';        

}