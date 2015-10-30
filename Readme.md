Installation:
Add this to "providers" array in config/app.hp
	'asp\commenter\CommenterServiceProvider',
And this to "aliases" array in config/app.php
 	'Commenter'     => 'asp\commenter\Helpers\Commenter',
Run "Composer update"
Run "php artisan vendor:publish"
Set the api key in config/CommenterConfig.php
Usage:
	You must have a username set in session by the key 'user' to enable commenting and rplying
	Just add following return a 'comments' variable obtained as shown below:
		$view= \Commenter::loadComment();
        return \View::make('welcome')->with('comments',$view);
	Then in your view, insert the following snippet wherever you want to render the comments:
		@if(isset($comments))
            <?php echo $comments;?>
        @endif

