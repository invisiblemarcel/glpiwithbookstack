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
		$my_config = GlpiConfig::getConfigurationValues('plugin:Glpiwithbookstack');
        return self::createTabEntry($my_config['display_text_tab_name']);
    }
    /**
	 * @param string $category String with the category and its subcategories, depending on the config they could be cut
	 *
	 * @return void
	*/
	function echoBookstackSearchResults($categoryid)
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
		// get the details of the category by id, we need the name of the category for the search
		$result = $DB->request([
			'SELECT' => '*',
			'FROM'   => 'glpi_itilcategories',
			'WHERE'  => ['id' =>  ($categoryid)]
		]);
		// only 1 should be returned so just get the current (and only) row
		$row = $result->current();
		/* build the search term
		 * if setting search_category_name_only is true then cut top level categories and use category name only
		 * if setting search_category_name_only is false then use the whole path like category > subcategory > subsubcategory
		*/
		if ($my_config['search_category_name_only'])
		{
			$search = str_replace(' ', '+', $row['name']);
		}
		else
			$search = str_replace(' ', '+', str_replace(' > ', ' ', ($row['completename'])));
		/*
		 * Create 2 urls url_api and url_front and a href element for url display
		 * url_api	 = url for api call
		 * url_front = url for the displayed link in frontend
		*/
		$url_api	 = $bookstack_url.'/api/search?count='.($my_config['display_max_search_results']).'&query='.$search.'+{type:page}';
		$url_front	 = $bookstack_url.'/search?term='.$search.'+{type:page}';
		$search_term = str_replace('[search_term]', '"'.$search.'"', $my_config['display_text_search_on_bookstack']);
		$search_term = str_replace('+', ' ', $search_term);
		$url_display = '<a target="_blanc" href="'.$url_front.'">'.$search_term.'</a>';
		/*
		 * Initialize curl for Bookstack API call and search
		 * Get the response and parse it
		*/
		$ch = curl_init($url_api);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		// do not print the return
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Token '.($bookstack_token[0]).':'.($bookstack_token[1])]);
		$response_json = curl_exec($ch);
		curl_close($ch);
		$response=json_decode($response_json, true);
		/*
		 * Display the Bookstack search results in a table
		*/
		echo '<table cellpadding="10px">';
		echo '<tr>';
		echo '<td style="min-width: 200px;"><h1>'.($my_config['display_text_book_page']).'</h1></td>';
		echo '<td><h1>'.($my_config['display_text_content_preview']).'</h1></td>';
		echo '<td><h1 style="color: blue; text-decoration: underline; text-align: right;">'.$url_display.'</h1></td>';
		echo '</tr>';
		// counter for counting the results
		$counter = 0;
		foreach($response['data'] as $book) {
			echo '<tr style="border-top: 1px solid black;">';
			echo '<td><b><a target="_blanc" href="'.$bookstack_url.'/link/'.($book['id']).'">'.($book['name']).'</a></b></td>';
			echo '<td colspan="2">'.($book['preview_html']['content']).'</td>';
			echo '</tr>';
			$counter++;
		}
		// if not all results are displayed then show how many displayed and how many missing
		if (($response['total']) > $counter)
		{
			$display_max_results_text = str_replace('[result_count]', $counter, $my_config['display_text_max_results_reached']);
			$display_max_results_text = str_replace('[max_results]', $response['total'], $display_max_results_text);
			$display_max_results_text = str_replace('[url]', '<b>'.$url_display.'</b>', $display_max_results_text);
			echo '<tr style="border-top: 1px solid black;"><td colspan="3">'.$display_max_results_text.'</td></tr>';
		}
		echo '</table>';
		return true;
	}
    /**
     * This function is called from GLPI to render the form when the user click
     *  on the menu item generated from getTabNameForItem()
    */
    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0)
    {
		// call function for search result display
		$config = new self();
		$config->echoBookstackSearchResults($item->fields['itilcategories_id']);
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
		// Check if option-id is not set and categoy is set, that means new ticket and category selected
		if (!isset($options['id']) && $options['itilcategories_id'] !== 0) {
			// call function for search result display
			$config = new self();
			$config->echoBookstackSearchResults($options['itilcategories_id']);
			return true;
		}
	}
}
