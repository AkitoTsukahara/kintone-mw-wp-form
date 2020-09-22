<?php
class Kintone_MW_WP_Form_Filter{

    /**
     * Kintone_MW_WP_Form_Filter constructor.
     *
     * @return void
     */
    public function __construct(){

        //保存されているデータを取得
        $args = array(
            'post_type'     => 'kintone-mw-wp-form',
            'post_status'   => 'private'
        );
        $the_posts = get_posts($args);

        if ($the_posts) {
            foreach ($the_posts as $post) {
                //mwform key取得
                $mw_wp_form_id = get_post_meta($post->ID, 'mw_wp_form_id', true);

                if(!has_filter('mwform_admin_mail_mw-wp-form-' . $mw_wp_form_id,array($this, 'mwform_auto_mail'))){
                    //filters
                    add_filter('mwform_admin_mail_mw-wp-form-' . $mw_wp_form_id, array($this, 'mwform_auto_mail'), 10, 3);
                }
            }
        }
    }

    /**
     * MW FormのフィルターでKintoneにデータを投げる
     *
     * @param $Mail
     * @param $values
     * @param $Data
     * @return mixed
     */
    public function mwform_auto_mail($Mail, $values, $Data){
        $mwwp_id = $values['mw-wp-form-form-id'];
        //MW WP Form IDからpost_idを取得する
        $post_id = $this->get_postid_from_mwwp_id($mwwp_id);

        //キントーン API連携
        $kintone_form_api = new Kintone_MW_WP_Form_API($post_id);
        $kintone_form_api->post($values);

        return $Mail;
    }

    /**
     * MW WP Form IDからpost_idを取得する
     *
     * @param $mwwp_id
     * @return mixed
     */
    public function get_postid_from_mwwp_id($mwwp_id){
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare("
            SELECT post_id
            FROM $wpdb->postmeta AS meta
            WHERE (meta.meta_key = 'mw_wp_form_id' AND meta.meta_value = %s)
        ",$mwwp_id));
        if(empty($result)){
            return false;
        }else{
            return $result[0]->post_id;
        }
    }

}
new Kintone_MW_WP_Form_Filter();
