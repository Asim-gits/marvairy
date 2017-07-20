<?php

class WPML_Beaver_Builder_Integration {

	const NAME = 'Beaver builder';

	/** @var WPML_Beaver_Builder_Register_Strings $register_strings*/
	private $register_strings;
	/** @var WPML_Beaver_Builder_Update_Translation $update_translation */
	private $update_translation;

	public function __construct(
		WPML_Beaver_Builder_Register_Strings $register_strings,
		WPML_Beaver_Builder_Update_Translation $update_translation
	) {
		$this->register_strings = $register_strings;
		$this->update_translation = $update_translation;
	}

	public function add_hooks() {
		add_filter( 'wpml_page_builder_support_required', array( $this, 'support_required' ) );
		add_action( 'wpml_page_builder_register_strings', array( $this, 'register_pb_strings' ), 10, 2 );
		add_action( 'wpml_page_builder_string_translated', array( $this, 'update_translated_post' ), 10, 5 );
	}

	public function support_required( array $page_builder_plugins ) {
		$page_builder_plugins[] = self::NAME;
		return $page_builder_plugins;
	}

	public function register_pb_strings( $post, $package_key ) {
		if ( self::NAME === $package_key['kind'] ) {
			$this->register_strings->register_strings( $post, $package_key );
		}
	}

	public function update_translated_post( $kind, $translated_post_id, $original_post, $string_translations, $lang ) {
		if ( self::NAME === $kind ) {
			$this->update_translation->update( $translated_post_id, $original_post, $string_translations, $lang );
		}
	}
}
