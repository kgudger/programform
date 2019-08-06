<?php
/**
 * @file mainpage.php
 * MainPage class for handling most page duties.
 *
 * @author Keith Gudger
 * @copyright  (c) 2014, Keith Gudger, all rights reserved
 * @license    http://opensource.org/licenses/BSD-2-Clause
 * @version    Release: 1.0
 * @package    KSQD Spinitron / Program page form
 *
 */

include_once "/var/www/html/wp-content/plugins/programform/includes/redirect.php";
include_once "/var/www/html/wp-content/plugins/programform/includes/formlib.php";
include_once "/var/www/html/includes/util.php";

/**
 * Parent class used for all form pages.
 *
 * Implements main and checkForm
 * provides template for processData and showContent
 */
class MainPage {
    /*--- private variables ---*/
    protected $formL;	//!< form from library formlib
	protected $sessnp; 	//!<sets as non-profit or individual?
	protected $db;		//!< PDO data base object
	protected $checkArray; //!<check array for checkForm.
	protected $apiBaseUrl; //!< Base URL for Spinitron
	protected $spin_api_key; //!< API key for Spinitron from database

    /**
     * Constructor.
     *
     * @param $db is the PDO database object
	 * @param $seesnp is yes = non-profit, no = individual account
	 * @param @checkArray tells checkForm what and how to check form
     */
    function __construct($db = "", $sessnp="no", $checkArray="") {
		$this->db = $db ;
		$this->sessnp = $sessnp;
    	$this->formL = new FormLib("error") ;
		$this->checkArray = $checkArray;
		$this->apiBaseUrl = "https://spinitron.com/api";
		$sql = "SELECT option_value 
		        FROM `wp_options` 
		        WHERE option_name = 'WsPin_API_Key_settings'";
		// get api key from WP database
		$result = $db->query($sql);
		$row = $result->fetch(PDO::FETCH_ASSOC);
		$pieces = explode('"',$row['option_value']);
		$this->spin_api_key = $pieces[3];
    }

    /**
     * Handles the processing of the whole page.
     *
     * @param $title is the title of the page.
     * @param $normRedirect is the usual redirect.
     * @param $altRedirect is the alternate redirect.
     */
	function main($title = "", &$uid, $normRedirect = "",
					$altRedirect = "") {
	
		if (isset($_POST["Submit"])) {
    	    $this->checkForm();
    	    if (!($this->formL->iserror())) { // data is OK
				$this->processData($uid);
    		    if (!($this->formL->iserror())) { // data is OK
					if ( ($this->sessnp) == "yes" ) { // for non-profit
			            if (!empty($altRedirect)) 
							redirect($altRedirect);
					} else {						// for individual
			            if (!empty($normRedirect)) 
				            redirect($normRedirect);
					}
				}
			}
		}
//    	include "includes/header.php";	// same header for every page
    	return $this->showContent($title, $uid);
//    	include "includes/footer.php";  // same footer for every page
	}

    /**
     * Form Checking using checkArray.
     *
     */
	function checkForm() {
		if (!empty($this->checkArray)) {
			foreach ($this->checkArray as $checkval) {
				$this->formL->$checkval[0](
								$checkval[1],$checkval[2]);
			}
		}
	}

    /**
     * Processes the db requests (if necessary, overwritten).
     *
	 * @param $uid passed by reference for database
     */
	function processData(&$uid) {
	}

    /**
     * displays the actual page (always overwritten).
     *
	 * @param $title is page title.
	 * @param $uid passed by reference for database
     */
	function showContent($title, &$uid) {
	}
}
?>
