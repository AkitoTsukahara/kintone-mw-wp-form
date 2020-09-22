<?php
class Kintone_MW_WP_Form_API{
    private $user_id            = '';
    private $user_password      = '';
    private $subdomain          = '';
    private $host_name          = '';
    private $api_record_url     = '';
    private $api_records_url    = '';
    private $api_token          = '';
    private $app_id             = '';
    private $mw_wp_form_id      = '';

    /**
     * Kintone_MW_WP_Form_API constructor.
     *
     * @param $post_id
     * @return void
     */
    public function __construct($post_id){

        //カスタムフィールド取得
        $this->subdomain        = get_post_meta($post_id, 'subdomain', true);
        $this->user_id          = get_post_meta($post_id, 'user_id', true);
        $this->user_password    = get_post_meta($post_id, 'user_password', true);
        $this->api_token        = get_post_meta($post_id, 'api_token', true);
        $this->app_id           = get_post_meta($post_id, 'app_id', true);
        $this->mw_wp_form_id    = get_post_meta($post_id, 'mw_wp_form_id', true);

        //API URLs
        $this->host_name = $this->subdomain . '.cybozu.com';
        $this->api_record_url = 'https://' . $this->host_name . '/k/v1/record.json';
        $this->api_records_url = 'https://' . $this->host_name . '/k/v1/records.json';

    }

    /**
     * 投稿用のheader情報を取得.
     *
     * @return array
     */
    private function get_headers(){
        $headers = array(
            'X-Cybozu-Authorization:' . base64_encode($this->user_id . ':' . $this->user_password),
            'Authorization: Basic ' . base64_encode($this->user_id . ':' . $this->user_password),
            'X-Cybozu-API-Token: ' . $this->api_token,
            'Host: ' . $this->host_name . ':443',
            'Content-Type: application/json',
        );

        return $headers;
    }

    /**
     * kintoneからデータを取得.
     *
     * @param $url
     * @param $fields
     * @param string $request
     * @return bool|string
     */
    private function request_api($url, $fields, $request = 'post'){
        //headerを取得
        $headers = $this->get_headers();

        $curl = curl_init();
        if ($request === 'post') { //追加時
            curl_setopt($curl, CURLOPT_POST, true);
        } elseif ($request === 'put') { //更新時
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        } elseif ($request === 'get') { //取得時
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($fields));
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    /**
     * MW WP Formからの送信データをkintone連携用に整形・リクエスト
     *
     * @param $values
     * @return bool|string
     */
    public function post($values){

        // kintoneに投げるデータを作成
        $record = array();
        foreach ($values as $key => $value) {
            //チェックボックス対策で値が配列かどうかチェックする
            if (!is_array($value)) {
                //テキスト等はそのままでOK
                $record[$key] = array('value' => $value);
            } else {
                foreach ($value as $key2 => $items) {
                    if ($key2 == 'data') {
                        $checkboxes = array();
                        foreach ($items as $item) {
                            $checkboxes[] = $item;
                        }
                        $record[$key] = array('value' => $checkboxes);
                    }
                }
            }
        }

        $fields = array(
            'app'       => $this->app_id,
            'record'    => $record,
        );

        $response = $this->request_api($this->api_record_url, $fields);

        return $response;
    }

}
