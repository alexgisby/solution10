# Solution10\Auth

## What is this?

Auth is a combined Authentication and Authorization package.

### Key features:

* Can run multiple Auth instances side by side (ie Customer login being different to Admin Panel login).
* Powerful Permissions. Define packages with rules and callbacks, and override on a per-user level.
* Storage Agnostic. Use of delegates means you can use any storage backend, MySQL, JSON, Memcached, whatever.
* Uses PHPass for secure password hashing.


## Basic Usage

    $auth = new Solution10\Auth('default',
                                new Solution10\Auth\Session\Default,
                                new Solution10\Auth\PDO,
                                array(
                                    'phpass_cost' => 8,
                                ));

    $auth->login('alex@solution10.com', 'MyPasswordIs1337');

    if($auth->logged_in())
    {
        if($auth->can('post'))
        {
            echo 'Hi, you can post!';
        }
        else
        {
            echo 'You shall not post!';
        }
    }
    else
    {
        echo 'Not logged in.';
    }

## Requirements

* Solution10\Collection
