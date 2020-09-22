<?php
class Kintone_MW_WP_Form_Admin{

    /**
     * Kintone_MW_WP_Form_Admin constructor.
     *
     * @return void
     */
    public function __construct(){
        add_action( 'admin_menu' , array( $this, '_add_custom_fields' ) );
        add_action( 'save_post', array( $this, '_save_post'));
        add_filter('wp_insert_post_data', array( $this, '_private_post_type'));
    }

    /**
     * カスタムフィールドセット
     *
     * @return void
     */
    function _add_custom_fields(){
        add_meta_box(
            'kintone-custom-fields-setting',
            'kintone ユーザー認証',
            array( $this, '_insert_custom_fields' ),
            'kintone-mw-wp-form',
            'advanced'
        );
    }

    /**
     * 新規作成時にデフォルトで非公開にする
     *
     * @param $post
     * @return mixed
     */
    function _private_post_type($post){
        if ($post['post_type'] === 'kintone-mw-wp-form')
            $post['post_status'] = 'private';
        return $post;
    }

    /**
     * kintoneユーザ認証 入力HTMLの出力
     *
     * @return void
     */
    function _insert_custom_fields(){
        global $post;
        $subdomain      = get_post_meta($post->ID, 'subdomain', true);
        $user_id        = get_post_meta($post->ID, 'user_id', true);
        $user_password  = get_post_meta($post->ID, 'user_password', true);
        $api_token      = get_post_meta($post->ID, 'api_token', true);
        $app_id         = get_post_meta($post->ID, 'app_id', true);
        $mw_wp_form_id  = get_post_meta($post->ID, 'mw_wp_form_id', true);
        ?>
        <div class="wrap">
            <p>
                kintoneの「フィールドコード」と、MW WP Formの各フォームタグの「name」が一致したものが保存されます。
            </p>
            <hr>
            <p>
                kintone APIを使うための情報を設定します。
            </p>
            <table class="form-table">

                <tr>
                    <th class="row">サブドメイン名</th>
                    <td>
                        <label><input type="text" name="subdomain" value="<?= $subdomain ?>" class="regular-text" placeholder="サブドメインの文字列"></label>
                        <br>https://サブドメイン名.cybozu.com
                    </td>
                </tr>

                <tr>
                    <th class="row">ログイン名</th>
                    <td>
                        <label><input type="text" name="user_id" value="<?= $user_id ?>" class="regular-text"></label>
                    </td>
                </tr>

                <tr>
                    <th class="row">パスワード</th>
                    <td>
                        <label><input type="password" name="user_password" value="<?= $user_password ?>" class="regular-text"></label>
                    </td>
                </tr>

                <tr>
                    <th class="row">APIトークン</th>
                    <td>
                        <label><input type="text" name="api_token" value="<?= $api_token ?>" class="regular-text"  placeholder="例: 4xXAhPtB4BbP2gcMMyd1NtlUCgdabjafja"></label>
                    </td>
                </tr>

                <tr>
                    <th class="row">APP ID</th>
                    <td>
                        <label><input type="text" name="app_id" value="<?= $app_id ?>" class="regular-text"  placeholder="例: 12"></label>
                    </td>
                </tr>

            </table>

            <hr>

            <h3>MW WP Form情報</h3>
            <table class="form-table">

                <tr>
                    <th class="row">フォーム識別子</th>
                    <td>
                        <label><input type="text" name="mw_wp_form_id" value="<?= $mw_wp_form_id ?>" class="regular-text" placeholder="例: 156"></label>
                    </td>
                </tr>

            </table>
        </div>
        <?php
    }

    /**
     * メタ情報の登録
     *
     * @param $post_id
     * @return void
     */
    function _save_post( $post_id ) {
        if(isset($_POST['subdomain'])){
            update_post_meta($post_id, 'subdomain', esc_html($_POST['subdomain']));
        }
        if(isset($_POST['user_id'])){
            update_post_meta($post_id, 'user_id', esc_html($_POST['user_id']));
        }
        if(isset($_POST['user_password'])){
            update_post_meta($post_id, 'user_password', esc_html($_POST['user_password']));
        }
        if(isset($_POST['api_token'])){
            update_post_meta($post_id, 'api_token', esc_html($_POST['api_token']));
        }
        if(isset($_POST['app_id'])){
            update_post_meta($post_id, 'app_id', esc_html($_POST['app_id']));
        }
        if(isset($_POST['mw_wp_form_id'])){
            update_post_meta($post_id, 'mw_wp_form_id', esc_html($_POST['mw_wp_form_id']));
        }

    }

}
new Kintone_MW_WP_Form_Admin();
