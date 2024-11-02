<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	$pagina = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : ''; 
?>

<div class="b61_menu">

	<div class="b61_logo">
		<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/b61_icon-black.png'); ?>" alt="Brasil 61">
	</div>

	<div class="b61_menu_itens">

		<a href="<?php echo esc_url(get_admin_url()); ?>admin.php?page=br61" title="Dashboard" class="menu_item <?php if( isset($pagina) && $pagina == 'br61' ){ echo 'menu_item_active'; } ?>">
			<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/icons/home.png'); ?>" alt="Dashboard">
		</a>

		<a href="<?php echo esc_url(get_admin_url()); ?>admin.php?page=br61_configuracoes" title="Configurações" class="menu_item <?php if( isset($pagina) && $pagina == 'br61_configuracoes' ){ echo 'menu_item_active'; } ?>">
			<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/icons/config.png'); ?>" alt="Configurações">
		</a>

		<a href="<?php echo esc_url(get_admin_url()); ?>admin.php?page=br61_logs" title="Logs" class="menu_item <?php if( isset($pagina) && $pagina == 'br61_logs' ){ echo 'menu_item_active'; } ?>">
			<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/icons/log.png'); ?>" alt="Logs">
		</a>

		<a href="<?php echo esc_url(get_admin_url()); ?>admin.php?page=br61_ativacao" title="Chave de Ativação" class="menu_item menu_b61_ativacao <?php if( isset($pagina) && $pagina == 'br61_ativacao' ){ echo 'menu_item_active'; } ?>">
			<img src="<?php echo esc_url(br61_PLUGIN_DIR.'assets/images/icons/key.png'); ?>" alt="Chave de Ativação">
		</a>

	</div>

</div>

<input type="hidden" id="baseurl" value="<?php echo esc_url(home_url()); ?>">