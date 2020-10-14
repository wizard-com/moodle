Change Log in version 3.0.2 (2020073103)
========================================
1. Fix 'Error in function quiz_num_submissions_ungraded' - #176.
2. Fix 'course_participant_count inaccurate' - #179.
3. Fix 'Lesson status inaccurate' - #180.

Change Log in version 3.0.1 (2020073102)
========================================
1. Fix 'Too few arguments to function theme_adaptable_core_renderer::render_mycourses(),
   0 passed in [dirroot]/lib/outputrenderers.php on line 497 and exactly 1 expected' - #172.
2. Fix navbar is not showing on the frontpage.
3. Fix 'Book module has two icons' - #174.
4. Fix 'Course in category' - https://moodle.org/mod/forum/discuss.php?d=408081#p1656297.
5. Fix 'Ungraded assign' - https://moodle.org/mod/forum/discuss.php?d=410681.
6. Fix btn-secondary text colour when link.
7. Fix 'Filter not applied' - https://moodle.org/mod/forum/discuss.php?d=408081#p1657138.
8. Fix 'Support for Embedded Questions filter' - #177.

Change Log in version 3.0.0 (2020073101)
========================================

Release candidate for Moodle 3.9.

1. Fix licence from GPLv2 to GPLv3 as is incorrect - Moodle plugins must be GPLv3.
2. Fix message drawer closure.
3. Fix 'Regression - Frontpage marketing blocks don't display on desktop' - #139.
4. Moodle 3.9 New Activity Chooser styling needs work - #131.
5. Fix 'Blocks - My Home recently accessed course' - #9.
6. Fix rubic icons -> https://moodle.org/mod/forum/discuss.php?d=408081#p1646693.
7. Fix 'Searchbox conflict with Advanced Forum (hsuforum)' - #133.
8. Fix 'Bullet list display in Collapsed Topics course format' - #81.
9. Fix 'Block settings are left justified' - #82.
10. Improve 'Improve Onetopic course format tab rendering' - #115.
11. Fix 'Missing action menu (like editing button / cog button) on content bank page' - #140.
12. Fix 'Button/link 'Turn editing on' missing on Moodle 3.9' - #129.
13. Fix '.btn declaired after .hidden' - #130.
14. Fix message drawer hover.
15. Improve position of #82.
16. Make 'side-post' have no padding on the right so that the page is symmetrical.
17. Fix 'H5P iframe element too small in content bank page' - #146.
18. Fix 'Navbar Custom Menu does not fit' - #128.
19. Fix 'Editing cog colour not consistent' - #149.
20. Fix 'Whitespace Below Header in Course Pages' - #38.
21. Fix 'Use of "$setting->set_updatedcallback('theme_reset_all_caches');" not needed on some settings' - #25.
22. Fix 'lib.php preg_match logic flaw' - #150.
23. Fix 'wrong rtl css' - #142.
24. Fix 'Impossible to enter a course with Coventry tiles' - #156.
25. Update to version 3 https://moodlehq.github.io/moodle-plugin-ci/UPGRADE-3.0.html - #158.
26. Assignment with restricting grouping shows all users or groups - #161.
27. Fix 'Regression? / Inconsistent cog positioning in content bank' - #166.
28. Fix 'Quiz attempt: no breadcrumbs' - #123.
29. Fix '$fontname can never be 'custom'' - #104.
30. Implement 'Update google fonts list ' - #42 - thanks to 'Sal Zaydon' - https://gitlab.com/szaydon for the list.
31. Fix no such font-family as 'default' - #42.
32. Fix 'Topic header text now black' - #167.

Change Log in version 2.2.2 (2019112601)
========================================

Main fixes & Enhancements done in this release.

- Fix mobile responsive settings in "layout responsive" settings page
- Fix ability to set general box color in forums
- Fix issues with login page when no header in use
- Fix issue of footer riding up on short pages with little content
- Fix close icon for activity chooser in Moodle 3.8
- Fix combo list on mobile, now collapses into single column

What's new?

- Layout responsive settings page
- Setting to control color of forum "general box" background where forum description is displayed


HTML/CSS sample code for block areas
------------------------------------
Here you will find some code samples to help you to customize the Info Box and the Marketing Blocks.

You can insert any HTML tag to customize the front page blocks. Use a <div> tag as a main container and add the height to keep the
same value in all the blocks.

The Font Awesome icons set is available in
http://fortawesome.github.io/Font-Awesome/icons/.

You can insert any of them following the examples
http://fortawesome.github.io/Font-Awesome/examples/


Front Page Slider Styles
------------------------
Add images with at least 1900px x 400px.
If you want to reduce or increase the height, Adaptable will resize the image automatically.
There are two possible slider styles each with different markup required:


Original BCU Slider Markup:

