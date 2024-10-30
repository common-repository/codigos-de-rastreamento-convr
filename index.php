<?php /*
 * Plugin Name: Gerenciador de Códigos de Rastreamento Convr 
 * Plugin URI:        https://convr.com.br/
 * Description:       Instale e gerencie códigos de rastreamento e pixels de um jeito fácil e simples. Compatível com  Facebook, Google Ads, Convr e mais.
 * Version:           0.0.4
 * Requires at least: 4.0
 * Requires PHP:      4.5
 * Author:            Multiverso Design
 * Author URI:        https://multiverso.pro/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       convr
*/

add_action( 'admin_menu', 'gcrc_convr_menu' );

function gcrc_convr_menu() {
    add_menu_page( 'Código de Rastreamento', 'Código de Rastreamento', 'manage_options', 'gcrc_codigo', 'gcrc_codigo_convr', plugins_url( 'codigos-de-rastreamento-convr/img/icone.png' ), 2  );
    add_submenu_page( 'gcrc_codigo', 'Adicionar Código', 'Adicionar Código', 'manage_options', 'gcrc_adicionar_codigo', 'gcrc_adicionar_codigo');
}

function gcrc_codigo_convr(){

    wp_enqueue_script( 'Bootstrap', plugins_url( '/js/bootstrap.min.js' , __FILE__ ), array(),  '1.1.' . rand(0, 10000));
    wp_register_style( 'Bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ), array(), '1.1.' . rand(0, 10000));

	wp_enqueue_script( 'Convr-script', plugins_url( '/js/main.js' , __FILE__ ));
	wp_register_style( 'Convr-style', plugins_url( '/css/estilo.css', __FILE__ ), array(), '1.1.' . rand(0, 10000) );
	
	wp_enqueue_style( 'Bootstrap' );
	wp_enqueue_style( 'Convr-style' );
    
    
	if(function_exists( 'wp_enqueue_media' )){
	wp_enqueue_media();
	}else{
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
	}
	$alerta = '';
	if(isset($_GET['excluir'])){
		$post_id = wp_strip_all_tags($_GET['excluir']);
		if ( 'publish' == get_post_status ( $post_id ) ) {
			$deletou = wp_delete_post( $post_id, true );
			if($deletou){
				$alerta = '<div class="alert alert-success" role="alert">Seu código foi removido.</div>'; 
			}
		}
	}
	
?>
    <div class="convr">
        <div class="dentro">
            <div class="row">
                <div class="col-1"></div>
                <div class="col"><?=$alerta;?></div>
            </div>
            <div class="row primeira">
                <div class="col-lg-1"></div>
                <div class="col-lg-2"><img src="<?=plugins_url( '/img/logo.png' , __FILE__ );?>"/></div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-1"></div>
                <div class="col-lg-7">
                    <h1>Gerenciador de Códigos de Rastreamento</h1>
                    <a class="button" href="<?=menu_page_url( 'gcrc_adicionar_codigo', false );?>">Adicionar Código</a>
                    <h2>Seus Códigos de Rastreamento</h2>
					<div class="codigos">
					<?php $codigos = gcrc_convr_codigos(); ?>
					<div class="table-responsive-sm table-hover">
					  <table class="table">
						<tr><th>Nome</th><th width="80px">Ações</th></tr>
						<?php foreach($codigos as $id=>$c){ ?>
						<tr><td><?=$c['nome'];?></td><td><a href="<?= menu_page_url('gcrc_adicionar_codigo', false) . '&editar=' . $id; ?>" class="editar"></a><a href="<?=menu_page_url('gcrc_codigo', false) . '&excluir=' . $id?>" class="excluir"></a></td></tr>
						<?php } ?>
					  </table>
					</div>
					</div>
                </div>
                <div class="col-lg-3 px-lg-0"><a href="https://convr.com.br/"><img class="sombra" src="<?=plugins_url( '/img/anuncio.jpg' , __FILE__ );?>" /></a></div>
            </div>
			<div class="row">
                <div class="col-lg-1"></div>
				 <div class="col-lg-10 px-lg-0"><a href="https://convr.com.br/"><img class="sombra" src="<?=plugins_url( '/img/anuncio-inf.png' , __FILE__ );?>" /></a></div>
			</div>
        </div>
    </div> 

<?php

}

function gcrc_convr_codigos($editar = null, $onde = null){
	$codigos = array();
	$args = array();
	if(!$editar){
		$args = array('post_type' => 'codigo_convr', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC', 'post_status' => 'publish');
	} else {
		$args =  array('post_type' => 'codigo_convr', 'post_status' => 'publish', 'p' => $editar);
	}
	
	if($onde){
		$args['meta_query'] = array(
        array(
            'key'     => 'posicao',
            'value'   => $onde,
			'compare' => '=='
        ),
    );
	}
	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
        $the_query->the_post();
		
			$posicao = get_post_meta(get_the_ID(), 'posicao', true);
			$codigo = get_post_meta(get_the_ID(), 'codigo', true);
			$onde = get_post_meta(get_the_ID(), 'onde', true);
			
			$codigos[get_the_ID()] = array(
			'nome' => get_the_title(),
			'posicao' => $posicao,
			'codigo' => $codigo,
			'onde' => $onde,
			); 
			
			$pagina = get_post_meta(get_the_ID(), 'pagina', true);
			if($pagina){
				$codigos[get_the_ID()]['paginas'] = get_post_meta(get_the_ID(), 'paginas', true);
			}
			
			$post = get_post_meta(get_the_ID(), 'post', true);
			if($post){
				$codigos[get_the_ID()]['posts'] = get_post_meta(get_the_ID(), 'posts', true);
			}
			
			$rpagina = get_post_meta(get_the_ID(), 'epagina', true);
			if($rpagina){
				$codigos[get_the_ID()]['esconder_pagina'] = get_post_meta(get_the_ID(), 'epaginas', true);
			}
			
			$rpost = get_post_meta(get_the_ID(), 'epost', true);
			if($rpost){
				$codigos[get_the_ID()]['esconder_post'] = get_post_meta(get_the_ID(), 'eposts', true);
			}
		
			
		}
	}
	wp_reset_postdata();
	return $codigos;
}

