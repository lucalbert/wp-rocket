<?php

namespace WP_Rocket\Engine\CriticalPath;

use DOMElement;
use WP_Rocket\Admin\Options_Data;
use WP_Rocket\Engine\DOM\HTMLDocument;

class DOM {

	/**
	 * Instance of Critical CSS.
	 *
	 * @var Critical_CSS
	 */
	protected $critical_css;

	/**
	 * Instance of options.
	 *
	 * @var Options_Data
	 */
	protected $options;

	/**
	 * Instance of the DOM.
	 *
	 * @var HTMLDocument
	 */
	protected $dom;

	/**
	 * Creates an instance of the DOM Handler.
	 *
	 * @param CriticalCSS  $critical_css Critical CSS instance.
	 * @param Options_Data $options      WP Rocket options.
	 */
	public function __construct( CriticalCSS $critical_css, Options_Data $options ) {
		$this->critical_css = $critical_css;
		$this->options      = $options;
	}

	/**
	 * Named constructor for transforming HTML into DOM.
	 *
	 * @since 3.6.2
	 *
	 * @param CriticalCSS  $critical_css Critical CSS instance.
	 * @param Options_Data $options      WP Rocket options.
	 * @param string       $html         Optional. HTML to transform into HTML DOMDocument object.
	 *
	 * @return self Instance of this class.
	 */
	public static function from_html( CriticalCSS $critical_css, Options_Data $options, $html ) {
		$instance = new static( $critical_css, $options );

		if ( $instance->okay_to_create_dom() ) {
			$instance->dom = HTMLDocument::from_html( $html );
		}

		return $instance;
	}

	/**
	 * Checks if it's okay to create the DOM from the HTML. This method can be overloaded.
	 *
	 * @since 3.6.2
	 *
	 * @return bool
	 */
	protected function okay_to_create_dom() {
		return true;
	}

	/**
	 * Checks if the string contains the given needle.
	 *
	 * @since 3.6.2
	 *
	 * @param string $search_string Search string.
	 * @param string $needle        Needle to find.
	 *
	 * @return bool
	 */
	protected function string_contains( $search_string, $needle ) {
		return ( false !== strpos( $search_string, $needle ) );
	}

	/**
	 * Converts an array into a string.
	 *
	 * @since 3.6.2
	 *
	 * @param array  $array    The array to convert.
	 * @param string $glue     Glue between the string parts.
	 * @param string $operator Operator between the key and value when flatten the array.
	 *
	 * @return string converted string.
	 */
	protected function array_to_string( array $array, $glue, $operator ) {
		return implode( $glue, $this->flatten_array( $array, $operator ) );
	}

	/**
	 * Flattens an array from key => value to string elements.
	 *
	 * For index key, the value is stored as the element.
	 * For keys, the key is combined with the value with the operator as the separator.
	 *
	 * @since 3.6.2
	 *
	 * @param array  $array    The array to flatten.
	 * @param string $operator The separator between the key and value.
	 *
	 * @return array
	 */
	protected function flatten_array( array $array, $operator ) {
		$flat = [];

		foreach ( $array as $key => $value ) {
			if ( is_integer( $key ) ) {
				$flat[] = $value;
			} else {
				$flat[] = "{$key}{$operator}{$value}";
			}
		}

		return $flat;
	}

	/**
	 * Sets the <noscript>.
	 *
	 * @since 3.6.2
	 *
	 * @param DOMElement $element The element to append within <noscript>.
	 */
	protected function set_noscript( $element ) {
		$noscript = $this->dom->createElement( 'noscript' );
		$noscript->appendChild( $element );
		$this->dom->get_body()->appendChild( $noscript );
	}
}