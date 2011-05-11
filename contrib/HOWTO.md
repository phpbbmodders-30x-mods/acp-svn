This HOWTO assumes that you have a working knowledge of SVN. It also assumes that your server runs *nix.

1. First create a SVN repo. I usually do not use the default directories at all ("trunk", "tags" and "branches") when the repo is only for a forum or website. But this howto is easy to adapt if you want to use them. Or if you want to run your forum from a sub-directory in the repo having for example a stable (site) and a development branch to work on.

2. Create a user that only has read access to the repo, this is for the forum to use. Make sure to give it a name you recognize.

3. Checkout the repo to you local computer, preferably to a place accessible with your local web server so you can test your changes before committing them.

4. Copy all forum files to your local repo. Don't add them yet, only copy them. Don't forget the files contained in this MOD, and the file edits if you choose to do them. The only file edit that is necessary is the one in config.php, but you can't do that through SVN.

5. Set cache/ files/ avatars/upload/ store/ and config.php to be ignored. You don't want your local test cache or files to be uploaded to the server. config.php is most likely not identical, and if there are more than one developer messing with the forum files you can be sure their config files are not identical.

6. If you have any other files that needs to be different locally from them on the server, set them to be ignored too.

7. Add all not ignored files and directories to be version controlled.

8. Commit everything to the repo.

9. Login to your live server. Either log in as root or run su to become root. If you can't do that you will have to use sudo for some things here.

10. cd to your forums parent directory (/var/www/ or /home/you/htdocs/) or something like that. You will need write permissions to this directory. Make sure there doesn't already exist a directory with the same name as the repo.

11. Run "svn checkout --username THE_FORUM_USER --password THE_FORUMS_PASS https://url/and/path/to/the/repo". By using the forums username and password you make sure that they work.

12. The ignored directories and files does not exist in the repo so you will need to create them.

	cd your_forum_dir
	mkdir cache files store images/avatars/upload

13. You need to create a config.php. If you already had a working forum you can copy that file and add the edits for this MOD using nano or the favorite text editor you have installed on your server (I personally prefer jed). If you don't have a working forum then you can open your local copy of config.php and copy the contents. If you are to install a new forum, then you just leave a empty config.php and phpBB will do the rest for you. After install you need to add the svn credentials to config.php to be able to update the forum through SVN.

14. You need to make the web server the owner of all files and directories, the web server should also be the only one allowed to write to them. You might need sudo to do these steps. Also change the name and group of the web server if they are not www-data

		chown -R www-data:www-data ./*

	These two makes the files and directories only accessible by the web server.

		find ./ -type d -exec chmod 700 '{}' \;
		find ./ -type f -exec chmod 600 '{}' \;

15. Now make sure nobody by accident commit and overwrite config.php

		chmod 400 config.php

16. Go up to the parent and make sure the forum directory is also only accessible by the web server. Change phpBB3 to the actual name.

		cd ..
		chown www-data:www-data phpBB3
		chmod 700 phpBB3

17. From now on all edits and other file changes to your forum should be done using the SVN repo.

18. You should now be able to access your forum with your browser. Go to http://yourdomain/phpBB3/install_svn.php. Remember to delete that file when done (using SVN).
You should have a new permission "Can manage SVN" that you can give groups or users. You can find "Manage SVN" in ACP under the system tab.

19. Enjoy