function gcrc_convr_paginas(){
	$paginas = array();
	
	$the_query = new WP_Query( array('post_type' => 'page', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'publish') );
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
        $the_query->the_post();
			$paginas[get_the_ID()] = get_the_title(); 
		}
	}
	wp_reset_postdata();
	return $paginas;
}

function gcrc_convr_posts(){
	$posts = array();
	$types = get_post_types( '', 'names');
	unset($types['page']);
	unset($types['codigo_convr']);
	$the_query = new WP_Query( array('post_type' => $types, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'publish') );
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
        $the_query->the_post();
			$posts[get_the_ID()] = get_the_title(); 
		}
	}
	wp_reset_postdata();
	return $posts;
}

function gcrc_valida($codigo){
	
	$validacao = array(
		'script' => array(
			'src' => array(),
			'async' => array(),
			'type' => array(),
			'charset' => array(),
			'defer' => array(),
			'xml:space' => array(),
			'class' => array(),
			'id' => array()
		),
		'a' => array('href' => array(),'class' => array(),'id' => array()),
		'strong' => array('class' => array(),'id' => array()),
		'p' => array('class' => array(),'id' => array()),
		'ul' => array('class' => array(),'id' => array()),
		'li' => array('class' => array(),'id' => array()),
		'div' => array('class' => array(), 'id' => array(), 'data' => array()),
	);
	
	return wp_kses($codigo, $validacao);
	
}

