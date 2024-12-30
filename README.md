Email verification mail:
    $user->sendEmailVerificationNotification() is used to send the email to the user after registration.
    the sendEmailVerificationNotification() is overridden in the User class to send a custom Notification through php artisan make:notification CustomNotification
    after the has value is compared and if correct, it redirects the user to the react app url 


Notification steps:
    1. after the post/comment is upvoted, relevant values to notifications table are inserted, updateNotification event is called
    2. to start the reverb server: 
                            php artisan reverb:start
            to broadcast an event event/broadcast(new Event($someData));
                event triggers the listeners whreas broadcast helper does not trigger any listeners


1. Broadcasting using Reverb
    Medium blog: https://medium.com/@noor1yasser9/complete-guide-for-setting-up-reverb-with-laravel-11-broadcasting-2e62e14c05ed

    steps:
    1. first create an event, implement ShouldBrodcast  contract and return Channel instnace on the broadcastOn() method 
    2. create Channel on the routes/channels.php file.
        Channels are classes that CLIENTS SUBSCRIBE TO from the frontend to listen for events
        can use private(authenticated channels), public and presense channel(used to track user typing while sending message, check online status)


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
    # What Happens with Auth::check()?
        Session-based Authentication: If Auth::check() is called without specifying a guard, it will use the default guard (web for session-based) to check if a user is logged in through a session.
    # Token-based Authentication: 
        If you want to check if a user is logged in with a token, you can specify the sanctum guard (or whichever token-based guard is configured) by calling Auth::guard('sanctum')->check(). This will validate the token instead of checking the session.

    Summary
    Auth::check() by default checks for session-based authentication.
    Auth::guard('sanctum')->check() checks for token-based authentication, validating the access token.
    Guards allow Laravel to manage different authentication methods in one app, handling both session and token-based logins.

    Apply middleware to constructor:
        read from here
        https://medium.com/@harrisrafto/advanced-controller-middleware-in-laravel-using-the-hasmiddleware-interface-31f4fbdb7288




    # Check for authoraization. only let the user delete their personal comments/posts
     
        can use either authoraization, gater, or middleware   
        paste the destroy function from CommentController on chatgpt and ask what other ways can it be done, besides doing it from within the controler.
        ask to get it done through GATES, MIDDLEWARES AND POLICIES


    # Laravel Guard:

        Gate facade
        Gate::define('delete-post', function(User $user, Post $post){
            if(!$post->user()->is(Auth::user())){return false;}
        })

        No need to send user because Laravle automatically injects the dependency injection through its methods 
        in controller:
            Gate::authorize('delete-post', $post);      -> will automatically abort and send 403

            if(Gate::allows('delete-post', $post)){     -> will not abort but instead only send boolean return type
                // do other operations here
            }

            do this only after you provide your gates on AppServiceProvider

    # Define Gates inside AppServiceProvider:
        
        create insdie Providers/AppServiceProvider

        paste the logic insdie boot() function 

        what i mean is only the function user() is defined in the Comment model class, but in the gate, object of the user is being called and not user() function, why is that? :
        ans: Ah, I see what you’re asking! This is an interesting feature of Laravel's Eloquent ORM: dynamic properties. When you define a relationship method like user() in a model, Laravel automatically makes it accessible as a property (e.g., $comment->user) instead of requiring it to be called as a method (e.g., $comment->user()).




    # Policies

       





             
    things to do:
    API:
    1. post -> pictures remaining
    2. comments -> almost all done
    3. tags -> 
    4. 