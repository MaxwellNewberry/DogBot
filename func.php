<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Database Functions
    include(dirname(__FILE__) . "/database/db.func.php");
    include(dirname(__FILE__) . "/peachapi.php");
    include(dirname(__FILE__) . "/dogbot.php");

    $auth = array(

        'method' => 'login', // two options login or register, default: register
        'login' => array(
            'username' => 'DogBot',
            'password' => ''
            //'name' => '' (add comma)
            // 'name' required if register
        )
    );

    $api = new PeachAPI\peach($auth);
    $db = new DogBot();

    /*
     * Get the notifications for DogBot.
     * (1) Cast object as assoc array and grab data object value
     * (2) Cast data object as assoc array and grab activityItems value
     */
    $notifications = ((array)((array) $api->stream->activity())['data'])['activityItems'];
    foreach($notifications as $notification) {
        $notification = (array) $notification; // Cast as associative array.
        if($notification['type'] == "tag") {
            // Get notifications of tags only.
            $postID = ((array) $notification['body'])['postID'];
            if(!$db->checkNotification($postID)) {
                // Have not yet actioned notification.
                $author = ((array) ((array) $notification['body'])['authorStream'])['name'];
                $imageURL = $db->getImageURL($author);

                // Post image
                var_dump($api->post->post(array('type' => 'image', 'image' => array($imageURL), 'body' => 'WOOF! @' . $author . ' Here\'s your dog')));

                // Add Post to DB so we don't duplicate
                $sql = DB::prepare("INSERT INTO `db_posts` VALUES(:pid, :username, :url);");
                $sql->execute(array('pid' => $postID, 'username' => $author, 'url' => $imageURL));
            }
        }
    }
