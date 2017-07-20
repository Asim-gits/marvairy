<?php

interface IWPML_Beaver_Builder_Module {

	/**
	 * @param string|int $node_id
	 * @param array $settings
	 * @param WPML_PB_String[] $strings
	 *
	 * @return WPML_PB_String[]
	 */
	public function get( $node_id, $settings, $strings );

	/**
	 * @param string|int $node_id
	 * @param array $settings
	 * @param WPML_PB_String $string
	 *
	 * @return array
	 */
	public function update( $node_id, $settings, WPML_PB_String $string );
}

