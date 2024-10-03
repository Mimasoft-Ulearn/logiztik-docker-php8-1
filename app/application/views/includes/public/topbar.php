<nav class="navbar public-navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="fa fa-cog"></span>
        </button>
        <!-- <a class="navbar-brand" href="<?php echo_uri('dashboard'); ?>"><img src="<?php echo get_file_uri(get_setting("system_file_path") . get_setting("site_logo")); ?>" /></a> -->
        <a class="navbar-brand" href="<?php echo_uri('dashboard'); ?>"><img src="<?php echo get_file_uri("files/system/default-site-logo.png"); ?>" /></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">

        <ul class="nav navbar-nav navbar-right">

            <li class="user-language-option">
                <?php echo js_anchor("<i class='fa fa-globe-americas'></i>", array("id" => "personal-language-icon", "class" => "dropdown-toggle", "data-toggle" => "dropdown")); ?>

                <ul class="dropdown-menu p0" style="height: 80px; min-width: 170px;">
                    <li>
                        <?php
 
                        $language_cookie = $this->input->cookie('public_language', TRUE);

                        $dir = "./application/language/";
                        if (is_dir($dir)) {
                            if ($dh = opendir($dir)) {
                                while (($file = readdir($dh)) !== false) {
                                    if ($file && $file != "." && $file != ".." && $file != "index.html") {
                                        if($file == "spanish" || $file == "english"){

                                            $language_status = "";
                                            $language_text = ucfirst(lang($file));

                                            if ($language_cookie == strtolower($file)) {
                                                $language_status = "<span class='pull-right checkbox-checked m0'></span>";
                                                $language_text = "<strong class='pull-left'>" . $language_text . "</strong>";
                                            } else if(!$language_cookie && $file == "spanish"){
                                                $language_status = "<span class='pull-right checkbox-checked m0'></span>";
                                                $language_text = "<strong class='pull-left'>" . lang("spanish") . "</strong>";
                                            }
                                            
                                            echo ajax_anchor(get_uri("public_forms/save_public_lang/$file"), $language_text . $language_status, array("class" => "clearfix", "data-reload-on-success" => "1"));
                                        }
                                    }
                                }
                                closedir($dh);
                            }
                        }
                        ?>
                    </li>
                </ul>
            </li>


            <?php
            /*if (get_setting("module_knowledge_base")) {
                echo " <li>" . anchor("knowledge_base", lang("knowledge_base")) . " </li>";
            }*/

            if (!get_setting("disable_client_login")) {
                echo " <li>" . anchor("signin", lang("signin")) . " </li>";
            }

            /*if (!get_setting("disable_client_signup")) {
                echo " <li>" . anchor("signup", lang("signup")) . " </li>";
            }*/
            ?>
            <li class="mr15 pr15 pl15">
            </li>
        </ul>
    </div>
</nav>

