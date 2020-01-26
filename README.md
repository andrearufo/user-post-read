# User Post Read

Wordpress plugin to register which logged user has read a determinate post (article, page or custom post).

The plugin create a database table (called `upr_data`) where it log the user id and the post id when an user open a post. So, you canknow if an user has read or not that post.

## Install

Download the latest stable release from the repository; install it like every others plugin inside the Wordpress. No options or configuration required.

## How to use it

You can use the helper function `upr_has_read($post_id, $user_id = null)` to know if the user has read the post inside the php code of your theme.

### Next steps

- Shotcode to use inside the editor
- More helpers functions