function gcrc_adicionar_codigo(){


	wp_enqueue_script( 'Popper', plugins_url( '/js/popper.min.js' , __FILE__ ), array(),  '1.1.' . rand(0, 1000));

    wp_enqueue_script( 'Bootstrap', plugins_url( '/js/bootstrap.min.js' , __FILE__ ), array(),  '1.1.' . rand(0, 1000));
    wp_register_style( 'Bootstrap', plugins_url( '/css/bootstrap.min.css', __FILE__ ), array(), '1.1.' . rand(0, 1000));
	
	
	
	wp_enqueue_script( 'BootstrapSelect', plugins_url( '/js/bootstrap-select.min.js' , __FILE__ ), array(),  '1.1.' . rand(0, 1000));
	wp_enqueue_script( 'BootstrapSelectPT', plugins_url( '/js/i18n/defaults-pt_BR.min.js' , __FILE__ ), array(),  '1.1.' . rand(0, 1000));
    wp_register_style( 'BootstrapSelect', plugins_url( '/css/bootstrap-select.min.css', __FILE__ ), array(), '1.1.' . rand(0, 1000));

	wp_enqueue_script( 'Convr-script', plugins_url( '/js/main.js' , __FILE__ ), array('jquery'));
	wp_register_style( 'Convr-style', plugins_url( '/css/estilo.css', __FILE__ ), array(), '1.1.' . rand(0, 1000) );
	
	wp_enqueue_style( 'Bootstrap' );
	wp_enqueue_style( 'BootstrapSelect' );
	wp_enqueue_style( 'Convr-style' );
    
    
	if(function_exists( 'wp_enqueue_media' )){
	wp_enqueue_media();
	}else{
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    }
	
	$editar = wp_strip_all_tags($_GET['editar']);
	
    if(isset($_POST['submit'])){
	if($editar){
		
	$qual =	$editar;
	
	update_post_meta($qual, 'codigo', gcrc_valida($_POST['codigo']));
	update_post_meta($qual, 'posicao', wp_strip_all_tags( $_POST['posicao'] ));
	update_post_meta($qual, 'onde', wp_strip_all_tags( $_POST['where'] ));
	update_post_meta($qual, 'pagina', wp_strip_all_tags( $_POST['pagina'] ));
	update_post_meta($qual, 'paginas',  sanitize_text_field($_POST['paginas']) );
	update_post_meta($qual, 'post', wp_strip_all_tags( $_POST['post'] ));
	update_post_meta($qual, 'posts',  sanitize_text_field($_POST['posts']) );
	update_post_meta($qual, 'epagina', wp_strip_all_tags( $_POST['epagina'] ));
	update_post_meta($qual, 'epaginas',  sanitize_text_field($_POST['epaginas']) );
	update_post_meta($qual, 'epost', wp_strip_all_tags( $_POST['epost'] ));
	update_post_meta($qual, 'eposts', sanitize_text_field($_POST['eposts']) );
	
	$my_post = array(
      'ID'           => $qual,
      'post_title'   => wp_strip_all_tags($_POST['nome']),
      'post_content' => 'This is the updated content.',
	);
 
	wp_update_post( $my_post );

	
	$alerta = '<div class="alert alert-success" role="alert">Seu código foi atualizado.</div>'; 
		
	} else {
		
	$codigo = array(
	  'post_title'    => wp_strip_all_tags( $_POST['nome'] ),
	  'post_type'  => 'codigo_convr',
	  'post_status'   => 'publish',
	  'post_author'   => get_current_user_id()
	);

	$qual =	wp_insert_post( $codigo );
	update_post_meta($qual, 'codigo',  gcrc_valida($_POST['codigo']) );
	update_post_meta($qual, 'posicao', wp_strip_all_tags( $_POST['posicao'] ));
	update_post_meta($qual, 'onde', wp_strip_all_tags( $_POST['where'] ));
	update_post_meta($qual, 'pagina', wp_strip_all_tags( $_POST['pagina'] ));
	update_post_meta($qual, 'paginas',  sanitize_text_field($_POST['paginas']) );
	update_post_meta($qual, 'post', wp_strip_all_tags( $_POST['post'] ));
	update_post_meta($qual, 'posts',  sanitize_text_field($_POST['posts']) );
	update_post_meta($qual, 'epagina', wp_strip_all_tags( $_POST['epagina'] ));
	update_post_meta($qual, 'epaginas',  sanitize_text_field($_POST['epaginas']) );
	update_post_meta($qual, 'epost', wp_strip_all_tags( $_POST['epost'] ));
	update_post_meta($qual, 'eposts', sanitize_text_field($_POST['eposts']) );
		$alerta = '<div class="alert alert-success" role="alert">Seu código foi adicionado.</div>'; 
	}
	}
	$codigo = array();
	if($editar){
		$codigo = gcrc_convr_codigos($editar)[$editar];
	}
?>
    <div class="convr">
        <div class="dentro">
            <div class="row">
                <div class="col-1"></div>
                <div class="col"><?=$alerta;?></div>
            </div>
            <div class="row primeira">
                <div class="col-1"></div>
                <div class="col"><img src="<?=plugins_url( '/img/logo.png' , __FILE__ );?>"/></div>
            </div>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-8">
                    <h1><?=($editar ? 'Atualize seu código' : 'Adicione seu novo código');?></h1>
                </div>
                <div class="col"></div>
            </div>
            <form action="<?=($editar ? menu_page_url('gcrc_adicionar_codigo', false) . '&editar=' . $editar :  menu_page_url('gcrc_adicionar_codigo', false));?>" method="post">
				<?= ($editar ? '<input type="hidden" name="editar" value="'.$editar.'"/>' : ''); ?>
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-8">
                        <input type="text" placeholder="Nome do Código" required name="nome" <?= ($codigo['nome'] ? 'value="' . $codigo['nome'] . '"': ''); ?>"/>
                        <span class="c">Cole o Código aqui</span>
                        <textarea name="codigo" required rows="12"><?= ($codigo['codigo'] ? esc_html($codigo['codigo']) : ''); ?></textarea>
                    </div>
                    <div class="col"></div>
                </div>
                <div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-2">
                        <label for="posicao">Posicionar o código</label>
                    </div>
                    <div class="col-6">
                        <select class="selectpicker" name="posicao" id="posicao">
                            <option value="head" <?= ($codigo['posicao'] ? ($codigo['posicao'] == 'head' ? 'selected' : '') : 'selected' ) ; ?>>Antes da tag HEAD</option>
                            <option value="body" <?= ($codigo['posicao'] ? ($codigo['posicao'] == 'body' ? 'selected' : '') : 'selected' ) ; ?>>Antes da tag BODY</option>
                        </select>
                    </div>
                    <div class="col"></div>
                </div>
                <div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-6">
                        <label for="posicao">Onde você quer inserir este código?</label>
                    </div>
                </div>
                <div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-6">
                            <input type="radio" value="all" name="where" id="campo-radio1" <?= ($codigo['onde'] ? ($codigo['onde'] == 'all' ? 'checked' : '') : '' ) ; ?> />
                            <label for="campo-radio1">Em todo site</label>
                            <input type="radio" value="some" name="where" id="campo-radio2" <?= ($codigo['onde'] ? ($codigo['onde'] == 'some' ? 'checked' : '') : '' ) ; ?> />
                            <label for="campo-radio2">Em páginas ou posts específicos</label>
                    </div>
                </div>
				<div class="some" <?= ($codigo['onde'] == 'some' ? 'style="display: block"' : '');?>>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-6">
                            <input type="checkbox" value="1" name="pagina" id="pagina" <?= ($codigo['paginas'] ? 'checked' : '' ); ?> />
							<label for="pagina">Incluir Páginas</label>
                    </div>
                </div>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-8">
					<select class="selectpicker" data-width="100%" name="paginas[]" id="paginas" multiple data-live-search="true">
					<option value="all" <?= ($codigo['paginas'] ? (in_array('all', $codigo['paginas']) != false ? 'selected' : '') : '') ; ?>>Todas</option>
                    <?php $paginas = gcrc_convr_paginas(); foreach($paginas as $id=>$nome) {
						
						echo ($codigo['paginas'] ? (in_array($id, $codigo['paginas']) ? '<option value="'.$id.'" selected>'.$nome.'</option>' : '<option value="'.$id.'">'.$nome.'</option>') : '<option value="'.$id.'">'.$nome.'</option>'); 
						
						} ?>
					</select>
                    </div>
                </div>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-6">
                            <input type="checkbox" value="1" name="post" id="post" <?= ($codigo['posts'] ? 'checked' : '' ); ?> />
							<label for="post">Incluir Posts</label>
                    </div>
                </div>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-8">
					<select class="selectpicker" data-width="100%" name="posts[]" id="posts" multiple data-live-search="true">
					<option value="all" <?= ($codigo['posts'] ? (in_array('all', $codigo['posts']) != false  ? 'selected' : '') : '') ; ?>>Todos</option>
                    <?php $posts = gcrc_convr_posts(); foreach($posts as $id=>$nome) {
							echo ($codigo['posts'] ? (in_array($id, $codigo['posts']) ? '<option value="'.$id.'" selected>'.$nome.'</option>' : '<option value="'.$id.'">'.$nome.'</option>') : '<option value="'.$id.'">'.$nome.'</option>'); 
						} ?>
					</select>
                    </div>
                </div>
                <div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-6">
                        <label for="posicao">Você quer excluir este código de determinadas páginas ou posts?</label>
                    </div>
                </div>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-6">
                            <input type="checkbox" value="1" name="epagina" id="epagina" <?= ($codigo['esconder_pagina'] ? 'checked' : '' ); ?> />
							<label for="epagina">Excluir Páginas</label>
                    </div>
                </div>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-8">
					<select class="selectpicker" data-width="100%" name="epaginas[]" id="epaginas" multiple data-live-search="true">
                    <?php $paginas = gcrc_convr_paginas(); foreach($paginas as $id=>$nome) { 
							echo ($codigo['esconder_pagina'] ? (in_array($id, $codigo['esconder_pagina']) ? '<option value="'.$id.'" selected>'.$nome.'</option>' : '<option value="'.$id.'">'.$nome.'</option>') : '<option value="'.$id.'">'.$nome.'</option>'); 
					} ?>
					</select>
                    </div>
                </div>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-6">
                            <input type="checkbox" value="1" name="epost" id="epost" <?= ($codigo['esconder_post'] ? 'checked' : '' ); ?> />
							<label for="epost">Excluir Posts</label>
                    </div>
                </div>
				<div class="row align-items-center espaco">
                    <div class="col-1"></div>
                    <div class="col-8">
					<select class="selectpicker" data-width="100%" name="eposts[]" id="eposts" multiple data-live-search="true">
                    <?php $posts = gcrc_convr_posts(); foreach($posts as $id=>$nome) { 
							echo ($codigo['esconder_post'] ? (in_array($id, $codigo['esconder_post']) ? '<option value="'.$id.'" selected>'.$nome.'</option>' : '<option value="'.$id.'">'.$nome.'</option>') : '<option value="'.$id.'">'.$nome.'</option>'); 
					} ?>
					</select>
                    </div>
                </div>
                </div>
                <div class="row align-items-center espaco">
                    <div class="col-1"></div>
					<div class="col-5">
						<a href="<?=menu_page_url('gcrc_codigo', false);?>" style="color: #b3b3b3" class="cancelar">Cancelar</a>
					</div>
                    <div class="col-3">
                           <input type="submit" class="button" name="submit" value="<?=($editar ? 'Atualizar Código' : 'Adicionar Código' );?>" />
                    </div>
                </div>
            </form>
        </div>
    </div> 

<?php  

}

