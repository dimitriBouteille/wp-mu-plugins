<?php

namespace Dbout\WpMuPlugins;

/**
 * Class RemoveH1WpEditor
 * @package Dbout\WpMuPlugins
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class RemoveH1WpEditor
{

    /**
     * @return void
     */
    public static function remove()
    {
        add_filter( 'tiny_mce_before_init', function($init) {
            $init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Pre=pre';
            return $init;
        });

        add_action( 'admin_head', function() {
            echo '<style>
	#editor .components-button.components-toolbar__control[data-subscript="1"] {
		width: 3px;
		padding: 3px 0;
		pointer-events: none;
		visibility: hidden;
	}
	</style>';
        });
    }
}
