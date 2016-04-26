<?php
/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @subpackage  Field_Multi_Text
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_multi_social' ) ) {

    /**
     * Main ReduxFramework_multi_social class
     *
     * @since       1.0.0
     */
    class ReduxFramework_multi_social {
    
        /**
         * Field Constructor.
         *
         * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        function __construct( $field = array(), $value ='', $parent ) {
        
            //parent::__construct( $parent->sections, $parent->args );
            $this->parent = $parent;
            $this->field = $field;
			$this->field2 = $field['custom'];
            $this->value = $value;
        
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function render() {
			$i = 0;
            $this->add_text = ( isset($this->field['add_text']) ) ? $this->field['add_text'] : __( 'Add More', 'redux-framework');
            
            $this->show_empty = ( isset($this->field['show_empty']) ) ? $this->field['show_empty'] : true;

            echo '<ul id="' . $this->field['id'] . '-ul" class="redux-multi-text">';
        
                if( isset( $this->value ) && is_array( $this->value ) ) {
                    foreach( $this->value as $k => $value ) {
                        if( $value != '' ) {
                            echo '<li><input type="text" id="' . $this->field['id'] . '-' . $k . '" name="' . $this->field['name'] . '[]' . $this->field['name_suffix'] . '" value="' . esc_attr( $value ) . '" class="regular-text ' . $this->field['class'] . '" />';
			
							echo $this->fontAwesomeSelect($this->field2, $k);

							echo ' <a href="javascript:void(0);" class="multi-social-remove">' . __( 'Remove', 'redux-framework' ) . '</a></li>';
                        }
						$i++;
                    }
                } elseif($this->show_empty == true ) {

                    echo '<li><input type="text" id="' . $this->field['id'] . '" name="' . $this->field['name'] . '[]' . $this->field['name_suffix'] . '" value="" class="regular-text ' . $this->field['class'] . '" />';

					// Font Awesome selection
					 echo $this->fontAwesomeSelect($this->field2);


					echo ' <a href="javascript:void(0);" class="multi-social-remove">' . __( 'Remove', 'redux-framework' ) . '</a></li>';
					$i++;
                }
            
                echo '<li style="display:none;"><input type="text" id="' . $this->field['id'] . '" name="" value="" class="regular-text" />';
				$next = $i++;
				echo $this->fontAwesomeSelect($this->field2, $next, true);

				echo ' <a href="javascript:void(0);" class="multi-social-remove">' . __( 'Remove', 'redux-framework') . '</a></li>';

            echo '</ul>';
            $this->field['add_number'] = ( isset( $this->field['add_number'] ) && is_numeric( $this->field['add_number'] ) ) ? $this->field['add_number'] : 1;
            echo '<a href="javascript:void(0);" class="button button-primary redux-multi-social-add" data-add_number="'.$this->field['add_number'].'" data-id="' . $this->field['id'] . '-ul" data-name="' 
				. $this->field['name'] . '[]" data-name-select="' 
				. $this->parent->args['opt_name'] . '[opt-multi-fa][]">' . $this->add_text . '</a><br/>';

        }   


		/** 
		 * Get Font Awesome selection Function
		 *
		 */
		public function fontAwesomeSelect($custom, $key=0, $hidden=false) {
			$ext = '-' . $key;
			$options = get_option( $this->parent->args['opt_name']);
			$field2 = array();	
	        $field2['data'] = $custom['data'];
            $field2['args'] = array();
            $field2['id'] = $custom['id'] . $ext;
			if ($hidden) {
				$field2['name'] = '';
			} else {
            	$field2['name'] = $this->parent->args['opt_name'] . '[' . $custom['id'] . ']';
			}
            $field2['name_suffix'] = '';
			$this->value = isset($options[$custom['id']]) ? $options[$custom['id']] : '';
            $selection = '';
			$multi = '';

            //$icons_file = ReduxFramework::$_dir . 'inc/fields/select/font-awesome-icons.php';
			$icons_file = dirname( __FILE__ ) . '/../../../font-awesome-icons.php';
			$icons_file = apply_filters( 'redux-font-icons-file', $icons_file );
            if (file_exists($icons_file)) {
                require_once( $icons_file );
            }
			$icons_file = apply_filters("redux/{$this->parent->args['opt_name']}/field/font/icons/file", $icons_file);
            $field2['options'] = $this->parent->get_wordpress_data($field2['data'], $field2['args']);
            $field2['class'] = " font-icons";

            if (!empty($field2['width'])) {
                $width = ' style="' . $field2['width'] . '"';
            } else {
                $width = ' style="width: 40%;"';
            }

            $nameBrackets = "[]";

            $placeholder = (isset($field2['placeholder'])) ? esc_attr($field2['placeholder']) : __('Select an item', 'redux-framework');

			if ( isset( $field2['select2'] ) ) { // if there are any let's pass them to js
                $select2_params = json_encode( $field2['select2'] );
                $select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

                $selection .= '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
            }

            $sortable = (isset($field2['sortable']) && $field2['sortable']) ? ' select2-sortable"' : "";

            $selection .= '<select ' . $multi . ' id="' . $field2['id'] . '-select" data-placeholder="' . $placeholder . '" name="' . $field2['name'] . $nameBrackets . '" class="redux-select-item ' . $field2['class'] . $sortable . '"' . $width . ' rows="6">';
            $selection .= '<option></option>';
			
            foreach ($field2['options'] as $k => $v) {
				/*
				if (is_array($v)) {
                    $selection .= '<optgroup label="' . $k . '">';

                    foreach($v as $opt => $val) {
                        $this->make_option($opt, $val);
                    }

                    $selection .= '</optgroup>';

                    continue;
                }

				$selection .= $this->make_option($k, $v);
            }
*/


				$selected = '';
				if (isset($this->value[$key]) && $this->value[$key] == $k) {
					if ($hidden == false) {
						$selected = ' selected="selected"';
					}
				}
                $selection .= '<option value="' . $k . '"' . $selected . '>' . $v . '</option>';
            }//foreach

            $selection .= '</select>';
			return $selection;
		}

		private function make_option($id, $value) {
            if ( is_array( $this->value ) ) {
                $selected = ( is_array( $this->value ) && in_array( $id, $this->value ) ) ? ' selected="selected"' : '';
            } else {
                $selected = selected( $this->value, $id, false );
            }

            return '<option value="' . $id . '"' . $selected . '>' . $value . '</option>';
        }


        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since       1.0.0
         * @access      public
         * @return      void
         */
        public function enqueue() {
			/* enqueue select js & select styles since the icon select is a select field */
			wp_enqueue_script(
                    'redux-multi-social-js',
                    ReduxFramework::$_url . 'sh-themes/extensions/multi_social/multi_social/field_multi_social' . '.js',
                    array( 'jquery', 'select2-js', 'redux-js' ),
                    time(),
                    true
                );

			wp_enqueue_style(
                    'redux-field-select-css',
                    ReduxFramework::$_url . 'inc/fields/select/field_select.css',
                    time(),
                    true
                );
        }
    }   
}
