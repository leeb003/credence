<?php
/**
*
* Adapted from Edward McIntyre's wp_bootstrap_navwalker class. 
* Removed support for glyphicon and added support for Font Awesome
*
*/
/**
 * Class Name: wp_bootstrap_navwalker
 * GitHub URI: https://github.com/twittem/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 3 navigation style in a custom theme using the WordPress built in menu manager.
 * Version: 2.0.4
 * Author: Edward McIntyre - @twittem
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */
//exit if accessed directly
if(!defined('ABSPATH')) exit;

class wp_bootstrap_navwalker extends Walker_Nav_Menu {
	/**
     * Needed to add a variable to track the item we are at inside the start_lvl and end_lvl for column layouts
    **/
    private $curItem;	  // used to track current item outside start_el
	private $curLvl;	  // used to track multi-column in end_lvl
	private $colCount;    // used for column count selection 2,3, or 4...default 3 (4 columns)

	/**
	 * @see Walker::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */

    public function start_lvl( &$output, $depth = 0, $args = array() ) {
		
        // don't print any ul's for the main multi-column, keep row / col in order
        //if (strcasecmp($this->curItem->attr_title, 'multi-column' ) == 0 && $depth === 0) {
		if ( preg_match('/^(multi-column) (\S+)/', $this->curItem->attr_title, $matches)) {
			$this->colCount = 3; // default
			if ($matches[2] == '2-columns') {
				$this->colCount = 6;
			} elseif ($matches[2] == '3-columns') {
				$this->colCount = 4;
			}

			$this->curLvl = "multi";
            $output .= "";

		// don't print any for new-column either
		} elseif (strcasecmp($this->curItem->attr_title, 'new-column' ) == 0  && $depth === 1) {
			$this->curLvl = "newCol";
			$output .= "";
		

        } else {
			$this->curLvl = "normal";
            $indent = str_repeat( "\t", $depth );
            $output .= "\n<ul role=\"menu\" class=\" dropdown-menu pull-left\">\n";
        }
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
		
        // don't print anything out for new multi-column ul 
		// have to match on curLvl private variable set in start_lvl on match
		if ($this->curLvl == "multi") {
            $output .= "";
		
		} elseif ($this->curLvl == "newCol") {
			$output .= "";

        } else {
            $indent = str_repeat("\t", $depth);
            $output .= "$indent</ul>\n";
        }
    }




	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$this->curItem = $item;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		/**
		 * Dividers, Headers or Disabled
		 * =============================
		 * Determine whether the item is a Divider, Header, Disabled or regular
		 * menu item. To prevent errors we use the strcasecmp() function to so a
		 * comparison that is not case sensitive. The strcasecmp() function returns
		 * a 0 if the strings are equal.
		 */
		if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && ($depth === 1 || $depth === 2) ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="divider">';
		} else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
			$output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
		} else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
			$output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
		} else {

			$class_names = $value = '';

			$classes = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );

			/*
			if ( $args->has_children )
				$class_names .= ' dropdown';
			*/

			if($args->has_children && $depth === 0) { $class_names .= ' dropdown'; } elseif($args->has_children && $depth > 0) { $class_names .= ' dropdown-submenu'; }

			if ( in_array( 'current-menu-item', $classes ) )
				$class_names .= ' active';

			//if  (strcasecmp($item->attr_title, 'multi-column' ) == 0 ) {  // top level multi-column
			if ( preg_match('/^(multi-column) (\S+?)/', $item->attr_title, $matches)) {
				$class_names .= ' yamm-fw';
			}

			// remove Font Awesome icon from classes array and save the icon
			// we will add the icon back in via a <span> below so it aligns with
			// the menu item
			// Modified to support the item attribute inclusion of font awesome icons LB
			if ( preg_match('/^(fa-\S+)/', $item->attr_title, $matches)) {
			//if ( in_array('fa', $classes)) {
				//$key = array_search('fa', $classes);
				//$icon = $classes[$key + 1];
				//$class_names = str_replace($classes[$key+1], '', $class_names);
				//$class_names = str_replace($classes[$key], '', $class_names);
				$icon = esc_attr( $item->attr_title);
				$icon = 'fa ' . $matches[0];

			}
			
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			// new-column formatting
			if (strcasecmp($this->curItem->attr_title, 'new-column' ) == 0 && $depth === 1) {
				$output .= '<div class="new-column col-md-' . $this->colCount . ' ">' . "\n" 
					. '<ul class="dropdown-menu">' . "\n" . '<li' . $id . $value . $class_names .'>';

			} else {
				$output .= '<li' . $id . $value . $class_names .'>';
			}

			$atts = array();
			$atts['title']  = ! empty( $item->title )	? $item->title	: '';
			$atts['target'] = ! empty( $item->target )	? $item->target	: '';
			$atts['rel']    = ! empty( $item->xfn )		? $item->xfn	: '';

			// If item has_children add atts to a.
			// if ( $args->has_children && $depth === 0 ) {
			if ( $args->has_children ) {
				//$atts['href']   		= '#';
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
				$atts['data-toggle']	= 'dropdown';
				$atts['class']			= 'dropdown-toggle';
			} else {
				$atts['href'] = ! empty( $item->url ) ? $item->url : '';
			}

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = $args->before;

			// new-column
			if (strcasecmp($item->attr_title, 'new-column' ) == 0 ) {  // new-column
				$item_output .= '<span class="new-column-title">' . $item->title . '</span>';

			} else {

				// Font Awesome icons
				if ( ! empty( $icon ) ) {
					$item_output .= '<a'. $attributes .'><span class="' . esc_attr( $icon ) . '"></span>&nbsp;&nbsp;';
				} else {
					$item_output .= '<a'. $attributes .'>';	
				}
		
				if ( $args->has_children && 0 < $depth) {  // sub menu item
					$item_output .= $args->link_before . '<span class="nav-link-href">' 
								. apply_filters( 'the_title', $item->title, $item->ID )
								. '</span> <span class="nav-link-down fa fa-angle-down"> </span>' . $args->link_after
								. $args->after;
				} elseif ($args->has_children && 0 === $depth ) {  // top level menu item
					$item_output .= $args->link_before . '<span class="nav-link-href">' 
								. apply_filters( 'the_title', $item->title, $item->ID ) 
								. '</span> <span class="nav-link-down fa fa-angle-down"> </span></a>' . $args->link_after
								. $args->after;
				} else {

					$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) 
								. $args->link_after . '</a>';
					$item_output .= $args->after;
				}
			}

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

			// multi-column
            //if  (strcasecmp($item->attr_title, 'multi-column' ) == 0 ) {  // top level multi-column
			if ( preg_match('/^(multi-column) (\S+?)/', $item->attr_title, $matches)) {
                $output .= '<div class="dropdown-menu multi-column"><div class="row">' . "\n";
            } elseif (strcasecmp($item->attr_title, 'new-column' ) == 0 ) {  // new-column
				$output .= '</li>';
			}

		}
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth.
	 *
	 * This method shouldn't be called directly, use the walk() method instead.
	 *
	 * @see Walker::start_el()
	 * @since 2.5.0
	 *
	 * @param object $element Data object
	 * @param array $children_elements List of elements to continue traversing.
	 * @param int $max_depth Max depth to traverse.
	 * @param int $depth Depth of current element.
	 * @param array $args
	 * @param string $output Passed by reference. Used to append additional content.
	 * @return null Null on failure with no changes to parameters.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element )
            return;

        $id_field = $this->db_fields['id'];

        // Display this element.
        if ( is_object( $args[0] ) )
           $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }


	
    /** 
     * End Element modified to look for the custom menu and add div tags
     **/
    function end_el( &$output, $item, $depth = 0, $args = array() ) {

		if ( preg_match('/^(multi-column)/', $item->attr_title, $matches)) {
            $output .= '</div></div></li>' . "\n";

		// new column formatting
        } elseif (strcasecmp($item->attr_title, 'new-column' ) == 0 ) {
			$output .= '</ul>' . "\n" . '</div>' . "\n" . '' . "\n";

		// Allow Descriptions
		} elseif (preg_match('/\S+/', $item->post_content) && $depth == 2) {
			$output .= '<p>' . $item->post_content . '</p></li>' . "\n"; 

        } else { // default nav_walker end li
            $output .= "</li>\n";
        }
    }

	/**
	 * Menu Fallback
	 * =============
	 * If this function is assigned to the wp_nav_menu's fallback_cb variable
	 * and a manu has not been assigned to the theme location in the WordPress
	 * menu manager the function with display nothing to a non-logged in user,
	 * and will add a link to the WordPress menu manager if logged in as an admin.
	 *
	 * @param array $args passed from the wp_nav_menu function.
	 *
	 */
	public static function fallback( $args ) {
		if ( current_user_can( 'manage_options' ) ) {

			extract( $args );

			$fb_output = null;

			if ( $container ) {
				$fb_output = '<' . $container;

				if ( $container_id )
					$fb_output .= ' id="' . $container_id . '"';

				if ( $container_class )
					$fb_output .= ' class="' . $container_class . '"';

				$fb_output .= '>';
			}

			$fb_output .= '<ul';

			if ( $menu_id )
				$fb_output .= ' id="' . $menu_id . '"';

			if ( $menu_class )
				$fb_output .= ' class="' . $menu_class . '"';

			$fb_output .= '>';
			$fb_output .= '<li><a href="' . admin_url( 'nav-menus.php' ) . '">Add a menu</a></li>';
			$fb_output .= '</ul>';

			if ( $container )
				$fb_output .= '</' . $container . '>';

			echo $fb_output;
		}
	}

}