<div class="span9">
  <h4>Information</h4>
    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
    et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
  </div>

  <div class="span3">
  <a href="#" class="submit">2016/17 Courses <i class="fa-chevron-right fa"></i></a>
</div>


Coventry Style Slider Markup

<div class="span6 col-sm-6">
<h3>Hand-crafted</h3> <h4>pixels and code for the Moodle community</h4>
<a href="#" class="submit">Check out our custom theme pricing!</a>
</div>


Frontpage Marketing Block HTML structure Coventry

<div><img src="http://somewebsite.com/2.jpg" class="marketimage"></div>
<h4><a href="#">International Courses</a></h4>
<p>Some text below the link....</p>


Front page Info Box and Marketing Blocks
----------------------------------------

There are two Info blocks in the front page located above and below the Marketing Blocks. These are just for compatibility with the
old BCU.

It is recommended to use the new marketing blocks builder that allows you to create your own layout and add much more blocks.

There are 8 rows where you can add up to 4 blocks in each with a total of 32 block of different size. See pix/layout.png for
more information.

You can enter any HTML code to the block, include FA icons, images, videos and apply in-line styles.

Some samples:


Block with solid background, FA icon and some text:

<div style="text-align:center; background: #e6e6e6; height: 350px; padding: 7px;">
    <i class="fa fa-paint-brush fa-5x" style="color: #3A454b;"></i>
    <h3>Title </h3>
    <div style="text-align:center;">Add your text here.</div>
</div>


Block with border and transparent background:

<div style="text-align:center; height: 350px; padding: 7px; border: 1px solid #3A454b;">
    <i class="fa fa-list fa-5x" style="color: #3A454b;"></i>
    <h3>Heading</h3>
    <div style="text-align:center; padding: 5px; color: #3A454b;">Add your text here.</div>
</div>


Block with an image:

<div style="height: 350px;">
    <img src="http://yoursite/yourimage.jpg" style="vertical-align:text-bottom; margin: 0 .5em;" height="auto" width="100%">
    <p style="margin-top: 5px; color: #333333; text-align: center;"><strong>Add your text here</strong></p>
</div>


Block with a video:

<div style="background: #606060; height: 350px">
    <center>
    <iframe src="https://www.youtube.com/embed/wop3FMhoLGs" allowfullscreen="" frameborder="0" height="315" width="560"></iframe>
    </center>
</div>


Block using multi-lang filter:

<div style="width: 100%; height: 240px; background-color: #cccccc;">
<h1 style="text-align: center; line-height: 120px;">
      <span class="multilang" lang="en">text in english</span>
      <span class="multilang" lang="es">texto en espaÃƒÂ±ol</span>
      <span class="multilang" lang="fr">texte en franÃƒÂ§ais</span>
      <span class="multilang" lang="ca">text en catalÃƒ </span>
</div>


Footer Blocks
-------------
You can apply the same HTML/CSS in the footer blocks.

Some samples:

Contact information

<i class="fa fa-building"></i>  High St. 100<br>
<span style="margin-left: 20px;">123456 City</span><br><br>
<i class="fa fa-phone"></i> +12 (3)456 78 90<br>
<i class="fa fa-envelope"></i> info@mail.com<br>
<i class="fa fa-globe"></i> www.example.com


List with Chevron

<ul class="block-list">
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>Accessibility</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>Moodle Help</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>Moodle Feedback</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>IT Help</span></a></li>
    <li><a href="http://moodle.org/"><span class="fa fa-chevron-right"></span><span>IT Feedback</span></a></li>
</ul>


Copyright text
--------------
A sample of copyright text using FA icon

Made with <i class="fa fa-heart" style="color: #ff0000;"></i> in Europe


News Ticker
-----------
From version 1.3 the news ticker do not need to create an unordered list. Just add paragraphs using <p> tags:

<p>Configure all the theme colors</p>
<p>Use any Google Font for the content, headings and site title</p>
<p>Display a logo or a configurable title site</p>
<p>Configurable Slideshow</p>
<p>Display up to 12 marketing blocks in the front page</p>


Messages / Notifications
------------------------
Moodle 3.2 includes a new system to display messages and notifications in the screen.

The new system displays a hard coded black icons that are difficult to see when using dark background color in the top header.
In that case, you can use an alternate icons pack using white color.

Login the server by FTP or SFTP and open /theme/adaptable/pix_core/i and
delete notifications.png and rename notifications-white.png to notifications.png

Then open /theme/adaptable/pix_core/t and delete message.png and
rename message-white.png to message.png

From moodle 3.6 the messages and notifications has been changed to the called "Messages Drawer".


Activities icons
------------------------
From version 1.4, Adaptable includes its own icons pack that replace the default moodle icons.
If you don't want to use the icons just remove adaptable/pix_plugins and adaptable/pix_core/f
You can enable this icons from the administration.