function gcrc_posts_convr() {
 
    register_post_type( 'codigo_convr',
        array(
            'labels' => array(
                'name' => __( 'Códigos do Convr' ),
                'singular_name' => __( 'Código do Convr' )
            ),
            'public' => false,
			'exclude_from_search' => true,
			'show_ui' => false,
			'show_in_nav_menus' => false,
			'show_in_menu' => false,
			'show_in_admin_bar' => false, 
            'has_archive' => false,
			'supports' => array( 'title' ),
            'rewrite' => array('slug' => 'codigo_convr'),
        )
    );
}
add_action( 'init', 'gcrc_posts_convr' );

function gcrc_codes($onde){
		$codigos = gcrc_convr_codigos(null, $onde);
	
	foreach($codigos as $id=>$c){
		if($c['onde'] == 'all'){
			echo $c['codigo'];
		} else {
			
			if($c['paginas'] && get_post_type() == 'page'){
				$todasp = in_array('all', $c['paginas']);
				$removidap = false;
				if($c['esconder_pagina']){
				$removidap = array_search(get_the_ID(), $c['esconder_pagina']);
				}
				if($todasp != false && $removidap === false){
					echo $c['codigo'];
				}	
			}
			
			if($c['posts'] && get_post_type() != 'page'){
				$todasp = in_array('all', $c['posts']);
				$removidap = false;
				if($c['esconder_post']){
				$removidap = array_search(get_the_ID(), $c['esconder_post']);
				}
				if($todasp != false && $removidap === false){
					echo $c['codigo'];
				}	
			}
		}
	}
}


function gcrc_convr_footer() {
		gcrc_codes('body');
}
add_action( 'wp_footer', 'gcrc_convr_footer' );

function gcrc_convr_header() {
	gcrc_codes('head');
}
add_action('wp_head', 'gcrc_convr_header');