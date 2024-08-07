<?php

namespace DtCr\Core;

use DtCr\Modules\StyleEngine\Module as StyleEngineModule;
use WP_HTML_Tag_Processor;

class BlockUtils {

	const BLOCK_ID        = 'dt_cr_id_class';
	const BLOCK_ID_PREFIX = 'dt-cr-';

	static function get_class_id_from_atts( $attributes ) {

		return $attributes[ self::BLOCK_ID ];
	}

	static function append_classes( $block_content, $content_classes ) {
		$tag = self::get_tag_to_modify( $block_content );
		if ( empty( $content_classes ) || ! $tag ) {
			return $block_content;
		}

		foreach ( $content_classes as $class_name ) {
			$tag->add_class( $class_name );
		}

		return $tag->get_updated_html();
	}

	static function remove_classes( $block_content, $content_classes ) {
		$tag = self::get_tag_to_modify( $block_content );
		if ( empty( $content_classes ) || ! $tag ) {
			return $block_content;
		}

		foreach ( $content_classes as $class_name ) {
			$tag->remove_class( $class_name );
		}

		return $tag->get_updated_html();
	}

	static function get_tag_to_modify( $block_content ) {
		$p = new WP_HTML_Tag_Processor( $block_content );
		while ( $p->next_tag() ) {
			$tag_name = $p->get_tag();
			if ( $tag_name !== 'STYLE' && $tag_name !== 'SCRIPT' ) {
				return $p;
			}
		}

		return null;
	}

	static function append_styles( $block_content, $css_styles ) {
		if ( empty( $css_styles ) ) {
			return $block_content;
		}

		return "<style>{$css_styles}</style>{$block_content}";
	}

	// $ret = BlockUtils::add_styles_from_css_rules( [
	// [
	// 'selector'     => '@media screen and (max-width: 100px)',
	// 'declarations' => [
	// [
	// 'selector'     => '.block',
	// 'declarations' => [
	// 'color'   => 'red',
	// 'display' => 'flex',
	// ],
	// ],
	// [
	// 'selector'     => '.block2',
	// 'declarations' => [
	// 'color'   => 'green',
	// 'display' => 'none',
	// ],
	// ],
	// ],
	// ],
	// [
	// 'selector'     => '.block3',
	// 'declarations' => [
	// 'color'   => 'red',
	// 'display' => 'block',
	// ],
	// ],
	// ] );

	static function add_styles_from_css_rules( $css_rules ) {
		if ( ! empty( $css_rules ) ) {
			/*
			 * Add to the style engine store to enqueue and render layout styles.
			 * Return compiled layout styles to retain backwards compatibility.
			 * Since https://github.com/WordPress/gutenberg/pull/42452,
			 * wp_enqueue_block_support_styles is no longer called in this block supports file.
			 */
			return StyleEngineModule::get_stylesheet_from_css_rules(
				$css_rules,
				array(
					'context'  => 'core',
					'prettify' => false,
				)
			);
		}

		return '';
	}
}
