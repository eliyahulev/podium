<?php
/**
 * `*
 * Let Editors manage users
 *
 * @package podium
 */

function podium_editor_manage_users()
{

    // let editor manage users

    $edit_editor = get_role('editor');
// Get the user role

    // Edit theme
    $edit_editor->add_cap('edit_theme_options');

    // let editor manage users
    $edit_editor->add_cap('edit_users');
    $edit_editor->add_cap('create_users');
    $edit_editor->add_cap('add_users');
    $edit_editor->add_cap('list_users');

}

add_action('init', 'podium_editor_manage_users');

// Hide all administrators from user list.

/**
 * @param $user_search
 */
function podium_pre_user_query($user_search)
{

    $user = wp_get_current_user();

    if (!current_user_can('manage_options')) {

        global $wpdb;

        $user_search->query_where =
            str_replace('WHERE 1=1',
            "WHERE 1=1 AND {$wpdb->users}.ID IN (
                 SELECT {$wpdb->usermeta}.user_id FROM $wpdb->usermeta
                    WHERE {$wpdb->usermeta}.meta_key = '{$wpdb->prefix}capabilities'
                    AND {$wpdb->usermeta}.meta_value NOT LIKE '%administrator%')",
            $user_search->query_where
        );

    }

}

add_action('pre_user_query', 'podium_pre_user_query');

// Remove 'Administrator' from the list of roles if the current user is not an admin
/**
 * @param  $roles
 * @return mixed
 */
function podium_editable_roles($roles)
{

    if (isset($roles['administrator']) && !current_user_can('administrator')) {
        unset($roles['administrator']);
    }

    return $roles;
}

add_filter('editable_roles', 'podium_editable_roles');


// Hide slug changing options from editors
function hide_slug_box()
{
    global $post;
    global $pagenow;

    if (is_admin() && ('post-new.php' == $pagenow || 'post.php' == $pagenow)) {
        echo '<style>
        #edit-slug-buttons,
        #slugdiv{
            display: none;
        }
        </style>';
    }

}
if (!current_user_can('manage_options')) {
    add_action('admin_head', 'hide_slug_box');
}
