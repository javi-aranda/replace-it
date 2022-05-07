<?php
    /**
    * Plugin Name: ReplaceIt!
    * Plugin URI: https://github.com/javisenberg/replace-it/
    * Description: Filtra todas las palabras ofensivas de los comentarios.
    * Version: 0.1
    * Author: Javisenberg
    * Author URI: https://github.com/javisenberg/
    * License: GPL2
    */
    //define( 'REPLACEIT_URL', plugin_dir_path( __FILE__ )  );
?>
<?php
    function replaceIt_Install(){
        global $wpdb;
        $table_comments= $wpdb->prefix . "comments";
        $sql = "ALTER TABLE $table_comments ADD COLUMN `comment_replaceit` BOOL DEFAULT FALSE;";
        $wpdb->query($sql);
        $table_replaceit = $wpdb->prefix . "replaceit";
        $sql = "CREATE TABLE IF NOT EXISTS $table_replaceit(
            wordToReplace VARCHAR(255) PRIMARY KEY,
            wordReplaced VARCHAR(255) NOT NULL
        );";
        $wpdb->query($sql);
        $seek_and_change = "CREATE PROCEDURE IF NOT EXISTS `seek_and_change`()
        BEGIN
        DECLARE post_id INT;
        DECLARE post_temp TEXT;
        DECLARE old_str, new_str VARCHAR(255);
        DECLARE finale BOOL DEFAULT FALSE;
        DECLARE replaceit_seeker CURSOR FOR SELECT comment_id, comment_content FROM $table_comments WHERE comment_replaceit <> 1;
        DECLARE replaceit_replacer CURSOR FOR SELECT wordToReplace, wordReplaced FROM $table_replaceit;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET finale = TRUE;
        OPEN replaceit_seeker;
            replaceit_first_loop: LOOP
                FETCH replaceit_seeker INTO post_id, post_temp;
                IF finale THEN LEAVE replaceit_first_loop;
                END IF;
                UPDATE $table_comments SET comment_replaceit=1 WHERE comment_id = post_id;
                OPEN replaceit_replacer;
                    replaceit_sec_loop: LOOP
                        FETCH replaceit_replacer INTO old_str, new_str;
                        IF finale THEN LEAVE replaceit_sec_loop;
                        END IF;
                        IF (LOCATE(old_str, post_temp) > 0) THEN UPDATE $table_comments SET comment_content=REPLACE(post_temp, old_str, new_str) WHERE comment_id = post_id;
                        END IF;
                    END LOOP;
                CLOSE replaceit_replacer;
                SET finale = FALSE;
            END LOOP;
        CLOSE replaceit_seeker;
        END";
        $wpdb->query($seek_and_change);
    }

    function replaceIt_Uninstall(){
        global $wpdb;
        $table_name = $wpdb->prefix . "comments";
        $sql = "ALTER TABLE $table_name DROP COLUMN `comment_replaceit`";
        $wpdb->query($sql);
        $table_name = $wpdb->prefix . "replaceit";
        $sql = "DROP TABLE $table_name;";
        $wpdb->query($sql);
        $drop_seek_and_change = "DROP PROCEDURE IF EXISTS `seek_and_change`";
        $wpdb->query($drop_seek_and_change);
    }

    function replaceIt_GetList() {
        global $wpdb;
        $table = $wpdb->prefix . "replaceit";
        $sql = "SELECT `wordToReplace` FROM $table ORDER BY `wordToReplace`;";
        $r = $wpdb->get_results($sql, ARRAY_A);
        foreach($r as $row){
            $arr[] = $row['wordToReplace'];
        }
        return $arr;
    }

    function replaceIt_Panel(){
        include('view/menu.php');
        global $wpdb;
        $table = $wpdb->prefix . "replaceit";
        if(isset($_POST['insert']) && !empty($_POST['og_post'] && !empty($_POST['replace_post']))){
            $sql = "INSERT INTO $table (wordToReplace, wordReplaced) VALUES ('{$_POST['og_post']}', '{$_POST['replace_post']}');";
            $wpdb->query($sql);
        }
        if(isset($_POST['delete']) && !empty($_POST['options'])){
            $sql = "DELETE FROM $table WHERE `wordToReplace`='{$_POST['options']}';";
            $wpdb->query($sql);
        }
        if(isset($_POST['replace'])){
            $wpdb->query("CALL seek_and_change()");
        }
    }

    function replaceIt_Add_Menu(){
        if (function_exists('add_options_page')) {
            add_menu_page('ReplaceIt', 'ReplaceIt', 8, basename(__FILE__), 'replaceIt_Panel');
            //add_options_page('ReplaceIt', 'ReplaceIt', 8, basename(__FILE__), 'replaceIt_Panel');
        }
    }

    function replaceIt_plugin_action_links( $links ) {
        $links[] = '<a href="'. esc_url( get_admin_url( null, 'admin.php?page=replaceit.php' ) ) .'">' . __( 'Settings' ) . '</a>';
        return $links;
    }

    if (function_exists('add_action')) {
        add_action('admin_menu', 'replaceIt_Add_Menu');
    }
    
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'replaceIt_plugin_action_links' );
    add_action('activate_replaceit/replaceit.php', 'replaceIt_Install');
    add_action('deactivate_replaceit/replaceit.php', 'replaceIt_Uninstall');
     
?>