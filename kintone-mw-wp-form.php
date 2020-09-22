<?php
/*
Plugin Name: MW WP Form kintone連携
Author: Akito Tsukahara Lifebook
Version: 1.0.0
 */

define('KMWF_VERSION', '1.0.0');
define('KMWF_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('KMWF_PLUGIN_NAME', trim(dirname(KMWF_PLUGIN_BASENAME), '/'));

class Kintone_MW_WP_Form{

    /**
     * Kintone_MW_WP_Form constructor.
     */
    public function __construct(){
        add_action( 'plugins_loaded', array( $this, '_initialize' ), 11 );
        add_action( 'after_setup_theme', array( $this, '_load_admin_initialize_files' ), 9 );
        add_action( 'mwform_after_exec_shortcode', array( $this, '_load_initialize_files' ), 9 );
    }

    /**
     * Load adminclasses
     *
     * @return void
     */
    public function _load_admin_initialize_files() {
        include_once( plugin_dir_path( __FILE__ ) . 'classes/class.admin.php' );
    }

    /**
     * Load classes
     *
     * MW WP Formのショートコードが呼ばれた時にだけ処理を行う（無駄な呼び出しを控える為）
     * @return void
     */
    public function _load_initialize_files() {
        include_once( plugin_dir_path( __FILE__ ) . 'classes/class.api.php' );
        include_once( plugin_dir_path( __FILE__ ) . 'classes/class.filter.php' );
    }

    /**
     * ページセットアップ
     *
     * @return void
     */
    public function _initialize() {
        //国際化用ファイルの読み込み
        //load_plugin_textdomain( 'kintone-mw-wp-form' );

        add_action( 'init', array( $this, '_self_made_post_type' ) );
    }

    /**
     * カスタム投稿タイプ登録
     *
     * @return void
     */
    function _self_made_post_type() {
        register_post_type( 'kintone-mw-wp-form',
            array(
                'label'                 => 'kintone MW WP Form',
                'public'                => true,
                'exclude_from_search'   => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 30,
                'hierarchical'          => true,
                'has_archive'           => true,
                'with_front'            => true,
                'supports'              => array(
                    'title',
                    'custom-fields',
                    'post-formats',
                    'revisions',
                ),
            )
        );
    }

}
new Kintone_MW_WP_Form();