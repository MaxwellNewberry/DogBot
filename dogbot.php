<?php

//require dirname(__FILE__) . "/database/db.func.php";

class DogBot {

    /*
     * Checks whether notification has been serviced.
     */
    function checkNotification($id) {
        $sql = DB::prepare("SELECT COUNT(*) FROM `db_posts` WHERE `pid` = :id");
        $sql->execute(array('id'=>$id));
        return ($sql->fetchColumn() > 0);
    }

    function getImageURL($username) {
        $sql = DB::prepare("SELECT * FROM `db_images` ORDER BY RAND();");
        $sql->execute();

        $images_array = $sql->fetchAll();
        $selected_index = $this->getIndex($username, $images_array);
        return $images_array[$selected_index]['url'];
    }

    function getIndex($username, $arr, $i=0) {
        if($i == (sizeof($arr)-1)) {
            return "https://ae01.alicdn.com/kf/HTB1IVc7GVXXXXXmXFXXq6xXFXXXz/119328304/HTB1IVc7GVXXXXXmXFXXq6xXFXXXz.jpg";
        }

        $rand = rand(0, sizeof($arr)-1); // Select random image
        if(!$this->checkImage($username, $arr[$rand]['url'])) {
            return $rand;
        }
        else {
            return $this->getIndex($username, $arr, $i+1);
        }
        return 0;
    }

    function checkImage($username, $url) {
        $sql = DB::prepare("SELECT count(*) FROM `db_posts` WHERE `username` = :username AND `url` = :url");
        $sql->execute(array('username' => $username, 'url' => $url));
        return ($sql->fetchColumn() > 0);
    }

}