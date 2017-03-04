<?php

class Navigation {

    function __construct() {

    }

    public function pluginsMenu() {

        global $con;

        $pluginsArray = array();

        $plugins_q = 'SELECT * FROM cms_plugins
                      ORDER BY
                      sortorder ASC';
        $plugins_r = mysqli_query($con,$plugins_q);
        if(mysqli_num_rows($plugins_r)) {
            while ($plugins = mysqli_fetch_array($plugins_r, MYSQLI_ASSOC)) {

                $plugin         = array();
                $plugin['id']   = $plugins['id'];
                $plugin['name'] = $plugins['name'];
                $plugin['url']  = 'view/'.$plugins['id'];
                array_push($pluginsArray, $plugin);
            }
            return $pluginsArray;
        } else {
            return false;
        }
    }

    public function mainNav() {

        $menuArray = array(
            '1' => array(
                'name' => 'Home',
                'url'  => '',
                'icon' => 'home',
                'uri'  => ''
            ),
            '2' => array(
                'name' => 'Pages',
                'url'  => 'pages',
                'icon' => 'files-o',
                'uri'  => 'pages',
                'sub'  => array(
                    array(
                        'name' => 'Pages',
                        'url'  => '',
                    ),
                    array(
                        'name' => 'Add Page',
                        'url'  => 'add',
                    ),
                    array(
                        'name' => 'Categories',
                        'url'  => 'cats',
                    ),
                    array(
                        'name' => 'New Category',
                        'url'  => 'addcat',
                    )
                )
            ),
            '3' => array(
                'name' => 'Plugins',
                'url'  => 'plugins',
                'icon' => 'gears',
                'uri'  => 'plugins',
                'sub'  => Navigation::pluginsMenu()
            ),
            '4' => array(
                'name' => 'Contacts',
                'url'  => 'contacts',
                'icon' => 'users',
                'uri'  => 'contacts',
                'sub'  => array(
                    array(
                        'name' => 'View Contacts',
                        'url'  => '',
                    ),
                    array(
                        'name' => 'Add Contact',
                        'url'  => 'add',
                    ),
                    array(
                        'name' => 'Categories',
                        'url'  => 'cats',
                    ),
                    array(
                        'name' => 'New Category',
                        'url'  => 'newcat',
                    )
                )
            ),
            '5' => array(
                'name' => 'E-Marketing',
                'url'  => 'mail',
                'icon' => 'envelope-o',
                'uri'  => 'mail',
                'sub'  => array(
                            array(
                                'name' => 'Campaigns',
                                'url'  => '',
                            ),
                            array(
                                'name' => 'New Campaign',
                                'url'  => 'newcampaign',
                            )
                          )
            ),
            '6' => array(
                'name' => 'Docs &amp; Resources',
                'url'  => 'filemanager',
                'icon' => 'archive',
                'uri'  => 'filemanager',
            )
        );

        return $menuArray;
    }

    public function renderNav() {

      global $url;

      $this->globalNav  = Navigation::mainNav();
      $menuStr		  = "<div class=\"leftNav\">\n";
      $menuStr		 .= "<ul id=\"menu\">";

    	foreach ($this->globalNav as $navItem){


            $menuStr  .= "\n<li>".
                         "<a".(isset($navItem['sub'])?'':' href="'. _EQROOT_ . $navItem['url'].'"')." title=\"\" class=\"".
                         (isset($navItem['sub']) && $navItem['sub']!= false?'exp ':'').($url[0]==$navItem['url']?' selected':'')."\">".
                         "<span><i class=\"fa fa-".$navItem['icon']." fa-fw\"></i>" .
                         $navItem['name']."</span></a>";

            	if(isset($navItem['sub']) && $navItem['sub']!= false) {
            		$menuStr .= "\n   <ul class=\"sub ".($url[0]==$navItem['url']?'active':'')."\">";
            		foreach ($navItem['sub'] as $navSub){
            			$menuStr .= "\n      <li><a href=\"". _EQROOT_ . $navItem['url']."/".$navSub['url']."\" title=\"\">".$navSub['name']."</a></li>";
            		}
            		$menuStr .= "\n   </ul>\n";
            	}
            $menuStr .= "</li>";
        }
        $menuStr .= "\n</ul>";
	 	$menuStr .= "\n</div>\n";

        return $menuStr;
    }
}
