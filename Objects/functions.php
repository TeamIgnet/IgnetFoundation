<?php
/*******************Function to Register Objects***********************/

//Функция регистрации объекта, по заданным конфигурационным данным
function IGNET_DEF_ObjectRegister( array $data ){
	if( $data['slug']=='post' OR $data['slug']=='page' ) return false;
	if( empty($data['slug']) ) return false;
	
	$slug = $data['slug'];
	$name = $data['name']?$data['name']:'Объекты';
	$singularName = $data['singularName']?$data['singularName']:'Объект';
	$menuPosition = $data['menuPosition']?$data['menuPosition']:null;
	$menuIcon = empty($data['menuIcon'])?null:$data['menuIcon'];
	$taxonomies = $data['taxonomies']?$data['taxonomies']:array();
	$supports = $data['supports'];
	
	$publicly_queryable = $data['publicly_queryable'] == '0' ? $data['publicly_queryable'] : 1;
	$exclude_from_search = $data['exclude_from_search'] == '1' ? $data['exclude_from_search'] : 0;
	$show_ui = $data['show_ui'] == '0' ? $data['show_ui'] : 1;
	$show_in_menu = $data['show_in_menu'] == '0' ? $data['show_in_menu'] : 1;

	if ( $data['public'] == '0' ) {
		$public = 0;
		$publicly_queryable = 0;
		$exclude_from_search = 0;
		$show_ui = 0;
		$show_in_menu = 0;
	} else {
		$public = 1;
	}
	
	
	$labels = array(
		 'name' => $name // основное название для типа записи
		,'singular_name' => $singularName // название для одной записи этого типа
		,'add_new' => 'Добавить ' // для добавления новой записи
		,'add_new_item' => 'Добавляем ' // заголовка у вновь создаваемой записи в админ-панели.
		,'edit_item' => 'Редактировать ' // для редактирования типа записи
		,'new_item' => 'Новый объект' // текст новой записи
		,'view_item' => 'Посмотреть ' // для просмотра записи этого типа.
		,'search_items' => 'Поиск' // для поиска по этим типам записи
		,'not_found' => 'Не найдено ни одного объекта' // если в результате поиска ничего не было найдень
		,'not_found_in_trash' => 'В корзине не найдено ни одного объекта' // если не было найдено в корзине
		,'parent_item_colon' => '' // для родительских типов. для древовидных типов
		,'menu_name' => $name // название меню
	);
	$args = array(
		 'label' => null //Имя типа записи помеченное для перевода на другой язык
		,'labels' => $labels 
		,'description' => ''
		
		,'publicly_queryable' => (boolean) $publicly_queryable //Запросы относящиеся к этому типу записей будут работать во фронтэнде (в шаблоне сайта)
		,'exclude_from_search' => (boolean) $exclude_from_search //Исключить ли этот тип записей из поиска по сайту
		,'show_ui' => (boolean) $show_ui //Показывать ли меню для управления этим типом записи в админ-панели. 
		,'show_in_menu' => (boolean) $show_in_menu //Показывать ли тип записи в администраторском меню и где именно показывать управление этим типом записи. 		
		,'public' => (boolean) $public //показывать ли эту менюшку в админ-панели.
		
		,'menu_position' => (int) $menuPosition // Позиция где должно расположится меню нового типа записи
		,'menu_icon' => null //Ссылка на картинку, которая будет использоваться для этого меню		
		,'query_var' => true
		,'rewrite' => true
		,'capability_type' => 'post' 
		,'menu_icon' => $menuIcon	
		,'has_archive' => true
		,'hierarchical' => false //Будут ли записи этого типа иметь древовидную структуру (как постоянные страницы)
		,'supports' => $supports //Вспомогательные поля на странице создания/редактирования этого типа записи.
		,'show_in_nav_menus' => true
	);
	
	if( !empty($taxonomies) ) $args['taxonomies'] = $taxonomies; //Массив зарегистрированных таксономий, которые будут связанны с этим типом записей	
	
	register_post_type( $slug, $args );
}