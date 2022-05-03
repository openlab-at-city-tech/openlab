<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc;

if ( !defined( 'ABSPATH' ) ) {
    die;
}

use \DateTime;
use Inc\Core\Tasks;
use Inc\Core\Members;
use Inc\Core\Projects;
use Inc\Core\Utillities;
use Inc\Core\Categories;
use Inc\Base\BaseController;

class ZephyrProjectManager
{
    /**
     * The unique instance of the plugin.
     * @var ZephyrProjectManager
     */
    private static $current_user_id;
    private static $instance;
    private static $projects;
    private static $tasks;
    private static $users;
    private static $formatted_users;
    private static $user_tasks;
    private static $categories;
 
    /**
     * Gets an instance of our plugin.
     * @return ZephyrProjectManager
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
 
    private function __construct() {
        self::$current_user_id = get_current_user_id();

        //self::$projects = Projects::get_projects();
        self::$users = [];
        self::$categories = [];
        self::$formatted_users = [];
        self::$tasks = [];
        self::$projects = [];
        self::$categories = $this->get_categories();
        self::$tasks = [];
        self::$user_tasks = [];
        self::$user_tasks = $this->filter_user_tasks( self::$current_user_id );
    }

    public static function get_projects( $args = null ) {

        if ( empty( self::$projects ) ) {

            $projects = Projects::get_projects();

            foreach ( $projects as $project ) {
                self::$projects[$project->id] = $project;
            }
        }

        self::$projects = apply_filters( 'zpm_filter_global_projects', self::$projects );
        
        $projects = [];

        if ( !is_null( $args ) ) {
            if ( isset( $args['category'] ) && $args['category'] !== '-1' && $args['category'] !== 'all' ) {
                $projects = Projects::filter_by_category( self::$projects, $args['category'] );
            } else {
                $projects = self::$projects;
            }

            if (isset($args['user_id'])) {
                $projects = Projects::get_user_projects($args['user_id']);
            }
        } else {
            $projects = self::$projects;
        }

        return $projects;
    }

    public static function get_project( $id ) {
        if ( isset( self::$projects[$id] ) ) {
            return self::$projects[$id];
        } else {
            if ( $id !== '-1' ) {
                return Projects::get_project( $id );
            } else {
                return [];
            }
        }
    }

    public static function add_project( $project ) {
        self::$projects[$project->id] = $project;
    }

    public static function get_tasks( $args = null ) {

        if ( empty( self::$tasks ) ) {
            $tasks = Tasks::get_tasks();

            foreach ( $tasks as $task ) {
                self::$tasks[$task->id] = $task;
            }
        }
        self::$tasks = Tasks::sortByStartDate(self::$tasks);
        self::$tasks = apply_filters( 'zpm_filter_global_tasks', self::$tasks );

        if ( !is_null( $args ) ) {
            return self::filter_tasks( $args );
        }
        return self::$tasks;
    }

    public static function get_task( $id ) {
        if ( isset( self::$tasks[$id] ) ) {
            return self::$tasks[$id];
        } else {
            if ( $id !== '-1' ) {
                return Tasks::get_task( $id );
            } else {
                return [];
            }
        }
    }

    public static function add_task( $task ) {
        self::$tasks[$task->id] = $task;
    }

    public static function get_users( $formatted = true, $args = null ) {
        $results = [];
        $users = [];

        if ( sizeof( self::$users ) <= 0 ) {
            $users = Utillities::get_users( false );
            foreach ($users as $user) {
                self::$users[$user->ID] = $user;
            }
        }
        if ( sizeof( self::$formatted_users ) <= 0 ) {
            $users = Utillities::get_users();
            foreach ($users as $user) {
                self::$formatted_users[$user['id']] = $user;
            }
        }

        // Args
        if ( !is_null( $args ) ) {

            if ( isset( $args['can_zephyr'] ) ) {
                foreach ( self::$formatted_users as $user ) {
                    if (( isset( $user['can_zephyr'] ) && $user['can_zephyr'] == "true" ) || !isset( $user['can_zephyr'] )) {
                        $results[] = $user;
                    }
                }
                return $results;
            }
        }

        if ( $formatted ) {
            return self::$formatted_users;
        } else {
            return self::$users;
        }
    }

    public static function get_user_tasks() {
        
        return self::$user_tasks;
    }

    public static function get_user( $id, $formatted = true ) {
        if ( $formatted ) {
            if ( isset( self::$formatted_users[$id] ) ) {
                return self::$formatted_users[$id];
            } else {
                return Members::get_member( $id );
            }
        } else {
            if ( isset( self::$users[$id] ) ) {
                return self::$users[$id];
            } else {
                return Members::get_member( $id );
            }
        }
        return [];
    }

    public static function get_categories() {
        $categories = [];

        if ( sizeof( self::$categories ) <= 0 ) {
            $categories = Categories::fetch();
            foreach ( $categories as $category ) {
                self::$categories[$category->id] = $category;
            }
        }

        return self::$categories;
    }


    public static function get_category( $id ) {
        self::get_categories();
        if ( sizeof( self::$categories ) <= 0 ) {
        }
        if ( isset( self::$categories[$id] ) ) {
            return self::$categories[$id];
        } else {
            if ( $id !== '-1' ) {
                return null;
            } else {
                return null;
            }
        }
    }

    public static function filter_user_tasks( $user_id ) {
        //$manager = ZephyrProjectManager();
        $tasks = array();

        // Add tasks if user is assigned or in team
        if ( is_array( $tasks ) ) {
            foreach ( self::$tasks as $task ) {
                if ( is_object( $task ) ) {
                    $team = property_exists( $task, 'team' ) ? $task->team : '-1';

                    if ( Tasks::is_assignee( $task, $user_id ) ) {
                        $tasks[] = $task;
                    }
                }
            }
        }
        return $tasks;
    }

    public static function filter_tasks( $args ) {
        $tasks = [];

        foreach ( self::$tasks as $task ) {
            if ( isset( $args['completed'] ) ) {
                if ( $task->completed == $args['completed'] ) {
                    if ( isset( $args['project'] ) ) {
                        if ( $task->project == $args['project'] ) {
                            $tasks[] = $task;
                        }
                    } else {
                        $tasks[] = $task;
                    }
                }
            }
        }
        
        return $tasks;
    }

    public function zpm_is_single_project() {
        if (isset($_GET['project']) || isset($_POST['project_id']) || isset($_REQUEST['project_id'])) {
            return true;
        } else {
            return false;
        }
    }
}