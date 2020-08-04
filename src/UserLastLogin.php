<?php

namespace Dbout\WpMuPlugins;

/**
 * Class UserLastLogin
 * @package Dbout\WpMuPlugins
 *
 * @author      Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 * @link        https://github.com/dimitriBouteille Github
 * @copyright   (c) 2020 Dimitri BOUTEILLE
 */
class UserLastLogin
{

    /**
     * Date format in column
     *
     * @var string
     */
    private $dateFormat = 'l j F Y à G:i';

    /**
     * Column name
     *
     * @var string
     */
    private $columnName = 'Dernière connexion';

    /**
     * Column id
     *
     * @var string
     */
    private $columnId = 'lastLogin';

    /**
     * Name of the meta that saves the last connection date
     *
     * @var string
     */
    private $metaName = 'dbout_last_login';

    /**
     * If the column can be sorted
     *
     * @var bool
     */
    private $isSortable = true;

    /**
     * @param string $dateFormat
     * @return $this
     */
    public function setDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * @param string $columnName
     * @return $this
     */
    public function setColumnName(string $columnName): self
    {
        $this->columnName = $columnName;
        return $this;
    }

    /**
     * @param bool $isSortable
     * @return $this
     */
    public function isSortable(bool $isSortable): self
    {
        $this->isSortable = $isSortable;
        return $this;
    }

    /**
     * Saves user login date
     *
     * @param $userLogin
     * @param $user
     */
    public function saveLastLogin($userLogin, $user)
    {
        update_user_meta( $user->ID, $this->metaName, current_time('timestamp'));
    }

    /**
     * Adds the new column to the table
     *
     * @param $columns
     * @return mixed
     */
    public function addColumn($columns)
    {
        $posts = null;
        if(isset($columns['posts'])) {
            $posts = $columns['posts'];
            unset($columns['posts']);
        }

        $columns[$this->columnId] = $this->columnName;

        // Move the column to the end of the table
        if($posts) {
            $columns['posts'] = $posts;
        }

        return $columns;
    }

    /**
     * Adds value in column
     *
     * @param $value
     * @param $columnName
     * @param $userId
     * @return string
     */
    public function addColumnValue($value, $columnName, $userId)
    {
        if($this->columnId == $columnName) {
            $value = 'N.A';

            $lastLogin = (int)get_user_meta($userId, $this->metaName, true);
            if($lastLogin) {
                $value = date_i18n($this->dateFormat, $lastLogin);
            }
        }

        return $value;
    }

    /**
     * @param $columns
     * @return mixed
     */
    public function addSortable($columns)
    {
        $columns[$this->columnId] = $this->columnId;
        return $columns;
    }

    /**
     * Sort the column
     *
     * @param \WP_User_Query $query
     * @return \WP_User_Query
     */
    public function sort($query)
    {
        $orderBy = $query->get('orderby');

        if($orderBy == $this->columnId) {

            $meta_query = [
                'relation' => 'OR',
                [ 'key' => $this->metaName, 'compare' => 'NOT EXISTS', ],
                [ 'key' => $this->metaName, ],
            ];

            $query->set( 'meta_query', $meta_query );
            $query->set( 'orderby', 'meta_value' );
        }

        return $query;
    }

    /**
     * Saves the new column and adds the backup option of the last connection
     */
    public function register()
    {
        add_action('wp_login', [$this, 'saveLastLogin'], 10, 2);
        add_filter('manage_users_columns', [$this, 'addColumn']);
        add_filter('manage_users_custom_column', [$this, 'addColumnValue'], 10, 3);

        if($this->isSortable) {
            add_filter('manage_users_sortable_columns', [$this, 'addSortable']);
            add_action('pre_get_users', [$this, 'sort'], 1);
        }
    }
}
