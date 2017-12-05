<?php


class RestClient{
  static $instance = null;
  var $url = 'http://casingh.me/projects/vector_assessment/api/';
  var $ch;
  var $query_string;
  var $result_json;

  function __construct(){

  }

  static function getInstance(){
    if (RestClient::$instance === null){
      RestClient::$instance = new RestClient();
    }
    return RestClient::$instance;
  }

  public function init_request(){
    $this->ch = curl_init($this->url . $this->query_string);
  }

  public function default_settings(){
    $this->query_string = "";
    $this->result_json = "";
  }

  /**
   * @param string $data_string
   * @return \Interest
   * @throws Exception
   **/
  public function do_post($data_string){
    $this->default_settings();
    $this->init_request();
    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );
    $this->result_json = curl_exec($this->ch);
    return $this->convert_json_to_php();
  }

  public function do_put($json_data){
    $this->default_settings();
    $this->init_request();
    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data))
    );
    $this->result_json = curl_exec($this->ch);
    return $this->convert_json_to_php();
  }

  public function do_get($query = ""){
    $this->default_settings();
    $this->query_string = $query;
    $this->init_request();
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    $this->result_json = curl_exec($this->ch);
    return $this->convert_json_to_php();
  }

  public function convert_json_to_php($json = ""){
    if (empty($json)){
      $json = $this->result_json;
    }
    $decoded = json_decode($json);
    if ($decoded === null){
      throw new Exception("Json not returned " . var_export($json, true), 1);
    }
    return $decoded;
  }
}
