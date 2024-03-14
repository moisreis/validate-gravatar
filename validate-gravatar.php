<?php
/*
Plugin Name: Validate Gravatar
Description: This plugin retrieves profile data including profile pictures and initials from the last N comments.
Version: 1.0
Author: Moisés Reis
License: GPL2
*/

/**
 * Validate Gravatar existence based on email.
 *
 * @param string $email The email address.
 * @return bool Whether Gravatar exists for the given email.
 */
function sdb_validate_gravatar($email)
{
    // Craft a potential URL and test its headers
    $hash = md5(strtolower(trim($email)));
    $uri = 'https://www.gravatar.com/avatar/' . $hash . '?d=404';
    $headers = @get_headers($uri);
    // Check if the HTTP status code is 200 (OK)
    return strpos($headers[0], '200') !== false;
}

/**
 * Get profile pictures and initials from the last N comments.
 *
 * @param int $post_id The ID of the post.
 * @param int $number_of_comments Number of comments to retrieve profile data from.
 * @return array Array containing profile data for each comment.
 */
function get_last_comments_profile_data($post_id, $number_of_comments = 3)
{
    // Retrieve comments for the given post.
    $comments = get_comments(array('post_id' => $post_id));
    // Initialize an array to store profile picture URLs and initials.
    $profile_data = array();
    // Loop through the last N comments to get profile pictures and initials.
    $last_comments = array_slice($comments, 0, $number_of_comments);
    foreach ($last_comments as $comment) {
        $comment_email = get_comment_author_email($comment->comment_ID);
        $has_gravatar = sdb_validate_gravatar($comment_email);
        $gravatar = get_avatar_url($comment_email, array('size' => 64));
        // Get initials of the current comment author.
        $initials = strtoupper(substr(get_comment_author($comment->comment_ID), 0, 2));
        // Check if Gravatar is available, otherwise, use a default image.
        $profile_data[] = array(
            'gravatar' => $has_gravatar ? $gravatar : '',
            'initials' => $initials,
        );
    }
    return $profile_data;
}
?>
