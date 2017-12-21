<?php

        $OAUTH_CONSUMER_KEY = "UhuBsj54DbRnnHGQYaPywHmAi";      // APIキー
        $OAUTH_SECRET = 'e1zSaGoQorVKzyW5W7U8LaAmkUsf3nnjdIHCxh45Y03bnnBj86';   // APIシークレットキー
        $OAUTH_TOKEN = "42566916-5CI2BDtmiUQdWFKtTY6ajgxmLlYjURI4ftW2K88h3";    // アクセストークン
        $OAUTH_TOKEN_SECRET = 'YHfdFB1RJxIcHQ6XOriX0GZhmZmFpNmI5wegaBIRDflIU';  // アクセストークンシークレット

        // oauth認証で使用するパラメータ
        $OAUTH_VERSION = "1.0";
        $OAUTH_SIGNATURE_METHOD = "HMAC-SHA1";

        // Twitter検索をするAPIとMETHODの指定
        $TWITTER_API_URL = 'https://api.twitter.com/1.1/search/tweets.json';    // 検索API
        $REQUEST_COUNT = 10;    // 取得するツイート数
        $REQUEST_METHOD = 'GET' ;

        //検索するキーワードの設定
        $SEARCH_KEYWORD = 'realDonaldTrump';


        /***** OAuth1.0認証の署名生成 *****/
        // キー部分の作成
        $oauth_signature_key = rawurlencode($OAUTH_SECRET) . '&' . rawurlencode($OAUTH_TOKEN_SECRET) ;

        // パラメータの生成・編集
        $oauth_nonce = microtime();
        $oauth_timestamp = time();
        $oauth_signature_param = 'count=' . $REQUEST_COUNT .
        '&oauth_consumer_key=' . $OAUTH_CONSUMER_KEY .
        '&oauth_nonce='.rawurlencode($oauth_nonce) .
        '&oauth_signature_method='. $OAUTH_SIGNATURE_METHOD .
        '&oauth_timestamp=' . $oauth_timestamp .
        '&oauth_token=' . $OAUTH_TOKEN .
        '&oauth_version=' . $OAUTH_VERSION .
        '&q=from:' . rawurlencode($SEARCH_KEYWORD) .
        '&tweet_mode=extended';

        // データ部分の作成
        $oauth_signature_date = rawurlencode($REQUEST_METHOD) . '&' . rawurlencode($TWITTER_API_URL) . '&' . rawurlencode($oauth_signature_param);

        // 上記のデータとキーを使ってHMAC-SHA1方式のハッシュ値に変換
        $oauth_signature_hash = hash_hmac( 'sha1' , $oauth_signature_date , $oauth_signature_key , TRUE ) ;

        // base64エンコードしてOAuth1.0認証の署名作成
        $oauth_signature = base64_encode( $oauth_signature_hash );


        /***** Authorizationヘッダーの作成 *****/
        $req_oauth_header = array("Authorization: OAuth " . 'count=' . rawurlencode($REQUEST_COUNT) .
        ',oauth_consumer_key=' . rawurlencode($OAUTH_CONSUMER_KEY) .
        ',oauth_nonce='.str_replace(" ","+",$oauth_nonce) .
        ',oauth_signature_method='. rawurlencode($OAUTH_SIGNATURE_METHOD) .
        ',oauth_timestamp=' . rawurlencode($oauth_timestamp) .
        ',oauth_token=' . rawurlencode($OAUTH_TOKEN) .
        ',oauth_version=' . rawurlencode($OAUTH_VERSION) .
        ',q=from:' . rawurlencode($SEARCH_KEYWORD) .
        ',oauth_signature='.rawurlencode($oauth_signature));


        /***** リクエストURLの作成 *****/
        $TWITTER_API_URL .= '?tweet_mode=extended&q=from:' . rawurlencode($SEARCH_KEYWORD) . '&count=' . rawurlencode($REQUEST_COUNT);


        /***** cURLによるリクエスト実行 *****/
        // セッション初期化
        $curl = curl_init() ;

        // オプション設定
        curl_setopt( $curl , CURLOPT_URL , $TWITTER_API_URL ) ; // リクエストURL
        curl_setopt( $curl , CURLOPT_HEADER, false ) ; // ヘッダ情報の受信なし
        curl_setopt( $curl , CURLOPT_CUSTOMREQUEST , $REQUEST_METHOD ) ;        // リクエストメソッド設定
        curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false ) ; // 証明書検証なし
        curl_setopt( $curl , CURLOPT_RETURNTRANSFER , true ) ;  // curl_execの結果を文字列で返す
        curl_setopt( $curl , CURLOPT_HTTPHEADER , $req_oauth_header ) ; // リクエストヘッダー設定
        curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;    // タイムアウトの秒数設定

        // セッション実行
        $res_str = curl_exec( $curl ) ;

        // セッション終了
        curl_close( $curl ) ;


        /***** リクエスト実行結果取得 *****/
        $res_str_arr = json_decode($res_str, ture) ;    // JSONを変換

        /***** 検索結果表示 *****/
        foreach ($res_str_arr['statuses'] as $twit_result){
                $twit_content = $twit_result['full_text'];
                $twit_time = date("Y年m月d日 H時i分s秒",strtotime($twit_result['created_at']));
                echo "<<<　" .$twit_time . "　>>>";
                echo "\r\n" ;
                echo $twit_content;
                echo "\r\n";
                echo "---------------------------------------";
                echo "\r\n";
        }
?>
