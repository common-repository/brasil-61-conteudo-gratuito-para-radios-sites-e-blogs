<?php
/*
 Plugin Name:       Brasil 61 - Conteúdo gratuito para rádios, sites e blogs.
 Plugin URI:        https://brasil61.com/quem-somos
 Description:       Transforme seu site ou blog em uma fonte de conteúdo de qualidade com o plugin do Brasil 61. Agora, você pode manter um site que se atualiza automaticamente, com novos conteúdos diários. A melhor parte? O plugin do Brasil 61 vai te ajudar a impulsionar seu marketing de conteúdo, a melhorar o seu SEO, a aumentar o envolvimento do seu público e trazer mais visualizações. Tudo isso pode trazer um maior ganho financeiro. Experimente o plugin do Brasil 61 hoje mesmo!
 Version:           1.0.3.1
 Requires at least: 5.2
 Requires PHP:      7.2
 Author:            Brasil 61
 Author URI:        https://brasil61.com/
 Text Domain:       brasil61_importador
 License: GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'br61_PLUGIN_DIR', plugin_dir_url(__FILE__) ); 
define( 'br61_URL_API', 'https://brasil61.com/api/v1/' ); 

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                                 ASSETS                                  │
// └─────────────────────────────────────────────────────────────────────────┘
function br61_assets() {
	wp_enqueue_style( 'css-br61', plugins_url( 'assets/css.css', __FILE__ ), '', 1 );
	wp_enqueue_script('js-br61', plugins_url( 'assets/scripts.js', __FILE__ ), ['jquery'], 1, true);
}
add_action( 'admin_enqueue_scripts', 'br61_assets' );

function br61_front_assets() {
	wp_enqueue_script('js-br61-front', plugins_url( 'assets/scripts_front.js', __FILE__ ), ['jquery'], 1, true);
}
add_action( 'wp_enqueue_scripts', 'br61_front_assets' );

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                              MENU PAGES                                 │
// └─────────────────────────────────────────────────────────────────────────┘
function br61_optionsPage(){

	add_menu_page(
		'Brasil 61',
		'Brasil 61',
		'manage_options',
		'br61',
		'br61_dashboard_html',
		'none',
		20
	);

	add_submenu_page(
		'br61',
		'Configurações',
		'Configurações',
		'manage_options',
		'br61_configuracoes',
		'br61_configuracoes_html'
	);


	add_submenu_page(
		'br61',
		'Logs',
		'Logs',
		'manage_options',
		'br61_logs',
		'br61_logs_html'
	);

	add_submenu_page(
		'br61',
		'Chave de ativação',
		'Chave de ativação',
		'manage_options',
		'br61_ativacao',
		'br61_ativacao_html'
	);

}
add_action( 'admin_menu', 'br61_optionsPage' );



// ┌─────────────────────────────────────────────────────────────────────────┐
// │                                FUNCTIONS                                │
// └─────────────────────────────────────────────────────────────────────────┘
function br61_isB61page(){

	$pagina = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

	if( isset($pagina) && preg_match("/br61/i", $pagina) ){
		return true;
	}else{
		return false;
	}

}

function br61_body_class($classes) {
	if( br61_isB61page() ){
		$classes .= 'b61_importador';
		return $classes;
	}
}
add_filter('admin_body_class', 'br61_body_class');



function br61_getActivationKey() {

	$pagina = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

	if( !get_option( 'br61_key' ) && br61_isB61page() && $pagina != 'br61_ativacao' ){
		wp_redirect( admin_url( '/admin.php?page=br61_ativacao' ) );
		exit;
	}

} 
add_action( 'init', 'br61_getActivationKey' );


function br61_salvar_configuracoes() {

	if( wp_verify_nonce( sanitize_text_field( wp_unslash ( $_REQUEST['_wpnonce'] ) ), 'br61_salvar_configuracoes' ) ){

		$cron_antigo 		= '';
		$cron_atual 		= get_option( 'br61_cron' );
		$cron 				= sanitize_text_field($_POST['cron']);

		$categoria 			= array_map( 'sanitize_text_field', $_POST['categoria'] );
		$categoria 			= array_values(array_filter($categoria));
		$categoria_site 	= array_map( 'sanitize_text_field', $_POST['categoria_site'] );
		$categoria_site 	= array_values(array_filter($categoria_site));

		$tag 				= array_map( 'sanitize_text_field', $_POST['tag'] );
		$tag 				= array_values(array_filter($tag));
		$tag_site 			= array_map( 'sanitize_text_field', $_POST['tag_site'] );
		$tag_site 			= array_values(array_filter($tag_site));

		$autor 				= sanitize_text_field($_POST['autor']);
		$status 			= sanitize_text_field($_POST['status']);
		$data_importacao 	= sanitize_text_field($_POST['data_importacao']);

		if( !isset($_POST['importar_imagem']) ){ 
			$importar_imagem = 'false'; 
		}else{
			$importar_imagem = sanitize_text_field($_POST['importar_imagem']);
		}

		if( !isset($_POST['importar_resumo']) ){ 
			$importar_resumo = 'false'; 
		}else{
			$importar_resumo = sanitize_text_field($_POST['importar_resumo']);
		}

		if( !isset($_POST['backlink']) ){ 
			$backlink = 'false'; 
		}else{
			$backlink = sanitize_text_field($_POST['backlink']);
		}

		update_option( 'br61_importar_imagem', $importar_imagem );
		update_option( 'br61_importar_resumo', $importar_resumo );
		update_option( 'br61_backlink', $backlink );
		update_option( 'br61_autor', $autor );
		update_option( 'br61_status', $status );
		update_option( 'br61_cron', $cron );
		update_option( 'br61_data_importacao', $data_importacao );

		if( 
			isset($categoria) && !empty($categoria) &&
			isset($categoria_site) && !empty($categoria_site)
		){

			$array_categorias = array();
			$total_categorias = count($categoria); 		

			for( $i=0 ; $i < $total_categorias ; $i++ ) {
				$array_categorias[$categoria[$i]] = $categoria_site[$i];
				update_option( 'b61_categoria_last_page_'.$categoria[$i], 1 );
			}

			update_option( 'br61_categorias', $array_categorias );

		}else{
			update_option( 'br61_categorias', false );
		}

		if( 
			isset($tag) && !empty($tag) &&
			isset($tag_site) && !empty($tag_site)
		){

			$array_tags = array();
			$total_tags = count($tag); 		

			for( $i=0 ; $i < $total_tags ; $i++ ) {
				$array_tags[$tag[$i]] = $tag_site[$i];
				update_option( 'b61_tag_last_page_'.$tag[$i], 1 );
			}

			update_option( 'br61_tags', $array_tags );

		}else{
			update_option( 'br61_tags', false );
		}

		if( $cron_atual != $cron ){
			$cron_antigo = $cron_atual;
		}

		br61_updateCron( $cron, $cron_antigo );

		wp_redirect( admin_url( '/admin.php?page=br61_configuracoes&salvo=1' ) );
		exit;

	}else{
		wp_die( 'Sem permissão' ); 
	}

} 
add_action( 'admin_post_br61_salvar_configuracoes', 'br61_salvar_configuracoes' );


function br61_checkToken() {

	$nonce = sanitize_text_field(wp_unslash( $_POST['nonce'] ));

	if( wp_verify_nonce($nonce, 'br61_salvar_token') ){

		$json_body = sanitize_text_field($_POST['dados_body']);
		$json_body = html_entity_decode( stripslashes($json_body) );
		$json_body = json_decode($json_body, true);

		if( isset($json_body['token']) && !empty($json_body['token']) ){

			$data = array('authentication_token' => $json_body['token']);

			$b61_post = wp_remote_post( br61_URL_API.'vehicles/authentication', array(
				'method'	=> 'POST',
				'timeout'	=> 30,
				'body'		=> json_encode($data)
			));

			if( $b61_post['response']['code'] == 400 || $b61_post['response']['code'] == 200 && empty($b61_post['body']) ){

				$response['error'] = 'Não foi possível validar essa chave de ativação, por favor, entre em contato com a Brasil 61.';

			}else{

				$body = json_decode($b61_post['body'], true);

				if( $body['status'] ){
					
					add_option( 'br61_key', $json_body['token'] );

					$response['success'] 	= true;
					$response['msg'] 		= 'Ativação feita com sucesso. Aguarde...';
				}else{
					$response['error'] = 'Não foi possível validar essa chave de ativação, por favor, entre em contato com a Brasil 61.';
				}

			}

		}else{
			$response['error'] = 'Informe uma chave de ativação.';
		}

	}else{
		$response['error'] = 'Sem permissão';
	}
	
	echo wp_json_encode($response);
	exit;

}
add_action( 'wp_ajax_br61_checkToken', 'br61_checkToken' );



function br61_getB61Notes() {

	$response = '';

	$b61_post = wp_remote_post( br61_URL_API.'/notice_center', array(
		'method'	=> 'GET',
		'timeout'	=> 30,
		'headers'     => array(
			'TOKEN' => get_option( 'br61_key' )
		)
	));

	if( $b61_post['response']['code'] == 200 && !empty($b61_post['body']) ){

		$response = json_decode($b61_post['body'], true);;
		$response = array_reverse($response);

	}

	return $response;
	exit;

}


function br61_getB61Tags( $array_categorias = '' ) {

	$b61_post = wp_remote_post( br61_URL_API.'/tags', array(
		'method'	=> 'GET',
		'timeout'	=> 30,
		'headers'   => array(
			'TOKEN' => get_option( 'br61_key' )
		)
	));

	if( $b61_post['response']['code'] == 400 || $b61_post['response']['code'] == 200 && empty($b61_post['body']) ){

		if( !empty($b61_post['body']) ){

			$body = json_decode($b61_post['body'], true);
			$response['error'] = $body['msg'];

			delete_option( 'br61_key' );

		}else{
			$response['error'] = 'Erro ao buscar as tags, entre em contato com a Brasil 61.';
		}

	}else{
		$body = json_decode($b61_post['body'], true);
		$response = $body['data'];
	}

	return $response;
	exit;

}
add_action( 'wp_ajax_br61_getB61Tags', 'br61_getB61Tags' );

function br61_getB61Categories() {

	$b61_post = wp_remote_post( br61_URL_API.'/categories', array(
		'method'	=> 'GET',
		'timeout'	=> 30,
		'headers'     => array(
			'TOKEN' => get_option( 'br61_key' )
		)
	));

	if( $b61_post['response']['code'] == 400 || $b61_post['response']['code'] == 200 && empty($b61_post['body']) ){

		if( !empty($b61_post['body']) ){

			$body = json_decode($b61_post['body'], true);
			$response['error'] = $body['msg'];

			delete_option( 'br61_key' );

		}else{
			$response['error'] = 'Erro ao buscar categorias, entre em contato com a Brasil 61.';
		}

	}else{
		$body = json_decode($b61_post['body'], true);
		$response = $body['data'];
	}

	return $response;
	exit;

}

function br61_getCategories() {

	$categories = get_categories(array(
		'orderby' 		=> 'name',
		'order'   		=> 'ASC',
		'hide_empty'	=> false,
	));

	return $categories;
	exit;

}


function br61_file_extension($mime) {
	$mime_map = [
		'video/3gpp2'                                                               => '3g2',
		'video/3gp'                                                                 => '3gp',
		'video/3gpp'                                                                => '3gp',
		'application/x-compressed'                                                  => '7zip',
		'audio/x-acc'                                                               => 'aac',
		'audio/ac3'                                                                 => 'ac3',
		'application/postscript'                                                    => 'ai',
		'audio/x-aiff'                                                              => 'aif',
		'audio/aiff'                                                                => 'aif',
		'audio/x-au'                                                                => 'au',
		'video/x-msvideo'                                                           => 'avi',
		'video/msvideo'                                                             => 'avi',
		'video/avi'                                                                 => 'avi',
		'application/x-troff-msvideo'                                               => 'avi',
		'application/macbinary'                                                     => 'bin',
		'application/mac-binary'                                                    => 'bin',
		'application/x-binary'                                                      => 'bin',
		'application/x-macbinary'                                                   => 'bin',
		'image/bmp'                                                                 => 'bmp',
		'image/x-bmp'                                                               => 'bmp',
		'image/x-bitmap'                                                            => 'bmp',
		'image/x-xbitmap'                                                           => 'bmp',
		'image/x-win-bitmap'                                                        => 'bmp',
		'image/x-windows-bmp'                                                       => 'bmp',
		'image/ms-bmp'                                                              => 'bmp',
		'image/x-ms-bmp'                                                            => 'bmp',
		'application/bmp'                                                           => 'bmp',
		'application/x-bmp'                                                         => 'bmp',
		'application/x-win-bitmap'                                                  => 'bmp',
		'application/cdr'                                                           => 'cdr',
		'application/coreldraw'                                                     => 'cdr',
		'application/x-cdr'                                                         => 'cdr',
		'application/x-coreldraw'                                                   => 'cdr',
		'image/cdr'                                                                 => 'cdr',
		'image/x-cdr'                                                               => 'cdr',
		'zz-application/zz-winassoc-cdr'                                            => 'cdr',
		'application/mac-compactpro'                                                => 'cpt',
		'application/pkix-crl'                                                      => 'crl',
		'application/pkcs-crl'                                                      => 'crl',
		'application/x-x509-ca-cert'                                                => 'crt',
		'application/pkix-cert'                                                     => 'crt',
		'text/css'                                                                  => 'css',
		'text/x-comma-separated-values'                                             => 'csv',
		'text/comma-separated-values'                                               => 'csv',
		'application/vnd.msexcel'                                                   => 'csv',
		'application/x-director'                                                    => 'dcr',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
		'application/x-dvi'                                                         => 'dvi',
		'message/rfc822'                                                            => 'eml',
		'application/x-msdownload'                                                  => 'exe',
		'video/x-f4v'                                                               => 'f4v',
		'audio/x-flac'                                                              => 'flac',
		'video/x-flv'                                                               => 'flv',
		'image/gif'                                                                 => 'gif',
		'application/gpg-keys'                                                      => 'gpg',
		'application/x-gtar'                                                        => 'gtar',
		'application/x-gzip'                                                        => 'gzip',
		'application/mac-binhex40'                                                  => 'hqx',
		'application/mac-binhex'                                                    => 'hqx',
		'application/x-binhex40'                                                    => 'hqx',
		'application/x-mac-binhex40'                                                => 'hqx',
		'text/html'                                                                 => 'html',
		'image/x-icon'                                                              => 'ico',
		'image/x-ico'                                                               => 'ico',
		'image/vnd.microsoft.icon'                                                  => 'ico',
		'text/calendar'                                                             => 'ics',
		'application/java-archive'                                                  => 'jar',
		'application/x-java-application'                                            => 'jar',
		'application/x-jar'                                                         => 'jar',
		'image/jp2'                                                                 => 'jp2',
		'video/mj2'                                                                 => 'jp2',
		'image/jpx'                                                                 => 'jp2',
		'image/jpm'                                                                 => 'jp2',
		'image/jpeg'                                                                => 'jpeg',
		'image/pjpeg'                                                               => 'jpeg',
		'application/x-javascript'                                                  => 'js',
		'application/json'                                                          => 'json',
		'text/json'                                                                 => 'json',
		'application/vnd.google-earth.kml+xml'                                      => 'kml',
		'application/vnd.google-earth.kmz'                                          => 'kmz',
		'text/x-log'                                                                => 'log',
		'audio/x-m4a'                                                               => 'm4a',
		'application/vnd.mpegurl'                                                   => 'm4u',
		'audio/midi'                                                                => 'mid',
		'application/vnd.mif'                                                       => 'mif',
		'video/quicktime'                                                           => 'mov',
		'video/x-sgi-movie'                                                         => 'movie',
		'audio/mpeg'                                                                => 'mp3',
		'audio/mpg'                                                                 => 'mp3',
		'audio/mpeg3'                                                               => 'mp3',
		'audio/mp3'                                                                 => 'mp3',
		'video/mp4'                                                                 => 'mp4',
		'video/mpeg'                                                                => 'mpeg',
		'application/oda'                                                           => 'oda',
		'application/vnd.oasis.opendocument.text'                                   => 'odt',
		'application/vnd.oasis.opendocument.spreadsheet'                            => 'ods',
		'application/vnd.oasis.opendocument.presentation'                           => 'odp',
		'audio/ogg'                                                                 => 'ogg',
		'video/ogg'                                                                 => 'ogg',
		'application/ogg'                                                           => 'ogg',
		'application/x-pkcs10'                                                      => 'p10',
		'application/pkcs10'                                                        => 'p10',
		'application/x-pkcs12'                                                      => 'p12',
		'application/x-pkcs7-signature'                                             => 'p7a',
		'application/pkcs7-mime'                                                    => 'p7c',
		'application/x-pkcs7-mime'                                                  => 'p7c',
		'application/x-pkcs7-certreqresp'                                           => 'p7r',
		'application/pkcs7-signature'                                               => 'p7s',
		'application/pdf'                                                           => 'pdf',
		'application/x-x509-user-cert'                                              => 'pem',
		'application/x-pem-file'                                                    => 'pem',
		'application/pgp'                                                           => 'pgp',
		'application/x-httpd-php'                                                   => 'php',
		'application/php'                                                           => 'php',
		'application/x-php'                                                         => 'php',
		'text/php'                                                                  => 'php',
		'text/x-php'                                                                => 'php',
		'application/x-httpd-php-source'                                            => 'php',
		'image/png'                                                                 => 'png',
		'image/x-png'                                                               => 'png',
		'application/powerpoint'                                                    => 'ppt',
		'application/vnd.ms-powerpoint'                                             => 'ppt',
		'application/vnd.ms-office'                                                 => 'ppt',
		'application/msword'                                                        => 'doc',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
		'application/x-photoshop'                                                   => 'psd',
		'image/vnd.adobe.photoshop'                                                 => 'psd',
		'audio/x-realaudio'                                                         => 'ra',
		'audio/x-pn-realaudio'                                                      => 'ram',
		'application/x-rar'                                                         => 'rar',
		'application/rar'                                                           => 'rar',
		'application/x-rar-compressed'                                              => 'rar',
		'audio/x-pn-realaudio-plugin'                                               => 'rpm',
		'application/x-pkcs7'                                                       => 'rsa',
		'text/rtf'                                                                  => 'rtf',
		'text/richtext'                                                             => 'rtx',
		'video/vnd.rn-realvideo'                                                    => 'rv',
		'application/x-stuffit'                                                     => 'sit',
		'application/smil'                                                          => 'smil',
		'text/srt'                                                                  => 'srt',
		'image/svg+xml'                                                             => 'svg',
		'application/x-shockwave-flash'                                             => 'swf',
		'application/x-tar'                                                         => 'tar',
		'application/x-gzip-compressed'                                             => 'tgz',
		'image/tiff'                                                                => 'tiff',
		'text/plain'                                                                => 'txt',
		'text/x-vcard'                                                              => 'vcf',
		'application/videolan'                                                      => 'vlc',
		'text/vtt'                                                                  => 'vtt',
		'audio/x-wav'                                                               => 'wav',
		'audio/wave'                                                                => 'wav',
		'audio/wav'                                                                 => 'wav',
		'application/wbxml'                                                         => 'wbxml',
		'video/webm'                                                                => 'webm',
		'audio/x-ms-wma'                                                            => 'wma',
		'application/wmlc'                                                          => 'wmlc',
		'video/x-ms-wmv'                                                            => 'wmv',
		'video/x-ms-asf'                                                            => 'wmv',
		'application/xhtml+xml'                                                     => 'xhtml',
		'application/excel'                                                         => 'xl',
		'application/msexcel'                                                       => 'xls',
		'application/x-msexcel'                                                     => 'xls',
		'application/x-ms-excel'                                                    => 'xls',
		'application/x-excel'                                                       => 'xls',
		'application/x-dos_ms_excel'                                                => 'xls',
		'application/xls'                                                           => 'xls',
		'application/x-xls'                                                         => 'xls',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
		'application/vnd.ms-excel'                                                  => 'xlsx',
		'application/xml'                                                           => 'xml',
		'text/xml'                                                                  => 'xml',
		'text/xsl'                                                                  => 'xsl',
		'application/xspf+xml'                                                      => 'xspf',
		'application/x-compress'                                                    => 'z',
		'application/x-zip'                                                         => 'zip',
		'application/zip'                                                           => 'zip',
		'application/x-zip-compressed'                                              => 'zip',
		'application/s-compressed'                                                  => 'zip',
		'multipart/x-zip'                                                           => 'zip',
		'text/x-scriptzsh'                                                          => 'zsh',
		'image/webp'                                                          		=> 'webp',
		'image/avif'                                                          		=> 'avif',
	];

	return isset($mime_map[$mime]) === true ? $mime_map[$mime] : false;
}

function br61_get_kses_extended_ruleset() {

    $kses_defaults = wp_kses_allowed_html( 'post' );

    $svg_args = array(
        'svg'   => array(
            'class'           => true,
            'aria-hidden'     => true,
            'aria-labelledby' => true,
            'role'            => true,
            'xmlns'           => true,
            'width'           => true,
            'height'          => true,
            'viewbox'         => true,
        ),
        'g'     => array( 'fill' => true ),
        'title' => array( 'title' => true ),
        'path'  => array(
            'd'    => true,
            'fill' => true,
        ),
    );

    return array_merge( $kses_defaults, $svg_args );
    
}

function br61_import_and_get_file_name($imagem_url, $upload_dir, $microtime, $titulo) {

	if (strpos($imagem_url, 'https:') === false) {
		$imagem_url = 'https:'.$imagem_url;
	}

	$microtime = str_replace(".","", $microtime);
	$microtime = str_replace(" ","", $microtime);

	$file_get_ssl_args = array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		)
	);

	$imagem_content = file_get_contents($imagem_url, false, stream_context_create($file_get_ssl_args));
	
	$finfo = new finfo(FILEINFO_MIME);

	$file_mimetype = $finfo->buffer($imagem_content);
	$file_mimetype = explode(";",$file_mimetype);

	$br61_file_extension = br61_file_extension($file_mimetype[0]);

	$file_name = preg_replace('/[^A-Za-z0-9\-._]/', '', $titulo);
	$file_name = $file_name."_".$microtime.".".$br61_file_extension;

	if ( wp_mkdir_p( $upload_dir['path'] ) ) {
	  $file = $upload_dir['path'] . '/' . $file_name;
	}else{
	  $file = $upload_dir['basedir'] . '/' . $file_name;
	}

	file_put_contents( $file, $imagem_content );

	$wp_filetype = wp_check_filetype( $file_name, null );

	$attachment = array(
		'post_mime_type' => $wp_filetype['type'],
		'post_title' => sanitize_file_name( $file_name ),
		'post_content' => '',
		'post_status' => 'inherit'
	);

	$attach_id = wp_insert_attachment( $attachment, $file );
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	$img_url = wp_get_attachment_image_url($attach_id, 'full');

	$dados['file_name'] = $file_name;
	$dados['file'] 		= $file;
	$dados['attach_id'] = $attach_id;
	$dados['img_url'] 	= $img_url;

	return $dados;
	
}

function br61_export_content_images($conteudo, $titulo){

	$doc = new DOMDocument();
	$doc->loadHTML($conteudo);

	$xml = simplexml_import_dom($doc);
	$images = $xml->xpath('//img');

	foreach( $images as $img ){

		$img_import = br61_import_and_get_file_name($img['src'], wp_upload_dir(), microtime(), $titulo);
		$conteudo = str_replace($img['src'], $img_import['img_url'], $conteudo);

	}

	$conteudo = str_replace('<img', '<img style="width:100%"', $conteudo);

	return $conteudo;
	
}

function br61_insertBacklink($titulo, $url){

	$html = '
		<div class="br61_backlink">
			<p>
				Fonte: <a href="'.$url.'" title="Artigo Original: '.$titulo.'" target="_blank">Brasil 61</a>
			</p>
		</div>
	';

	return $html;

}

function br61_set_post_view( $post ) {

	$post_id = $post->ID;

	if( is_singular( 'post' ) && in_the_loop() ) {

	    $count = get_post_meta($post_id, 'b61_post_view', true);

	    if( empty($count) ){
	        add_post_meta($post_id, 'b61_post_view', '1');
	    }else{
	    	$count++;
	        update_post_meta($post_id, 'b61_post_view', $count);
	    }

	}

}
add_action('the_post', 'br61_set_post_view', 10, 2);

add_filter('the_content', 'br61_insert_post_url');
function br61_insert_post_url($content) {

	$html = '<input type="hidden" id="baseurl" value="'.home_url().'">';
	$html .= '<input type="hidden" id="audio_nonce" value="'.wp_create_nonce( 'br61_audio_view' ).'">';
	$content .= $html;

	return $content;

}


// ┌─────────────────────────────────────────────────────────────────────────┐
// │                                  CRONS                                  │
// └─────────────────────────────────────────────────────────────────────────┘
add_action( 'br61_cron_hook', 'br61_import_posts' );
function br61_updateCron( $selected_cron, $old_cron ){

	$next_timestamp = wp_next_scheduled( 'br61_cron_hook' );

	if( !empty($old_cron) && $next_timestamp ){
		wp_unschedule_event( $next_timestamp, 'br61_cron_hook' );
	}

	if( !wp_next_scheduled( 'br61_cron_hook' ) ){
		wp_schedule_event( time(), $selected_cron, 'br61_cron_hook' );
	}

}

add_filter( 'cron_schedules', 'br61_adicionar_cron' );
function br61_adicionar_cron( $schedules ) { 
	
	$schedules['thirty_minutes'] = array(
		'interval' => 1800,
		'display'  => esc_html__( 'A cada 30 minutos' )
	);

	return $schedules;
}



// ┌─────────────────────────────────────────────────────────────────────────┐
// │                              IMPORT POSTS                               │
// └─────────────────────────────────────────────────────────────────────────┘
function br61_import_posts(){

	$categorias_selecionadas 	= get_option( 'br61_categorias' );
	$tags_selecionadas 			= get_option( 'br61_tags' );

	$data_importacao = get_option( 'br61_data_importacao' );
	$data_importacao = date("d-m-Y", strtotime($data_importacao));

	if( !empty($categorias_selecionadas) ){
		foreach( $categorias_selecionadas as $id_categoria_selecionada => $id_categoria_site_selecionada ){

			$pagina = get_option( 'b61_categoria_last_page_'.$id_categoria_selecionada );

			$data = array(
				'data' => array(
					'categories' 	=> $id_categoria_selecionada,
					'date' 			=> $data_importacao
				)
			);

			$b61_post = wp_remote_post( br61_URL_API.'posts?page='.$pagina, array(
				'method'	=> 'POST',
				'timeout'	=> 30,
				'headers'   => array(
					'TOKEN' 		=> get_option( 'br61_key' ),
					'Content-Type' 	=> 'application/json'
				),
				'body'		=> json_encode($data)
			));

			if( $b61_post['response']['code'] == 200 && !empty($b61_post['body']) ){

				$response = json_decode($b61_post['body'], true);

				if( $response['data'] ):

					foreach( $response['data'] as $data_post ):		

						$id_noticia = explode("-", $data_post['url_noticia']);
						$id_noticia = end($id_noticia);

						$args = array(
							'post_status'	=> array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
							'numberposts'	=> 1,
							'meta_query'	=> array(
								array(
									'key'   => 'b61_noticia_id',
									'value' => $id_noticia,
								)
							)
						);
						$posts 	= get_posts($args);
						
						if( count($posts) == 0 ):

							$audios 			= '';
							$conteudo_post 		= '';
							$texto_e_imagens 	= br61_export_content_images($data_post['texto'], $data_post['titulo']);

							if( !empty($data_post['url_audios']) ){
								foreach( $data_post['url_audios'] as $url_audio ){
									
									$audios .= '
										<audio controls class="b61_audio_player" data-noticia="'.$data_post['id'].'" src="'.$url_audio.'"></audio>
									';

								}
							}

							$conteudo_post .= $audios;
							$conteudo_post .= $texto_e_imagens;

							if( !empty($data_post['url_pixel']) ){
								$conteudo_post .= '
									<img src="'.$data_post['url_pixel'].'" alt="Pixel Brasil 61" class="b61_pixel" width="0" height="0">
								';
							}

							$dados_post = array(
							  'post_title'		=> $data_post['titulo'],
							  'post_content'	=> $conteudo_post,
							  'post_status'		=> get_option( 'br61_status' ),
							  'post_author'		=> get_option( 'br61_autor' )
							);

							if( isset($data_post['data_publicacao']) && !empty($data_post['data_publicacao']) ){

								$data_publicacao_post = str_replace("/", "-", $data_post['data_publicacao']);
								$data_publicacao_post = date("Y-m-d H:i:s", strtotime($data_publicacao_post));

								$dados_post['post_date'] = $data_publicacao_post;

							}

							if( get_option( 'br61_importar_resumo' ) && get_option( 'br61_importar_resumo' ) == 'true' ){
								$dados_post['post_excerpt'] = $data_post['resumo'];
							}

							if( get_option( 'br61_backlink' ) && get_option( 'br61_backlink' ) == 'true' ){
								$dados_post['post_content'] = $dados_post['post_content'].br61_insertBacklink($data_post['titulo'], $data_post['url_noticia']);
							}

							$new_post_id = wp_insert_post( $dados_post );
							wp_set_post_categories($new_post_id,$id_categoria_site_selecionada,false);
							update_post_meta($new_post_id,'b61_noticia_id',$id_noticia);


							if( get_option( 'br61_importar_imagem' ) && get_option( 'br61_importar_imagem' ) == 'true' ){

								if( !empty($data_post['url_imagem']) ){

									$imagem			= $data_post['url_imagem'];
									$img_import 	= br61_import_and_get_file_name($imagem, wp_upload_dir(), microtime(), $data_post['titulo']);

									sleep(1);
									set_post_thumbnail( $new_post_id, $img_import['attach_id'] );

								}

							}

						endif;

					endforeach;

					$nova_pagina = $pagina + 1;

					if( $nova_pagina <= $response['pages'] ){
						update_option( 'b61_categoria_last_page_'.$id_categoria_selecionada, $nova_pagina );
					}

					update_option( 'b61_categoria_total_pages_'.$id_categoria_selecionada, $response['pages'] );

				endif;

			}

		}
	}


	if( !empty($tags_selecionadas) ){
		foreach( $tags_selecionadas as $id_tag_selecionada => $id_tag_site_selecionada ){

			$pagina = get_option( 'b61_tag_last_page_'.$id_tag_selecionada );

			$data = array(
				'data' => array(
					'tags' 	=> $id_tag_selecionada,
					'date' 	=> $data_importacao
				)
			);

			$b61_post = wp_remote_post( br61_URL_API.'posts?page='.$pagina, array(
				'method'	=> 'POST',
				'timeout'	=> 30,
				'headers'   => array(
					'TOKEN' 		=> get_option( 'br61_key' ),
					'Content-Type' 	=> 'application/json'
				),
				'body'		=> json_encode($data)
			));

			if( $b61_post['response']['code'] == 200 && !empty($b61_post['body']) ){

				$response = json_decode($b61_post['body'], true);

				if( $response['data'] ):

					foreach( $response['data'] as $data_post ):		

						$id_noticia = explode("-", $data_post['url_noticia']);
						$id_noticia = end($id_noticia);

						$args = array(
							'post_status'	=> array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
							'numberposts'	=> 1,
							'meta_query'	=> array(
								array(
									'key'   => 'b61_noticia_id',
									'value' => $id_noticia,
								)
							)
						);
						$posts 	= get_posts($args);
						
						if( count($posts) == 0 ):

							$audios 			= '';
							$conteudo_post 		= '';
							$texto_e_imagens 	= br61_export_content_images($data_post['texto'], $data_post['titulo']);

							if( !empty($data_post['url_audios']) ){
								foreach( $data_post['url_audios'] as $url_audio ){
									
									$audios .= '
										<audio controls class="b61_audio_player" data-noticia="'.$data_post['id'].'" src="'.$url_audio.'"></audio>
									';

								}
							}

							$conteudo_post .= $audios;
							$conteudo_post .= $texto_e_imagens;

							if( !empty($data_post['url_pixel']) ){
								$conteudo_post .= '
									<img src="'.$data_post['url_pixel'].'" alt="Pixel Brasil 61" class="b61_pixel" width="0" height="0">
								';
							}

							$dados_post = array(
							  'post_title'    => $data_post['titulo'],
							  'post_content'  => $conteudo_post,
							  'post_status'   => get_option( 'br61_status' ),
							  'post_author'   => get_option( 'br61_autor' )
							);

							if( isset($data_post['data_publicacao']) && !empty($data_post['data_publicacao']) ){

								$data_publicacao_post = str_replace("/", "-", $data_post['data_publicacao']);
								$data_publicacao_post = date("Y-m-d H:i:s", strtotime($data_publicacao_post));

								$dados_post['post_date'] = $data_publicacao_post;

							}

							if( get_option( 'br61_importar_resumo' ) && get_option( 'br61_importar_resumo' ) == 'true' ){
								$dados_post['post_excerpt'] = $data_post['resumo'];
							}

							if( get_option( 'br61_backlink' ) && get_option( 'br61_backlink' ) == 'true' ){
								$dados_post['post_content'] = $dados_post['post_content'].br61_insertBacklink($data_post['titulo'], $data_post['url_noticia']);
							}

							$new_post_id = wp_insert_post( $dados_post );
							wp_set_post_categories($new_post_id,$id_tag_site_selecionada,false);
							update_post_meta($new_post_id,'b61_noticia_id',$id_noticia);


							if( get_option( 'br61_importar_imagem' ) && get_option( 'br61_importar_imagem' ) == 'true' ){

								if( !empty($data_post['url_imagem']) ){

									$imagem			= $data_post['url_imagem'];
									$img_import 	= br61_import_and_get_file_name($imagem, wp_upload_dir(), microtime(), $data_post['titulo']);

									sleep(1);
									set_post_thumbnail( $new_post_id, $img_import['attach_id'] );

								}

							}

						endif;

					endforeach;

					$nova_pagina = $pagina + 1;

					if( $nova_pagina <= $response['pages'] ){
						update_option( 'b61_tag_last_page_'.$id_tag_selecionada, $nova_pagina );
					}

					update_option( 'b61_tag_total_pages_'.$id_tag_selecionada, $response['pages'] );

				endif;

			}

		}
	}

}

// ┌─────────────────────────────────────────────────────────────────────────┐
// │                               PAGES HTML                                │
// └─────────────────────────────────────────────────────────────────────────┘
foreach( glob(dirname(__FILE__)."/includes/*.php") as $filename ){
	include $filename;
}



// ┌─────────────────────────────────────────────────────────────────────────┐
// │                               AUDIO VIEW                                │
// └─────────────────────────────────────────────────────────────────────────┘
add_action( 'wp_ajax_nopriv_br61_audio_view', 'br61_audio_view' );
add_action( 'wp_ajax_br61_audio_view', 'br61_audio_view' );

function br61_audio_view() {

	$nonce = sanitize_text_field(wp_unslash( $_POST['nonce'] ));

	if( wp_verify_nonce($nonce, 'br61_audio_view') ){

		$noticia_id = sanitize_text_field($_POST['noticia_id']);
		$token_site = get_option( 'br61_key' );

		if( !empty($token_site) ){

			$b61_post = wp_remote_post( br61_URL_API.'audio_events/'.$noticia_id, array(
				'method'	=> 'POST',
				'timeout'	=> 30,
				'headers'   => array(
					'TOKEN' 		=> $token_site,
					'Content-Type' 	=> 'application/json'
				)
			));

			if( $b61_post['response']['code'] == 200 && !empty($b61_post['body']) ){

				echo wp_json_encode($b61_post['body']);
				exit;

			}

		}

	}

}


// ┌─────────────────────────────────────────────────────────────────────────┐
// │                               DEACTIVATE                                │
// └─────────────────────────────────────────────────────────────────────────┘
function br61_deactivation(){ 

	//DESATIVA O CRON
	$next_timestamp = wp_next_scheduled( 'br61_cron_hook' );
	if( $next_timestamp ){
		wp_unschedule_event( $next_timestamp, 'br61_cron_hook' );
	}

	$categorias_selecionadas 	= get_option( 'br61_categorias' );
	$tags_selecionadas 			= get_option( 'br61_tags' );

	foreach( $categorias_selecionadas as $id_categoria_selecionada => $id_categoria_site_selecionada ){
		delete_option( 'b61_categoria_last_page_'.$id_categoria_selecionada );
		delete_option( 'b61_categoria_total_pages_'.$id_categoria_selecionada );
	}

	foreach( $tags_selecionadas as $id_tag_selecionada => $id_tag_site_selecionada ){
		delete_option( 'b61_tag_last_page_'.$id_tag_selecionada );
		delete_option( 'b61_tag_total_pages_'.$id_tag_selecionada );
	}

	delete_option( 'br61_key' );

}
register_deactivation_hook( __FILE__, 'br61_deactivation' );
