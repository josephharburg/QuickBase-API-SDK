<?php
 /*
 Title : QuickBase PHP Rest API SDK
 Author : Joseph Harburg (josephharburg@gmail.com)
 Description : The QuickBase PHP SDK is a very simple class for basic interaction with the QuickBase REST API.
 The QuickBase REST API is documented here:
 https://developer.quickbase.com/

*/

 // ini_set('display_errors', 'on'); // ini setting for turning on errors
 Class QuickBaseRestApi {
  var $user_token  = '';	// Valid user token
  var $app_token = ''; //Valid app token. Required.
  private $access_token = ''; //Created with get_temporary_access_token method
	public $base_url    = "https://api.quickbase.com/v1/"; //The current base url at the time.
  public $realm = ''; //Quickbase realm string before .quickbase.com
  public $user_agent = ''; //User agent

	public function __construct($user_token='', $app_token = '', $realm = '', $user_agent = '', $access_token = '') {
		if($app_token) $this->app_token = $app_token;

    if($user_token) $this->user_token = $user_token;

    if($realm) $this->realm = $realm . '.quickbase.com';

    if($user_agent) $this->user_token = $user_token;

    if($access_token) $this->access_token = $access_token;
	}

  /**
  * Method to set the temporary access token
  *
  * @param string $token
  */

  public function set_access_token($token){
    $this->$access_token = $token;
  }


  /**
  * Method to get the temporary access token of object instance
  *
  */

  public function get_access_token(){
    return $this->$access_token;
  }

  /**
  * Method to get and set a temporary access token
  *
  * @param string $db_id The id of the database you want access to. Required.
  */

  public function get_and_set_temporary_access_token($db_id){
    $headers = array(
    "QB-Realm-Hostname: $this->realm",
    "User-Agent: QuickBaseRestApiApp",
	  "QB-App-Token: $this->app_token",
    "Content-Type: application/json",
  );

  $url = $this->base_url . "/auth/temporary/$db_id";

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);

  $response = curl_exec($ch);
  $token = json_decode($response,true)['temporaryAuthorization'];
  $this->set_access_token($token);
  }

  /**
  * Method to make the request to QuickBase API
  *
  * @param string $type_of_request The type of http request
  * @param string $endpoint The enpoint to request. Required
  * @param string $body The correctly formatted data for posting. Optional unless Using POST
  *
  * @return mixed $response
  */


  //See https://developer.quickbase.com/ for actions and endpoints
  private function make_api_request($type_of_request = 'GET', $endpoint = '', $body = ''){
    $url = $this->base_url . $endpoint;
    $header_token = ($this->get_access_token()) ? "QB-TEMP-TOKEN:". $this->get_access_token() :  "QB-USER-TOKEN:" . $this->user_token;
    $headers = array(
    "QB-Realm-Hostname: $this->realm",
    "User-Agent: QuickBaseRestApiApp",
	  $header_token,
    "Content-Type: application/json",
  );
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    if($type_of_request == 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $response = curl_exec($ch);

    //This catches errors with the cURL request and logs them. Change the executable code to fit your error logging procedures
    if(curl_errno($ch)){
      error_log("There was an error with the QuickBaseRestApi call/n". "The HTTP Error Code recieved was: ".curl_errno($ch));
    }
    return $response;
  }

  /*--------------------------------------------
                    APP METHODS
  ---------------------------------------------*/
  /**
  * Get an app
  *
  * @see https://developer.quickbase.com/operation/getTable
  *
  * @param string $app_id Required.
  *
  * @return mixed $result
  */

  public function get_an_app($app_id = ''){
    $endpoint = "/apps/$app_id";
    $result = $this->make_api_request("GET", $endpoint);
    return $result;
  }




  /*--------------------------------------------
                    TABLE METHODS
  ---------------------------------------------*/
  /**
  * Get a table from your app
  *
  * @see https://developer.quickbase.com/operation/getTable
  *
  * @param string $table_id Required.
  * @param string $app_id Required.
  *
  * @return mixed $result
  */

  public function get_a_table($table_id = '', $app_id = ''){
    $endpoint = "/tables/$table_id?appId=$app_id";
    $result = $this->make_api_request("GET", $endpoint);
    return $result;
  }


  /**
  * Method to create a table
  *
  * @see https://developer.quickbase.com/operation/createTable
  *
  * @param string $app_id Required.
  * @param string $add_table_data See below and documentation link above.Required.
  *    $update_data = array(
  *     "name": "Table Name", (string)
  *     "description": "Table Description",(string)
  *     "singleRecordName": "Record",(string)
  *     "pluralRecordName": "Records"(string)
  *    );
  *
  * @return mixed $result
  */

  public function create_a_table($app_id = '', $add_table_data){
    $endpoint = "/tables?appId=$app_id";
    $body = json_encode( $add_table_data );
    $result = $this->make_api_request("POST", $endpoint, $body);
    return $result;
  }


  /**
  * Method to update a table
  *
  * @see https://developer.quickbase.com/operation/updateTable
  *
  * @param string $table_id Required.
  * @param string $app_id Required.
  * @param string $update_table_data See below and documentation link above.Required.
  *    $update_data = array(
  *     "name": "Table Name", (string)
  *     "description": "Table Description",(string)
  *     "singleRecordName": "Record",(string)
  *     "pluralRecordName": "Records"(string)
  *    );
  *
  * @return mixed $result
  */

  public function update_a_table($table_id= '', $app_id = '', $update_table_data){
    $endpoint = "/tables/$table_id?appId=$app_id";
    $body = json_encode( $update_table_data );
    $result = $this->make_api_request("POST", $endpoint, $body);
    return $result;
  }

  /*--------------------------------------------
                    REPORT METHODS
  ---------------------------------------------*/

  /**
  * Get all reports from a table
  *
  * @see https://developer.quickbase.com/operation/getTable
  *
  * @param string $table_id Required.
  *
  * @return mixed $result
  */

  public function get_reports_for_a_table($table_id){
    $endpoint = "/reports?tableId=$table_id";
    $result = $this->make_api_request("GET", $endpoint);
    return $result;
  }

  /**
  * Get all reports from a table
  *
  * @see https://developer.quickbase.com/operation/getTable
  *
  * @param string $report_id Required.
  * @param string $table_id Required.
  *
  * @return mixed $result
  */

  public function get_single_report( $report_id ,$table_id ){
    $endpoint = "/reports/$report_id?tableId=$table_id";
    $result = $this->make_api_request("GET", $endpoint);
    return $result;
  }


    /*--------------------------------------------
                      RECORD METHODS
    ---------------------------------------------*/

    /**
    * Make a query for record data
    *
    * @see https://developer.quickbase.com/operation/runQuery
    *
    * @param string $table_id The table to query. Required
    * @param array  $select Array of field ids. Required
    * @param string $where A Quickbase query language formatted bracket enclosed string see documentation link above. Required
    *   $where = {3.CT.'string'}
    * @param array $sort_by A multidimensional array correctly formatted see below. See documentation link above. Optional
    *    $sort_by = array(
    *       array(
    *         "fieldId" => "field id", (int|string) The field id to sort by.
    *         "order" => "ASC|DESC" (string) which order parameter.
    *       ) ...add as many sorting parameters as allowed
    *     )
    *
    * @param array $group_by A multidimensional array correctly formatted. See documentation link above. Optional
    *    $group_by = array(
    *       array(
    *         "fieldId" => "field id", (int|string) The field id to group. Required
    *         "grouping" => "ASC|DESC|equal values" (string) which grouping. Required
    *       )
    *     )
    *
    * @param array $options An array of options. See documentation link above. Optional
    *    $options = array(
    *         "skip" => "number", (int) Number of records to skip. Optional
    *         "compareWithAppLocalTime", => "true" (bool) See documentation. Optional
    *         "top" => "number" (bool) Number of records to display. Optional
    *     )
    *
    * @return mixed $result
    */

    public function query_for_data($table_id, $select, $where, $sort_by, $group_by, $options){
      $endpoint = "/records/query";
      $select = json_encode( $select );
      $where = ($where) ? $where: '""';
      $sort_by = ($sort_by) ? json_encode( $sort_by ): "[{}]";
      $group_by = ($group_by) ? json_encode( $group_by ): "[{}]";
      $options = ($options) ? json_encode( $options ): "{}";
      $body = "{
        'from': $table_id,
        'select': $select,
        'where' : $where,
        'sortBy': $sort_by,
        'groupBy': $group_by,
        'options': $options,
      }";
      $result = $this->make_api_request("POST", $endpoint, $body);
      return $result;
    }

    /**
    * Update or create record(s)
    *
    * @see https://developer.quickbase.com/operation/runQuery
    *
    * @param string $table_id
    * @param array $values_to_update a multidimensional array see below or see SDK documentation
    *     $values_to_update = array(
    *       array(
    *          (string) table primary key field id in quotes. Required =>
    *                    array("value" => (int|string) primary key id to update or new id. Required),
    *          (string) field id value in quotes =>
    *                    array("value" => (int|string) value for field),
    *          (string) field id value in quotes =>
    *                    array("value" => (int|string) value for field),
    *            ... put as may key value pairs that you need
    *        ),
    *       array(
    *           (string) another table primary key field id in quotes =>
    *                  array("value" => (int|string) another record primary key id),
    *           (string) field id value in quotes =>
    *                  array("value" => (int|string) value for field),
    *        ),
    *        ... put as many records as you need
    *     );
    * @param array $fields_to_return A list of field ids to return after update. Optional
    *
    * @return mixed $result
    */

    public function update_or_create_records($table_id = '', $values_to_update, $fields_to_return = array(3)){
      $endpoint = "/records";
      $data = json_encode( $values_to_update );
      $fields_to_return = json_encode( $fields_to_return );
      $body = "{
        'to': $table_id,
        'data': $data,
        'fieldsToReturn': $fields_to_return
      }";
      $result = $this->make_api_request("POST", $endpoint, $body);
      return $result;
    }
}
?>
