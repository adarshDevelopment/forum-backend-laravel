1. Database

1. user     -> done
2. posts (id, title, content, picture_exists(bool), likes, tag_id ) -> done
3. pictures (id, picture_id, post_id, )     
4. commens (id, comment, user_id, likes, )      
5. tags(id, title, created_by, updated_by )     -> done


php artisan make:model Post -mcrfs
Model: Post.php
Migration: xxxx_xx_xx_xxxxxx_create_posts_table.php
Controller: PostController.php
Factory: PostFactory.php
Seeder: PostSeeder.php
Request: (You'll still need to create the request separately, as it's not included in this command.)

php artisan make:model YourModelName -mcrfs
Explanation of Options:
-m: Create a migration file.
-c: Create a controller.
-r: Make the controller a resource controller.
-f: Create a factory.
-s: Create a seeder.



Laravel Authnetication
    What Happens with Auth::check()?
        Session-based Authentication: If Auth::check() is called without specifying a guard, it will use the default guard (web for session-based) to check if a user is logged in through a session.
    Token-based Authentication: 
        If you want to check if a user is logged in with a token, you can specify the sanctum guard (or whichever token-based guard is configured) by calling Auth::guard('sanctum')->check(). This will validate the token instead of checking the session.

    Summary
    Auth::check() by default checks for session-based authentication.
    Auth::guard('sanctum')->check() checks for token-based authentication, validating the access token.
    Guards allow Laravel to manage different authentication methods in one app, handling both session and token-based logins.