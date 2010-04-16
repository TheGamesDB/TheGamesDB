<style>
 #todo LI {
   margin-bottom: 5px;
 }
</style>

<div class="section">
<b>I am Scott and Paul's giant to-do list:</b>
<ul id="todo">
<li>Change "sort" in interfaces to recognize articles in whatever language the results use. Example 'el, la, los, las, nos'.
<li>Fix tv.com importers.
<li>Set up mirrors. (Testing in-progress) 
<li>Add creative commons license information to the site.  Link in footer as privacy and legal info.
<li>Add legal agreement that states that users are responsible for the content they upload, verify it's not copyrighted by someone else, and agree to the creative commons terms.
<li>Allow users to select their preferred banner for each series and season.  If user identifier is passed as a param to the interfaces, return only their preferred banner if it exists.
<li>Allow user identifier as a parameter for the getseries, getepisode, updateseries, and updateepisode interfaces.  When passed (if they have an option selected in their account settings), interfaces will update their favorite shows list with the series that are updated.  It will also mark each episode as being "possessed".
<li>Provide interface for "missing episodes" information.  For any seasons that they already have episodes from, it will get a list of any shows they don't have.
<li>User ratings.  Allow interface for plugins to send rating data for series and episodes.
</ul>
</div>
