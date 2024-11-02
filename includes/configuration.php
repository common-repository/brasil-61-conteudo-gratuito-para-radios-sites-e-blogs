<?php
function br61_configuracoes_html(){

	$file_get_ssl_args = array(
		"ssl"=>array(
			"verify_peer"=>false,
			"verify_peer_name"=>false,
		)
	);

	$pagina = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';

		echo '<section class="b61_container flex page_'.esc_html($pagina).'">';

		include dirname(__FILE__)."/static/menu.php";

		echo '<div class="b61_body">';

			$tags 						= br61_getB61Tags();
			$categorias 				= br61_getB61Categories();
			$categorias_local 			= br61_getCategories();
			$categorias_selecionadas 	= get_option( 'br61_categorias' );
			$tags_selecionadas 			= get_option( 'br61_tags' );

			$salvo 						= isset( $_GET['salvo'] ) ? sanitize_text_field( $_GET['salvo'] ) : '';

	?>

			<?php if( isset($salvo) && $salvo == 1 ){ ?>
				<div class="width-100 form_response form-success hide_msg"><p>Configurações alteradas com sucesso!</p></div>
			<?php } ?>
	
			<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="flex flex-space">

				<?php if( isset($categorias['error']) ){ ?>
					<div class="width-100 form_response form-error"><p><?php echo esc_html($categorias['error']); ?></p></div>
				<?php exit; } ?>

				<fieldset class="width-100">

					<label>
						Selecione a categoria
						<small>Selecione nos botões abaixo qual tipo de categoria você gostaria de adicionar. É possível adicionar mais de uma categoria ou tag.</small>
					</label>
					<br>


					<div class="container_clone_selects display-none">
						<div class="container_editorias select_categorias">
							<div class="flex">

								<div class="container_editorias_select">
									<select name="categoria[]" class="select2">
										<option value="">Selecione a categoria que deseja importar</option>
										<?php foreach( $categorias as $categoria ){ ?>
											<option value="<?php echo esc_html($categoria['id']); ?>"><?php echo esc_html($categoria['title']); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="container_editorias_select">
									<select name="categoria_site[]">
										<option value="">Selecione a categoria do seu site para qual será importado</option>
										<?php foreach( $categorias_local as $dados_categoria_local ){ ?>
											<option value="<?php echo esc_html($dados_categoria_local->term_id); ?>"><?php echo esc_html($dados_categoria_local->name); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="container_editorias_action">
									<a href="#" class="btn btn-flex btn-mini btn-red remove_editorial">
										<span><?php echo wp_kses( file_get_contents(esc_url(br61_PLUGIN_DIR.'assets/images/icons/trash-solid.svg'), false, stream_context_create($file_get_ssl_args)), br61_get_kses_extended_ruleset() ); ?></span>

										Remover
									</a>
								</div>

							</div>
						</div>

						<div class="container_editorias select_tags">
							<div class="flex">

								<div class="container_editorias_select">
									<select name="tag[]" class="select2">
										<option value="">Selecione a Tag que deseja importar</option>
										<?php foreach( $tags as $tag){ ?>
											<option value="<?php echo esc_html($tag['id']); ?>"><?php echo esc_html($tag['title']); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="container_editorias_select">
									<select name="tag_site[]">
										<option value="">Selecione a categoria do seu site para qual será importado</option>
										<?php foreach( $categorias_local as $dados_categoria_local ){ ?>
											<option value="<?php echo esc_html($dados_categoria_local->term_id); ?>"><?php echo esc_html($dados_categoria_local->name); ?></option>
										<?php } ?>
									</select>
								</div>

								<div class="container_editorias_action">
									<a href="#" class="btn btn-flex btn-mini btn-red remove_editorial">
										<span><?php echo wp_kses( file_get_contents(esc_url(br61_PLUGIN_DIR.'assets/images/icons/trash-solid.svg'), false, stream_context_create($file_get_ssl_args)), br61_get_kses_extended_ruleset() ); ?></span>
										Remover
									</a>
								</div>

							</div>
						</div>

					</div>


					<div class="container_editorias_config">
						
						<?php 
							if( !empty($categorias_selecionadas) ){
								foreach ($categorias_selecionadas as $id_categoria_selecionada => $id_categoria_site_selecionada) {
						?>
									<div class="container_editorias select_categorias">
										<div class="flex">

											<div class="container_editorias_select">
												<select name="categoria[]" class="display-none">
													<?php 
														foreach( $categorias as $categoria ){ 
															if( $categoria['id'] == $id_categoria_selecionada ){
													?>
																<option value="<?php echo esc_html($categoria['id']); ?>" selected><?php echo esc_html($categoria['title']); ?></option>
													<?php 
															} 
														}
													?>
												</select>

												<p>
													<small>Categoria selecionada: </small>
													<br>
													<strong>
														<?php 
															foreach( $categorias as $categoria ){ 
																if( $categoria['id'] == $id_categoria_selecionada ){
																	echo esc_html($categoria['title']);
																}
															}
														?>
													</strong>
												</p>
											</div>

											<div class="container_editorias_select">
												<select name="categoria_site[]" required>
													<option value="">Selecione a categoria do seu site para qual será importado</option>
													<?php foreach( $categorias_local as $dados_categoria_local ){ ?>
														<option value="<?php echo esc_html($dados_categoria_local->term_id); ?>" <?php if( $dados_categoria_local->term_id == $id_categoria_site_selecionada ){echo "selected";} ?>><?php echo esc_html($dados_categoria_local->name); ?></option>
													<?php } ?>
												</select>
											</div>

											<div class="container_editorias_action">
												<a href="#" class="btn btn-flex btn-mini btn-red remove_editorial">
													<span><?php echo wp_kses( file_get_contents(esc_url(br61_PLUGIN_DIR.'assets/images/icons/trash-solid.svg'), false, stream_context_create($file_get_ssl_args)), br61_get_kses_extended_ruleset() ); ?></span>
													Remover
												</a>
											</div>

										</div>
									</div>
						<?php
								}
							}
						?>

						
						<?php 
							if( !empty($tags_selecionadas) ){
								foreach ($tags_selecionadas as $id_tag_selecionada => $id_tag_site_selecionada) {
						?>
									<div class="container_editorias select_tags">
										<div class="flex">

											<div class="container_editorias_select">

												<select name="tag[]" class="display-none">
													<?php 
														foreach( $tags as $tag){ 
															if( $tag['id'] == $id_tag_selecionada ){
													?>
																<option value="<?php echo esc_html($tag['id']); ?>" selected><?php echo esc_html($tag['title']); ?></option>
													<?php
															}
														}
													?>
												</select>

												<p>
													<small>Tag selecionada: </small>
													<br>
													<strong>
														<?php 
															foreach( $tags as $tag ){ 
																if( $tag['id'] == $id_tag_selecionada ){
																	echo esc_html($tag['title']);
																}
															}
														?>
													</strong>
												</p>
											</div>

											<div class="container_editorias_select">
												<select name="tag_site[]">
													<option value="">Selecione a categoria do seu site para qual será importado</option>
													<?php foreach( $categorias_local as $dados_categoria_local ){ ?>
														<option value="<?php echo esc_html($dados_categoria_local->term_id); ?>" <?php if( $dados_categoria_local->term_id == $id_tag_site_selecionada ){echo "selected";} ?>><?php echo esc_html($dados_categoria_local->name); ?></option>
													<?php } ?>
												</select>
											</div>

											<div class="container_editorias_action">
												<a href="#" class="btn btn-flex btn-mini btn-red remove_editorial">
													<span><?php echo wp_kses( file_get_contents(esc_url(br61_PLUGIN_DIR.'assets/images/icons/trash-solid.svg'), false, stream_context_create($file_get_ssl_args)), br61_get_kses_extended_ruleset() ); ?></span>
													Remover
												</a>
											</div>

										</div>
									</div>

						<?php
								}
							}
						?>


					</div>

					<div class="flex container_editorias_actions">
						<a href="#" class="btn btn-flex btn-mini btn-green add_new_editorial" data-type="categorias">
							<span><?php echo wp_kses( file_get_contents(esc_url(br61_PLUGIN_DIR.'assets/images/icons/newspaper-solid.svg'), false, stream_context_create($file_get_ssl_args)), br61_get_kses_extended_ruleset() ); ?></span>
							Adicionar nova categoria
						</a>

						<a href="#" class="btn btn-flex btn-mini btn-green add_new_editorial" data-type="tags">
							<span><?php echo wp_kses( file_get_contents(esc_url(br61_PLUGIN_DIR.'assets/images/icons/tag-solid.svg'), false, stream_context_create($file_get_ssl_args)), br61_get_kses_extended_ruleset() ); ?></span>
							Adicionar nova tag
						</a>
					</div>
			
				</fieldset>
				

				<fieldset class="width-32">
					<label for="importar_imagem" class="width-100">
						Importar imagem?
						<small>A imagem da publicação será baixada e salva na biblioteca de mídia do Wordpress</small>
					</label>
					<br>

					<div class="flex input_switch">
						<p>Não</p>
						
						<div>
							<input name="importar_imagem" id="importar_imagem" class="tgl tgl-light" type="checkbox" value="true" <?php if( get_option( 'br61_importar_imagem' ) && get_option( 'br61_importar_imagem' ) == 'true' ){ echo 'checked'; }elseif( !get_option( 'br61_importar_imagem' ) ){ echo 'checked'; } ?>>
    						<label class="tgl-btn" for="importar_imagem"></label>
						</div>

						<p>Sim</p>
					</div>

				</fieldset>


				<fieldset class="width-32">
					<label for="importar_resumo" class="width-100">
						Importar resumo?
						<small>Importar resumo do post original</small>
					</label>
					<br>

					<div class="flex input_switch">
						<p>Não</p>
						
						<div>
							<input name="importar_resumo" id="importar_resumo" class="tgl tgl-light" type="checkbox" value="true" <?php if( get_option( 'br61_importar_resumo' ) && get_option( 'br61_importar_resumo' ) == 'true' ){ echo 'checked'; }elseif( !get_option( 'br61_importar_resumo' ) ){ echo 'checked'; } ?>>
    						<label class="tgl-btn" for="importar_resumo"></label>
						</div>

						<p>Sim</p>
					</div>

				</fieldset>


				<fieldset class="width-32">
					<label for="backlink" class="width-100">
						Criar backlink do post?
						<small>Insere um backlink no final do post</small>
					</label>
					<br>

					<div class="flex input_switch">
						<p>Não</p>
						
						<div>
							<input name="backlink" id="backlink" class="tgl tgl-light" type="checkbox" value="true" <?php if( get_option( 'br61_backlink' ) && get_option( 'br61_backlink' ) == 'true' ){ echo 'checked'; }elseif( !get_option( 'br61_backlink' ) ){ echo 'checked'; } ?>>
    						<label class="tgl-btn" for="backlink"></label>
						</div>

						<p>Sim</p>
					</div>

				</fieldset>


				<fieldset class="flex width-48">
					<label for="autor" class="width-100">
						Autor
						<small>Para qual autor serão atribuídos os posts importados? Nossa sugestão é que criem um usuário "Brasil 61"</small>
					</label>

					<select required name="autor" id="autor">
						<option value="">Selecione</option>

						<?php 
							$autores = get_users([
								'fields'  => ['ID', 'display_name'],
								'orderby' => 'display_name',
							]);

							foreach( $autores as $autor ){
						?>
								<option value="<?php echo esc_html($autor->ID); ?>" <?php if( get_option( 'br61_autor' ) && get_option( 'br61_autor' ) == $autor->ID ){ echo 'selected'; } ?>><?php echo esc_html($autor->display_name); ?></option>
						<?php } ?>

					</select>

				</fieldset>

				<fieldset class="flex width-48">
					<label for="status" class="width-100">
						Status
						<small>Qual status quer que o post tenha após a importação. Com o status "publicado" o conteúdo já vai direto para o seu site.</small>
					</label>

					<select required name="status" id="status">
						<option value="">Selecione</option>
						<option value="publish" <?php if( get_option( 'br61_status' ) && get_option( 'br61_status' ) == "publish" ){ echo 'selected'; } ?>>Publicado</option>
						<option value="draft" <?php if( get_option( 'br61_status' ) && get_option( 'br61_status' ) == "draft" ){ echo 'selected'; } ?>>Rascunho</option>
						<option value="pending" <?php if( get_option( 'br61_status' ) && get_option( 'br61_status' ) == "pending" ){ echo 'selected'; } ?>>Revisão pendente</option>
					</select>

				</fieldset>

				<fieldset class="flex width-48">
					<label for="cron" class="width-100">
						Verificar novos posts
						<small>Selecione o intevalo de tempo para que o wordpress acesse o Brasil 61 em busca de novos conteúdos</small>
					</label>

					<select required name="cron" id="cron">
						<option value="">Selecione</option>
						<option value="thirty_minutes" <?php if( get_option( 'br61_cron' ) && get_option( 'br61_cron' ) == "thirty_minutes" ){ echo 'selected'; } ?>>A cada 30 minutos</option>
						<option value="hourly" <?php if( get_option( 'br61_cron' ) && get_option( 'br61_cron' ) == "hourly" ){ echo 'selected'; } ?>>A cada hora</option>
						<option value="daily" <?php if( get_option( 'br61_cron' ) && get_option( 'br61_cron' ) == "daily" ){ echo 'selected'; } ?>>1x por dia</option>
						<option value="twicedaily" <?php if( get_option( 'br61_cron' ) && get_option( 'br61_cron' ) == "twicedaily" ){ echo 'selected'; } ?>>2x por dia</option>
						<option value="weekly" <?php if( get_option( 'br61_cron' ) && get_option( 'br61_cron' ) == "weekly" ){ echo 'selected'; } ?>>Semanalmente</option>
					</select>

				</fieldset>

				<fieldset class="flex width-48">
					<label for="data_importacao" class="width-100">
						Data inicial da importação
						<small>Você pode importar conteúdos de datas passadas. A partir de qual dia gostaria de importar conteúdos?</small>
					</label>
					<input type="date" name="data_importacao" id="data_importacao" min="2023-01-01" value="<?php if( get_option( 'br61_data_importacao' ) ){ echo esc_html(get_option( 'br61_data_importacao' )); }else{echo esc_html(date('Y').'-01-01');} ?>">
				</fieldset>

				<?php wp_nonce_field( 'br61_salvar_configuracoes' ); ?>

				<div class="width-100 text-center">
					<button type="submit">Salvar configurações</button>
					<input type="hidden" name="action" value="br61_salvar_configuracoes">
				</div>

			</form>


<?php
		echo '</div>';        

	echo '</section>';        

}