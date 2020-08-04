<?php

namespace Dbout\WpMuPlugins;

/**
 * Class RemoveGenetatorMetas
 * @package Dbout\WpMuPlugins
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class RemoveGenetatorMetas
{

    /**
     * @return void
     */
    public static function remove()
    {
        remove_action( 'wp_head', 'wp_generator' );
        add_filter( 'the_generator', '__return_empty_string' );
    }
}
