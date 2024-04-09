# GLPIwithBookstack GLPI plugin

Bookstack is a lovely tool for knowledge management.
It presents the data like a library.
I prefer Bookstack over the build in GLPI knowledge base so I try to integrate Bookstack's API into GLPI frontend.

For now this plugin needs to be edited before it can be used.
You need to edit the file inc/integrate.class.php and set your Bookstack URL in the variable $bookstack_url.
Also you need to generate an API token for your user which need to be set in the variable $bookstack_token (id and secret).

After that you can install and enable the plugin.

The plugin adds a new tab to the ticket view form named _Knowledge base_.
The content of the tab is rendered in php and access your Bookstack through it's API and use the Bookstack search to lookup for the words in the ticket title.
All the results (pages) are printed with title and preview in a table with clickable title so you can jump directly to the Bookstack page.
There is also a Link at the top to jump to the search in Bookstack directly.

## Contributing

* Open a ticket for each bug/feature so it can be discussed
* Follow [development guidelines](http://glpi-developer-documentation.readthedocs.io/en/latest/plugins/index.html)
* Refer to [GitFlow](http://git-flow.readthedocs.io/) process for branching
* Work on a new branch on your own fork
* Open a PR that will be reviewed by a developer
