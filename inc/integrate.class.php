<?php

use Config as GlpiConfig;

class PluginGlpiwithbookstackIntegrate extends CommonGLPI
{
    /**
     * This function is called from GLPI to allow the plugin to insert one or more item
     *  inside the left menu of a Itemtype.
    */
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0)
    {
		// call function for search results and display number of results in tab
		$config = new self();
		$table_with_results = $config->getBookstackSearchResults($item->fields['itilcategories_id']);
		$my_config = GlpiConfig::getConfigurationValues('plugin:Glpiwithbookstack');
        return self::createTabEntry($my_config['display_text_tab_name'], $table_with_results['total']);
    }
    /**
	 * @param string $category String with the category and its subcategories, depending on the config they could be cut
	 *
	 * @return void
	*/
	function getBookstackSearchResults($categoryid)
	{
		// load plugin configuration
		$my_config = GlpiConfig::getConfigurationValues('plugin:Glpiwithbookstack');
		// set some data from the config as variables
		$bookstack_url = $my_config['bookstack_url'];
		$bookstack_token = [$my_config['bookstack_token_id'], $my_config['bookstack_token_secret']];
		// if url and token are not set, return without any output
		if ($bookstack_url === '' OR $bookstack_token[0] === '' OR $bookstack_token[1] === '')
		{
			return true;
		}
		// Get GLPI database connection
		global $DB;
		/* build the search term
		 * if setting search_category_name_only is true then cut top level categories and use category name only
		 * if setting search_category_name_only is false then use the whole path like category > subcategory > subsubcategory
		*/
		$search = '';
		/*if (true)
		{

		}
		else */if ($my_config['search_category_name_only'])
		{
			$result = $DB->request('SELECT name FROM glpi_itilcategories WHERE id = '.$categoryid);
			// only 1 should be returned so just get the current (and only) row
			$row = $result->current();
			$search = str_replace(' ', '+', $row['name']);
		}
		else if ($my_config['search_category_completename_but_only_visible'])
		{
			$query = 'WITH RECURSIVE getParent AS (';
			$query .= ' SELECT 1 AS row_num, id AS child_id, name AS child_name, itilcategories_id AS child_itilcategories_id, is_helpdeskvisible as child_is_helpdeskvisible FROM glpi_itilcategories WHERE id = ';
			$query .= $categoryid;
			$query .= ' UNION ALL';
			$query .= ' SELECT row_num+1, id, name, itilcategories_id, is_helpdeskvisible FROM getParent, glpi_itilcategories WHERE id = child_itilcategories_id AND child_itilcategories_id <> 0)';
			$query .= ' SELECT GROUP_CONCAT(child_name ORDER BY row_num DESC SEPARATOR \'+\') AS part_name FROM getParent WHERE child_is_helpdeskvisible = 1;';
			$result = $DB->request($query);
			// only 1 should be returned so just get the current (and only) row
			$row = $result->current();
			// if the result string is empty return 0 as total and empty table
			if($row['part_name'] == '')
			{
				$table_with_results['total'] = 0;
				$table_with_results['table'] =  '';
				return $table_with_results;
			}
			else
			{
				$search = str_replace(' ', '+', str_replace(' > ', ' ', ($row['part_name'])));
			}
		}
		else
		{
			$result = $DB->request('SELECT completename FROM glpi_itilcategories WHERE id = '.$categoryid);
			// only 1 should be returned so just get the current (and only) row
			$row = $result->current();
			$search = str_replace(' ', '+', str_replace(' > ', ' ', ($row['completename'])));
		}
		/*
		 * If the option search_in_tags_only is true in the configuration
		 * then set the whole search into brackets so it is a tag for Bookstack
		*/
		if ($my_config['search_in_tags_only'])
		{
			$search = '['.$search.']';
		}
		/*
		 * Create 2 urls url_api and url_front and a href element for url display
		 * url_api	 = url for api call
		 * url_front = url for the displayed link in frontend
		 * If the option search_type_pages_only is thue in the configuration
		 * then add the type page to the search so Bookstack looks for pages only
		*/
		if ($my_config['search_type_pages_only'])
		{
			$url_api	 = $bookstack_url.'/api/search?count='.($my_config['display_max_search_results']).'&query='.$search.'+{type:page}';
			$url_front	 = $bookstack_url.'/search?term='.$search.'+{type:page}';
		}
		else
		{
			$url_api	 = $bookstack_url.'/api/search?count='.($my_config['display_max_search_results']).'&query='.$search;
			$url_front	 = $bookstack_url.'/search?term='.$search;
		}
		$search_term = str_replace('[search_term]', '"'.$search.'"', $my_config['display_text_search_on_bookstack']);
		$search_term = str_replace('+', ' ', $search_term);
		$url_display = '<a target="_blanc" href="'.$url_front.'">'.$search_term.'</a>';
		/*
		 * Initialize curl for Bookstack API call and search
		 * Get the response and parse it
		*/
		$ch = curl_init($url_api);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		// set timeout for curl to prevent long time loading if Bookstack instance is not reachable
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $my_config['curl_timeout']);
		// load the option curl_ssl_verifypeer from config so the user can disable the check
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $my_config['curl_ssl_verifypeer']);
		// do not print the return
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Token '.($bookstack_token[0]).':'.($bookstack_token[1])]);
		$response_json = curl_exec($ch);
		curl_close($ch);
		// check if curl failed then return and do not return the data table
		if (!isset($response_json) || $response_json === false)
		{
			$table_with_results['table'] = "<hr><h1 style='color:red;'>BookStack Error: Please contact your administrator.</h1><hr>";
			return $table_with_results;
		}
		$response=json_decode($response_json, true);
		// check if curl return an error
		if (isset($response["error"]))
		{
			$table_with_results['table'] = "<hr><h1 style='color:red;'>BookStack Error: ".$response["error"]["message"]."</h1><hr>";
			return $table_with_results;
		}
		/*
		 * Display the Bookstack search results in a table
		 * first check if there are results, if not return and do not return the data table
		*/
		if ($response['total'] === 0)
			return true;

		$table_with_results['total'] = $response['total'];
		$table_with_results['table'] =  '<table cellpadding="10px">';
		$table_with_results['table'] .= '<tr>';
		$table_with_results['table'] .= '<td style="min-width: 200px;"><h1>'.($my_config['display_text_title']).'</h1></td>';
		$table_with_results['table'] .= '<td><h1>'.($my_config['display_text_content_preview']).'</h1></td>';
		$table_with_results['table'] .= '<td><h1 style="color: blue; text-decoration: underline; text-align: right;">'.$url_display.'</h1></td>';
		$table_with_results['table'] .= '</tr>';
		// counter for counting the results
		$counter = 0;
		foreach($response['data'] as $book) {
			$table_with_results['table'] .= '<tr style="border-top: 1px solid black;">';
			$table_with_results['table'] .= '<td><b><a target="_blanc" href="'.$bookstack_url.'/link/'.($book['id']).'">'.($book['name']).'</a></b></td>';
			$table_with_results['table'] .= '<td colspan="2">'.($book['preview_html']['content']).'</td>';
			$table_with_results['table'] .= '</tr>';
			$counter++;
		}
		// if not all results are displayed then show how many displayed and how many missing
		if (($response['total']) > $counter)
		{
			$display_max_results_text = str_replace('[result_count]', $counter, $my_config['display_text_max_results_reached']);
			$display_max_results_text = str_replace('[max_results]', $response['total'], $display_max_results_text);
			$display_max_results_text = str_replace('[url]', '<b>'.$url_display.'</b>', $display_max_results_text);
			$table_with_results['table'] .= '<tr style="border-top: 1px solid black;"><td colspan="3">'.$display_max_results_text.'</td></tr>';
		}
		$table_with_results['table'] .= '</table>';
		return $table_with_results;
	}
    /**
     * This function is called from GLPI to render the form when the user click
     *  on the menu item generated from getTabNameForItem()
    */
    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0)
    {
		// call function for search result and display
		$config = new self();
		$table_with_results = $config->getBookstackSearchResults($item->fields['itilcategories_id']);
		echo $table_with_results['table'];
        return true;
    }
	/**
		* Display contents at the begining of item forms.
		*
		* @param array $params Array with "item" and "options" keys
		*
		* @return void
		*/
	static public function postTicketForm($params) {
		$item = $params['item'];
		$options = $params['options'];

		// Check if option-id is not set that means new ticket and check if search for title is activated
		if (!isset($options['id']) && $item instanceof Ticket && true) // && $options['itilcategories_id'] !== 0
		{
			// call function for search result and display
			$config = new self();
			$table_with_results = $config->getBookstackSearchResults($options['itilcategories_id']);
			echo $table_with_results['table'];
			return true;
		}
		// Check if option-id is not set and categoy is set, that means new ticket and category selected
		else if (!isset($options['id']) && $item instanceof Ticket && $options['itilcategories_id'] !== 0)
		{
			// call function for search result and display
			$config = new self();
			$table_with_results = $config->getBookstackSearchResults($options['itilcategories_id']);
			echo $table_with_results['table'];
			return true;
		}
	}
}
