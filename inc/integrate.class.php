<?php

class PluginGlpiwithbookstackIntegrate extends CommonGLPI
{
     /**
     * This function is called from GLPI to allow the plugin to insert one or more item
     *  inside the left menu of a Itemtype.
     */
    function getTabNameForItem(CommonGLPI $item, $withtemplate=0)
    {
        return self::createTabEntry('Knowledge base');
    }

    /**
     * This function is called from GLPI to render the form when the user click
     *  on the menu item generated from getTabNameForItem()
     */
    static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0)
    {
    	$bookstack_url = 'https://example.com';
    	$bookstack_token = ['token_id', 'token_secret'];
	$search = str_replace(' ', '+', ($item->fields['name']));
        $url = $bookstack_url.'/api/search?query='.$search.'+{type:page}';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	// do not print the return
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Token '.($bookstack_token[0]).':'.($bookstack_token[1])]);
	$response_json = curl_exec($ch);
	curl_close($ch);
	$response=json_decode($response_json, true);
	
	echo "<h1 style='color:blue;text-decoration:underline'><a target='_blanc' href='".$bookstack_url."/search?term=".$search."+{type:page}'>View Knowledge Base search on Bookstack</a></h1>";
	
	echo '<table cellpadding="5px">';
	echo '<tr><td><h1>Book page</h1></td><td><h1>Content preview</h1></td></tr>';
	foreach($response['data'] as $book) {
		//var_dump($book);
		echo '<tr><td><b><a target="_blanc" href="'.$bookstack_url.'/link/'.($book['id']).'">'.($book['name']).'</a></b></td><td>'.($book['preview_html']['content']).'</td></tr>';
	}
	echo '</table>';
	
	//var_dump($item['Ticket']);
	//var_dump($item);
	
	//var_dump($response);
        return true;
    }
}
