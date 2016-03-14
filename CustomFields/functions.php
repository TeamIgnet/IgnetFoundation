<?php
function IGNET_DEF_get_extra_input_FILE( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	
	wp_enqueue_script('IGNET_C_FIELDS-admin-mediauploader');

	$my_post = $post;
	$name = $data['name'];
	$slug = $data['slug'];
	
	$mimeType = empty($data['mimeType'])?'image':$data['mimeType'];

	$buttonText = empty($data['buttonText'])?'Добавить '.$mimeType:$data['buttonText'];
	
	$multiple = !isset($data['multiple'])?1:$data['multiple'];
	$multiple = ($multiple==0)?0:1;
	
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';

	//print_r($data);
	
	//Уже выбранные данные
    $value_array = get_post_meta($my_post->ID, $slug, 0);
	$value = implode(",", $value_array);
	
	if( $data['default'] )
		$value = empty($value)?$data['default']:$value;
	
	$multiple_class = ($multiple==0 AND !empty($value))?'none_multiple':'';

	$return = '<div class="extra_field_box extra_field_file '.$multiple_class.' ">'.$before;

	$args = array( 
		'post_type' => 'attachment',
		'post_status' => null,
		'include' => $value
	);

	$return .= '<div class="tumb_results">
					<input 
					type="hidden" 
					name="IGNET_extra_fields[file]['.$slug.']" 
					id="'.$slug.'" 
					class="file_hidden_input" 
					value="'.$value.'" />';
	if( !empty($value) ){
		$attachments = get_posts( $args );
	}
	else{
		$attachments = array();
	}	
	
	if ($attachments) {
		foreach ( $attachments as $att ) {
			$mime_type = substr($att->post_mime_type, 0, strpos($att->post_mime_type, '/'));
			
			if($mime_type == 'video'){
				$return .=
				'<div 
				class="mediaplayer_box" 
				att_id="'. $att->ID .'">
					<div class="">'
					. wp_video_shortcode( array('src' => $att->guid) ) .
					'</div>
					<a class="remove_image_button button" title=" Удалить? ">×</a>
				</div>';
			}
			elseif($mime_type == 'audio'){
				$return .=
				'<div 
				class="mediaplayer_box mediaplayer_box_audio" 
				att_id="'. $att->ID .'">
					<div class="">'
					. wp_audio_shortcode( array('src' => $att->guid) ) .
					'</div>
					<a class="remove_image_button button" title=" Удалить? ">×</a>
				</div>';				
			}
			else{
				$metadata = wp_get_attachment_image_src( $att->ID, 'thumbnail', true );
				$return .=
				'<div 
				class="image_box" 
				style="background-image: url('.$metadata[0].')"
				att_id="'. $att->ID .'">
					<span>' .$att->post_title. '</span>
					<a class="remove_image_button button" title=" Удалить? ">×</a>
				</div>';
			}
		}
	}
	
	$return .= '</div>';// Ранее сохраненные данные
	$return .= '
		<div class="upload_image_button_div">
			<button 
			type="submit"
			multi="'.$multiple.'"
			mime_type="'.$mimeType.'"
			id="upload_image_button_'.$slug.'" 
			class="upload_image_button button">'.$buttonText.'</button>
		</div>' .$after;
	$return .= '</div>';	
	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_SELECT2( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	
	wp_enqueue_script('IGNET_C_FIELDS-admin-select2');
	
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	$targetPostType = empty($data['targetPostType'])?'post':$data['targetPostType'];
	$multiple = ($data['multiple'] == 0)?'false':'true';
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';

	//print_r($data['targetPostTax']);
	$tax_query = array();
	if( !empty($data['targetPostTax']) ){
		
		if(!empty($data['targetPostTax']['slugs'])){
			$terms = $data['targetPostTax']['slugs'];
			$field = 'slug';
		}
		
		if(!empty($data['targetPostTax']['ids'])){
			$terms = $data['targetPostTax']['ids'];
			$field = 'id';
		}
		
		$operator = ($data['targetPostTax']['operator'] == '!=')?'NOT_IN':'IN';
		
		$tax_query = array(
							'taxonomy' => $data['targetPostTax']['name'],
							'field' => $field,
							'terms' => $terms,
							'operator' => $operator
						);
	}
	
	//print_r($data['targetPostField']);
	$meta_query = array();
	if( !empty($data['targetPostField']) ){
		
		if(!empty($data['targetPostField']['slugs'])){
			$terms = $data['targetPostField']['slugs'];
		}
		
		if(!empty($data['targetPostField']['ids'])){
			$terms = $data['targetPostField']['ids'];
		}
		
		$operator = ($data['targetPostField']['operator'] == '!=')?'NOT IN':'IN';
		
		$meta_query = array(
							'key' => $data['targetPostField']['name'],
							'value' => $terms,
							'compare' => $operator
						);
	}
	
	$size = $data['size']?$data['size']:'100%';
	$width_class = ($size != '100%')?'_one':'';
	//Уже выбранные данные
    $value = get_post_meta($my_post->ID, $slug, 0);
	
	if( $data['default'] )
	$value = empty($value)?$data['default']:implode(",", $value);
	
	if( !empty($value) ){
		$selected = get_posts( array(
							'post_type' => $targetPostType,
							'post_status' => 'any',
							'include'   => $value
						)); 	
	}
	else{
		$selected = array();
	}

	
	$selected_data = array();
	foreach ($selected as $one_selected_post):
		$selected_data[] = array(
								'id' => $one_selected_post->ID, 
								'title' => $one_selected_post->post_title
							);
	endforeach;
	
	$my_post_id = $my_post->ID;
	$my_post_id = empty($my_post_id) ? 0 : $my_post_id;
	
	$selected_data = apply_filters( 'ignet_select2_selected_data', $selected_data, $my_post_id );
	$selected_data = apply_filters( "ignet_select2_{$slug}_selected_data", $selected_data, $my_post_id );
	
	$selected_data = ($multiple == 'false')?json_encode($selected_data[0]):json_encode($selected_data);

	$return = '<div class="extra_field_box extra_field_select2'.$width_class.' "><label>'.$before;
	
	$placeholder = empty($data['placeholder']) ? 'Добавить...': $data['placeholder'];
	$placeholder = apply_filters( 'ignet_select2_placeholder', $placeholder, $my_post_id );
	$placeholder = apply_filters( "ignet_select2_{$slug}_placeholder", $placeholder, $my_post_id );
	
	$minimumInputLength = 2;
	$minimumInputLength = apply_filters( 'ignet_select2_minimumInputLength', $minimumInputLength, $my_post_id );
	$minimumInputLength = apply_filters( "ignet_select2_{$slug}_minimumInputLength", $minimumInputLength, $my_post_id );
	
	$dataType = 'json';
	$dataType = apply_filters( 'ignet_select2_dataType', $dataType, $my_post_id );
	$dataType = apply_filters( "ignet_select2_{$slug}_dataType", $dataType, $my_post_id );
	
	$quietMillis = 100;
	$quietMillis = apply_filters( 'ignet_select2_quietMillis', $quietMillis, $my_post_id );
	$quietMillis = apply_filters( "ignet_select2_{$slug}_quietMillis", $quietMillis, $my_post_id );
	
	$formatResult = 'function(element){return element.title;}';
	$formatResult = apply_filters( 'ignet_select2_formatResult', $formatResult, $my_post->ID );
	$formatResult = apply_filters( "ignet_select2_{$slug}_formatResult", $formatResult, $my_post_id );
	
	$formatSelection = 'function(element){return element.title;}';
	$formatSelection = apply_filters( 'ignet_select2_formatSelection', $formatSelection, $my_post_id );
	$formatSelection = apply_filters( "ignet_select2_{$slug}_formatSelection", $formatSelection, $my_post_id );
	
	$onChange = 'function (e) { /*console.log("change", e);*/ }';
	$onChange = apply_filters( 'ignet_select2_onChange', $onChange, $my_post_id );
	$onChange = apply_filters( "ignet_select2_{$slug}_onChange", $onChange, $my_post_id );
	
	$return .= 
		'<input type="hidden" '.
		'value="'.$value.'" '.
		'data-init-text=""'.
		'name="IGNET_extra_fields[select2]['.$slug.']" '.
		'id="'.$slug.'" '. 

		'style="width:'.$size.';" />';
		
	$return .= 	
		$after.
		'</label>
		<script>
		( function( $ ) {
				$(window).load(function() {
					$("#'.$slug.'").select2({
						placeholder: "'.$placeholder.'",
						formatInputTooShort: function (input, min) { 
							return "Пожалуйста, введите " + (min - input.length) + " или более символов"; 
						},
						minimumInputLength: '.$minimumInputLength.',
						formatSearching: function () { return "Поиск..."; },
						formatNoMatches: function () { return "Ничего не найдено"; },

						multiple: '.$multiple.',
						
						ajax:{
							url: "'.admin_url('admin-ajax.php').'",
							dataType: "'.$dataType.'",
							quietMillis: '.$quietMillis.',
							data: function (term, page) { // page is the one-based page number tracked by Select2
								return {
										action: "ajax_query_fields",
										post_type: '.json_encode($targetPostType).',
										q_slug: "'.$slug.'", // page size
										page_limit: 10, // page size
										page: page, // page number
										q: term, //search term
										tax_query: '.json_encode($tax_query).',
										meta_query: '.json_encode($meta_query).'
									};
							},
							results: function (data, page) {
								//alert(data.total);
								var more = (page * 10) < data.total; // whether or not there are more results available

								// notice we return the value of more so Select2 knows if more results can be loaded
								return {
										results: data.elements,
										more: more
									};
							}
						},

						formatResult: '.$formatResult.', // omitted for brevity, see the source of this page
						
						formatSelection: '.$formatSelection.', // omitted for brevity, see the source of this page
						
						dropdownCssClass: "bigdrop", // apply css that makes the dropdown taller
						
						escapeMarkup: function (m) { return m; }, // we do not want to escape markup since we are displaying html in results

					})
					.on("change", '.$onChange.');
					
					//Если есть данные о значении, то делаем выбор
					$("#'.$slug.'").select2(
						"data", '.$selected_data.'
					);
				})
		})( jQuery );
		</script> 
		</div>';

	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_DATETIME( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
	if($data['format'] == 'yy:mm:dd hh:mm:ss'){
		$date_format = true;
		$time_format = 'HH:mm:ss';
	}
	elseif($data['format'] == 'yy:mm:dd hh:mm'){
		$date_format = true;
		$time_format = 'HH:mm';		
	}
	elseif($data['format'] == 'yy:mm:dd hh'){
		$date_format = true;
		$time_format = 'HH';		
	}	
	elseif($data['format'] == 'yy:mm:dd'){
		$date_format = true;
		$time_format = false;
	}	
	elseif($data['format'] == 'hh:mm:ss'){
		$date_format = false;
		$time_format = 'HH:mm:ss';
	}
	elseif($data['format'] == 'hh:mm'){
		$date_format = false;
		$time_format = 'HH:mm';		
	}	
	else{
		$date_format = true;
		$time_format = false;		
	}	

	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';

	$value = get_post_meta($my_post->ID, $slug, 1);
	
	if( $data['default'] )
	$value = empty($value)?$data['default']:$value;
	
	$defaultDate = ( $value )?'"'.$value.'"':'';

	$size = $data['size']?'width:'.$data['size'].'':'';

	$stepHour = is_numeric($data['stepHour'])?$data['stepHour']:1;
	$stepMinute = is_numeric($data['stepMinute'])?$data['stepMinute']:5;
	$stepSecond = is_numeric($data['stepSecond'])?$data['stepSecond']:10;

	$hour = is_numeric($data['default_hour'])?$data['default_hour']:0;
	$minute = is_numeric($data['default_minute'])?$data['default_minute']:0;
	$second = is_numeric($data['default_second'])?$data['default_second']:0;
	
	wp_enqueue_script('datepicker-ru');
	$onSelect = 'null';
	$onSelect = apply_filters( 'ignet_datetime_onSelect', $onSelect, $my_post_id );
	$onSelect = apply_filters( "ignet_datetime_{$slug}_onSelect", $onSelect, $my_post_id );
	
	if(!$time_format){
		$return .= 	
		'<script type="text/javascript">
		( function( $ ) {
			$(window).load(function() {
				$("#'.$slug.'").datepicker({
					defaultDate: new Date('.$defaultDate.'),
					onSelect: '.$onSelect.'
				});
			});
		})( jQuery );
		</script>';
	}
	elseif($date_format){
		wp_enqueue_script('jqueryUItimepickerAddoniRU');
		
		$return .= 	
		'<script type="text/javascript">
		( function( $ ) {
			$(window).load(function() {
				$("#'.$slug.'").datetimepicker({
					timeFormat: "'.$time_format.'",
					
					stepHour: '.$stepHour.',
					stepMinute: '.$stepMinute.',
					stepSecond: '.$stepSecond.',

					hour: '.$hour.',
					minute: '.$minute.',
					second: '.$second.',
					
					defaultDate: new Date('.$defaultDate.'),
					onSelect: '.$onSelect.'
				});
				
			});
		})( jQuery );
		</script>';		
	}
	else{
		wp_enqueue_script('jqueryUItimepickerAddoniRU');
		
		$return .= 	
		'<script type="text/javascript">
		( function( $ ) {
			$(window).load(function() {
				$("#'.$slug.'").timepicker({
					timeFormat: "'.$time_format.'",
					
					stepHour: '.$stepHour.',
					stepMinute: '.$stepMinute.',
					stepSecond: '.$stepSecond.',

					hour: '.$hour.',
					minute: '.$minute.',
					second: '.$second.',
					
					defaultDate: new Date('.$defaultDate.'),
					onSelect: '.$onSelect.'
				});
				
			});
		})( jQuery );
		</script>';			
	}	

	
	$return .= '<div class="extra_field_box extra_field_date"><label>'.
				$before.
				' <input  type="text" '.
				'placeholder="гггг-мм-дд"'.
				'id="'.$slug.'" '. 
				'name="IGNET_extra_fields['.$slug.']" '.
				'value="'.$value.'" '.
				'style="'.$size.'" />'.
				$after.
				'</label></div>';
				
	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_TEXT( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	$placeholder = empty($data['placeholder']) ? '': $data['placeholder'];
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';

	$size = $data['size']?$data['size'].'':'';
	if(strstr($data['size'], '%') == '%'){
		$divsize = explode('%', $data['size']);
		$divsize = $divsize[0] - 2;
		$divsize = 'width:'.$divsize.'%';
		$size = '100%';
	}
	else{
		$divsize = '';
	}
	
	if( !empty($data['clone']) ){
		$clone_button = empty($data['clone_button'])? 'Еще' : $data['clone_button'];
		
		$value = get_post_meta($my_post->ID, $slug);
		if( $data['default'] )
			$value = empty($value)?$data['default']:$value;		
		
		$return = '<div class="extra_field_box extra_field_text extra_field_text_clone" 
						style="'.$divsize.'"><div>'.$before;		
		
				if( is_array($value) AND !empty($value) ){
					$delete_input = '';
					foreach($value as $val){
						$return .= '<div class="input_div"> <input  type="text" '.
							'class="'.$slug.'" '. 
							'name="IGNET_extra_fields[ignet_clone_texts]['.$slug.'][]" '.
							'value="'.$val.'" '.
							'placeholder="'.$placeholder.'" '.
							'style="width:'.$size.'" />'.$delete_input.'</div>';
						$delete_input = '<span class="delete_input"></span>';
					}
				} else {
					$return .= '<div class="input_div"> <input  type="text" '.
					'class="'.$slug.'" '. 
					'name="IGNET_extra_fields[ignet_clone_texts]['.$slug.'][]" '.
					'value="" '.
					'placeholder="'.$placeholder.'" '.
					'style="width:'.$size.'" /></div>';
				}

		$return .= $after . '</div><div class="more_input_div"><span class="more_input button button-primary button-large">'.$clone_button.'</span></div>';
		$return .= '</div>';		
	} 
	else{
		$value = get_post_meta($my_post->ID, $slug, 1);
		if( $data['default'] )
		$value = empty($value)?$data['default']:$value;	
		$return = '<div class="extra_field_box extra_field_text" 
						style="'.$divsize.'"><label>'.
					$before.
					' <input  type="text" '.
					'class="'.$slug.'" '. 
					'name="IGNET_extra_fields['.$slug.']" '.
					'value="'.$value.'" '.
					'placeholder="'.$placeholder.'" '.
					'style="width:'.$size.'" />'.
					$after.
					'</label>
					</div>';		
	}

	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_NUMBER( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	$placeholder = empty($data['placeholder']) ? '': $data['placeholder'];
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';

	$value = get_post_meta($my_post->ID, $slug, 1);
	
	if( $data['default'] )
	$value = empty($value)?$data['default']:$value;
	
	$size = $data['size']?'width:'.$data['size'].'':'';

	$return = '<div class="extra_field_box extra_field_text"><label>'.
				$before.
				' <input  type="number" '.
				'min="'.$data['default_min'].'"'.
				'max="'.$data['default_max'].'"'.
				'id="'.$slug.'" '. 
				'step="' . $data['step'] . '"' .
				'name="IGNET_extra_fields['.$slug.']" '.
				'value="'.$value.'" '.
				'placeholder="'.$placeholder.'" '.
				'style="'.$size.'" />'.
				$after.
				'</label></div>';
				
	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_TEXTAREA( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	$placeholder = empty($data['placeholder']) ? '': $data['placeholder'];
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';

	$value = get_post_meta($my_post->ID, $slug, 1);
	
	if( $data['default'] )
	$value = empty($value)?$data['default']:$value;
	
	$rows = $data['rows']?$data['rows']:'';
	$cols = $data['cols']?$data['cols']:'';
	
	
	if($data['editor'] == '1'){
		ob_start();
			 $content = $value;
			 $editor_id = str_replace(array('-','_'), '', $slug);
			 $settings = array( 
						'textarea_name' => 'IGNET_extra_fields['.$slug.']',
						'editor_class'  => $slug.'_editor_class',
						
						'media_buttons' => 0, //Показывать медиа кнопку 
						'teeny'         => 0, //Скрыать или нет кнопку расширяющую возможности визуального редактора.
						'tinymce'       => 1, //Загружать визуальный редактор TinyMCE или нет. Можно указать параметры редактора напрямую в массиве array().
						'quicktags'     => 1, //Загружать HTML редактор или нет. Можно указать параметры напрямую в массиве array().
					);
					
				if( !empty($rows) ){
					 $settings['textarea_rows'] = $rows;
				}		

			echo '<div class="extra_field_box extra_field_textarea extra_field_textarea_wp_editor"><label>'.$before;
				wp_editor( $content, $editor_id, $settings );
			echo $after.'</label></div>';
		$return = ob_get_contents();
		ob_end_clean();
	}
	else{
		$return = '<div class="extra_field_box extra_field_textarea"><label>'.
				$before.
				' <textarea '.
					'id="'.$slug.'" '. 
					'placeholder="'.$placeholder.'" '. 
					'name="IGNET_extra_fields['.$slug.']" '.
					'rows="'.$rows.'" '.
					'cols="'.$cols.'" >'.$value.'</textarea>'.
				$after.
				'</label></div>';
	}
	
	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_SELECT( array $data ){
	if( empty($data['name'])or empty($data['slug']) or empty($data['options']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';
	
	if( is_array($data['options']) ){
		$data_options = $data['options'];
	}
	elseif(function_exists( $data['options'] )){
		$data_options = $data['options']($my_post->ID);
	}
	else{
		$data_options = array();
	}
	
	
	$return =  '<div class="extra_field_box extra_field_select">'.
				$before.
				' <select name="IGNET_extra_fields['.$slug.']" />';

		foreach($data_options as $option){
			$value = $option[0];
			$content = isset($option[1])?$option[1]:'';
			$value_from_DB = get_post_meta($my_post->ID, $slug, true);
			$selected = selected( $value_from_DB, $value, false);
			
			if( empty($value_from_DB) AND $data['default'] == $value){
				$selected = 'selected="selected2"';
			}
			$return .= '<option value="'.$value.'" '.$selected.' >'.$content.'</option>';
		}
		
	$return .= '</select>'.
				$after.
				'</div>';
				
	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_RADIO( array $data ){
	if( empty($data['name'])or empty($data['slug']) or empty($data['options']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';
	
	if( is_array($data['options']) ){
		$data_options = $data['options'];
	}
	elseif(function_exists( $data['options'] )){
		$data_options = $data['options']();
	}
	else{
		$data_options = array();
	}
	
	$return =  '<div class="extra_field_box extra_field_radio">'.
				$before;
				
		foreach($data_options as $option){
			$value = $option[0];
			$option_before = isset($option[1])?$option[1]:'';
			$option_after = isset($option[2])?$option[2]:'';
			
			$checked = checked( get_post_meta($my_post->ID, $slug, true), $value, false);
			if( empty($checked) AND $data['default'] == $value){
				$checked = 'checked="checked"';
			}
			
			$return .=  ' <label>'.
						$option_before
						.' <input    type="radio" 
									value="'.$value.'" '.
									'name="IGNET_extra_fields['.$slug.']" '.
									$checked.' /> '.$option_after.'</label>';
		}
		
	$return .=  $after.
				'</div>';
				
	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_CHECKBOX( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';
	
	$value = get_post_meta($my_post->ID, $slug, 1);
	if( $data['default'] )
	$value = (empty($value) and $value != 0)?$data['default']:$value;
	
	$checked = checked($value, 1, false);

	$return =   '<div class="extra_field_box extra_field_checkbox">'.
				'<label>'.
				$before.
				' <input  type="hidden" '. 
				'name="IGNET_extra_fields['.$slug.']" '.
				'value="0" />'.
				
				'<input  type="checkbox" '.
				'id="'.$slug.'" '. 
				'name="IGNET_extra_fields['.$slug.']" '.
				'value="1" '.$checked.' />'.
				$after.
				'</label></div>';
	return $data['before_box'] . $return . $data['after_box'];
}

function IGNET_DEF_get_extra_input_COLOR( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
    wp_enqueue_style( 'wp-color-picker' );        
    wp_enqueue_script( 'wp-color-picker' ); 
    
	$name = $data['name'];
	$slug = $data['slug'];
	
	$before = $data['before']?'<span class="before">'.$data['before'].'</span>':'';
	$after = $data['after']?'<span class="after">'.$data['after'].'</span>':'';
	
	$my_post_id = $my_post->ID;
	$my_post_id = empty($my_post_id) ? 0 : $my_post_id;
	
	$changeFunction = 'function(event, ui){}';
	$changeFunction = apply_filters( 'ignet_color_changeFunction', $changeFunction, $my_post_id );
	$changeFunction = apply_filters( "ignet_color_{$slug}_changeFunction", $changeFunction, $my_post_id );
	
	$clearFunction = 'function(){}';
	$clearFunction = apply_filters( 'ignet_color_clearFunction', $clearFunction, $my_post_id );
	$clearFunction = apply_filters( "ignet_color_{$slug}_clearFunction", $clearFunction, $my_post_id );
	
	$hideBool = 'true';
	$hideBool = apply_filters( 'ignet_color_hideBool', $hideBool, $my_post_id );
	$hideBool = apply_filters( "ignet_color_{$slug}_hideBool", $hideBool, $my_post_id );
	
	$palettesBool = 'true';
	$palettesBool = apply_filters( 'ignet_color_palettesBool', $palettesBool, $my_post_id );
	$palettesBool = apply_filters( "ignet_color_{$slug}_palettesBool", $palettesBool, $my_post_id );
	
	
	if( empty($before) )
	$before = '<span class="before">'.$data['name'].'</span>';
	
	$value = get_post_meta($my_post->ID, $slug, 1);
	if( $data['default'] )
	$value = (empty($value))?$data['default']:$value;

	$return =   '<div class="extra_field_box extra_field_color">'.
				'<script type="text/javascript">
					jQuery(document).ready(function($) {
						$("#'.$slug.'").wpColorPicker({
							change: '.$changeFunction.',
							clear: '.$clearFunction.',
							hide: '.$hideBool.',
							palettes: '.$palettesBool.'
						});
					});
				</script>'.
	
				'<label>'.
				$before.				
				'<input  type="text" '.
				'id="'.$slug.'" '. 
				'name="IGNET_extra_fields['.$slug.']" '.
				'value="'.$value.'"  />'.
				$after.
				'</label></div>';
	return $data['before_box'] . $return . $data['after_box'];
}


//Отдать скрытое поле
function IGNET_DEF_get_extra_input_hidden( array $data ){
	if( empty($data['name']) or empty($data['slug']) ) return false;
	global $post; 
	$my_post = $post;
	
	$name = $data['name'];
	$slug = $data['slug'];
	
	$value = get_post_meta($my_post->ID, 'address_city', 1);
	
	if( $data['default'] )
	$value = empty($value)?$data['default']:$value;

	$return = 	'<input  type="hidden" '.
				'id="'.$slug.'" '. 
				'name="IGNET_extra_fields['.$slug.']" '.
				'value="'.$value.'" />';
	return $data['before_box'] . $return . $data['after_box'];
}

//Отдать поле nonce
function IGNET_DEF_get_extra_fields_nonce(){
	return 	'<input type="hidden" name="IGNET_C_FIELDS_extra_fields_nonce" value="'. wp_create_nonce('nonce').'" /><div class="clear"></div>';
}


// включаем обновление полей при сохранении
add_action('save_post', 'IGNET_DEF_extra_fields_update', 0);
/* Сохраняем данные, при сохранении поста */
function IGNET_DEF_extra_fields_update( $post_ID ){
	//print_r($_POST);
    if ( !wp_verify_nonce( $_POST['IGNET_C_FIELDS_extra_fields_nonce'], 'nonce') ) return false; // проверка

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // выходим если это автосохранение
	
	if ( !current_user_can('edit_post', $post_ID ) ) return false; // выходим если юзер не имеет право редактировать запись

	if( !isset($_POST['IGNET_extra_fields']) ) return false;	// выходим если данных нет
	
	//Выполняем фильтрацию данных перед сохранением
	$IGNET_extra_fields = apply_filters(
								'ignet_с_fields_pre_save_metadata', 
								$_POST['IGNET_extra_fields'], 
								$post_ID
							);
	
	//Теперь, нужно сохранить/удалить данные
	foreach( $IGNET_extra_fields as $key=>$value ){
		if( $key == 'select2' ){
			foreach( $value as $select2_key=>$select2_value ){
				$select2_ids = array_diff(explode(',', $select2_value), array(''));
				delete_post_meta($post_ID , $select2_key);
				
				foreach( $select2_ids as $select2_ID ){
					add_post_meta($post_ID , $select2_key, $select2_ID);
				}
			}
			unset($_POST['IGNET_extra_fields']['select2']);
		}
		elseif( $key == 'file' ){
			foreach( $value as $file_key=>$file_value ){
				$file_ids = array_diff(explode(',', $file_value), array(''));
				delete_post_meta($post_ID , $file_key);
				
				foreach( $file_ids as $file_ID ){
					add_post_meta($post_ID , $file_key, $file_ID);
				}
			}
			unset($_POST['IGNET_extra_fields']['file']);
		}
		elseif( $key == 'ignet_clone_texts' ){
			foreach( $value as $var_name=>$text_array ){
				delete_post_meta($post_ID , $var_name);
				
				foreach( $text_array as $text_value ){
					$text_value = trim($text_value);
					if( !empty( $text_value )){
						add_post_meta($post_ID , $var_name, $text_value);
					}
				}
			}
			unset($_POST['IGNET_extra_fields']['ignet_clone_texts']);
		}
		else{
			if( empty($value) AND $value != 0 ){
				delete_post_meta($post_ID , $key); // удаляем поле если значение пустое
				continue;
			}
			else{
				update_post_meta($post_ID , $key, $value); // add_post_meta() работает автоматически
			}
		}
	}
	
	
	return $post_ID ;
}

//Отдать json данные на ajax запрос поля  select2
add_action( 'wp_ajax_ajax_query_fields', 'IGNET_DEF_ajax_query_fields_callback' );
function IGNET_DEF_ajax_query_fields_callback(){
    $slug = $_GET['q_slug'];
	
	add_filter('posts_search', 'search_only_of_titles', 500, 2);
	
	$args = array(
        'fields' => 'ids',
        's' => $_GET['q'],
        'paged' => $_GET['page'],
        'posts_per_page' => $_GET['page_limit'],
        'post_type' => $_GET['post_type'],
		'tax_query' => array( $_GET['tax_query'] ),
		'meta_query' => array( $_GET['meta_query'] )
        );

	$args = apply_filters( 'ignet_select2_ajax_args', $args );
	$args = apply_filters( "ignet_select2_{$slug}_ajax_args", $args );
	
    $query = new WP_Query( $args );

    $elements = array();
    foreach ($query->posts as $post_id){
        $elements[] = array(
            'id' => $post_id,
            'title' => get_the_title($post_id)
            );
    }
	
	$elements = apply_filters( 'ignet_select2_result_data', $elements );
	$elements = apply_filters( "ignet_select2_{$slug}_result_data", $elements ); 
	
    $data[] = array(
        "total" => (int)$query->found_posts, 
        'elements' => $elements
		);

	wp_send_json($data[0]);
	die;
}

 
function search_only_of_titles($search, &$wp_q) {
    global $wpdb;
    if (empty($search)) :
        return $search; // пропустить обработку - нет поискового запроса.
    endif;
    $q = $wp_q->query_vars;
    $n = !empty($q['exact']) ? '' : '%';
    $search = $poiskand = '';
    foreach ((array) $q['search_terms'] as $term) :
        $term = esc_sql(like_escape($term));
        $search .= "{$poiskand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
        $poiskand = ' AND ';
    endforeach;
    if (!empty($search)) :
        $search = " AND ({$search}) ";
        if (!is_user_logged_in()) :
            $search .= " AND ($wpdb->posts.post_password = '') ";
        endif;
    endif;
    return $search;
}