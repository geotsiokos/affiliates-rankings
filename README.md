# Affiliates Rankings
<p>Affiliates Rankings is a ranking system for promoting affiliates based on their performance.<br /> 
Each rank is represented by a group and with <a href="http://docs.itthinx.com/document/affiliates-pro/rates/" target="_blank">Rates</a> we can offer different commissions per group.<br />
Each time an affiliate achieves the performance condition of the higher Rank, <br />
is automatically promoted to that next Rank.
The higher the rank, the better is the performance reward for the affiliate.</p>
<p>The plugin is compatible with Affiliates Pro and Affiliates Enterprise plugins by <a href="https://www.itthinx.com/" target="_blank">itthinx.com</a>. </p>

## Requirements
<ul>
<li><a href="https://www.itthinx.com/shop/affiliates-pro/" target="_blank">Affiliates Pro</a> or <a href="https://www.itthinx.com/shop/affiliates-enterprise/" target="_blank">Affiliates Enterprise</a> should be installed, version >= 3.0.0.</li>
  <li>Commission method in <i>Affiliates > Settings</i>, <i>Commissions</i> tab should be <strong>Rates</strong>.</li>
<li><a href="https://https://wordpress.org/plugins/groups/" target="_blank">Groups</a> plugin, a free plugin for managing access restrictions by <a href="https://www.itthinx.com/" target="_blank">itthinx.com</a>.</li>
</ul>

## Usage
<p>Install and activate the plugin. </p>
<p>By default after activation, 6 new groups will be added: </p>
 <p> <quote>Rank 1, Rank 2, Rank 3, Rank 4, Rank 5, Rank 6</quote></p>
<p>and 6 conditions:</p>
<p><quote>10, 20, 30, 40, 50, 60</quote></p>

<p>Each time an affiliate registers throught the affiliates registration form, he will be added automatically to Rank 1. In order to be promoted to the next level, Rank 2, the affiliate must have earned at least 20 referrals and so on....</p>

## API
<p>You can change the group names and promotion conditions by using the following filters:</p>
<p><strong>Example</strong></p>
<p>If you want to use 4 groups named <i>Class 1, Class 2, Class 3, Class 4</i><br />
with conditions <i>5, 10, 15, 20</i> respectively, <br />
you should add the following snippets in the <i>functions.php</i> file of your active theme.</p>

<pre>add_filter( 'affiliates_ranking_groups_names', 'example_affiliates_ranking_groups_names' );
function example_affiliates_ranking_groups_names( $names ) {
	$names = array( 'Class 1', 'Class 2', 'Class 3', 'Class 4' );
	return $names;
}</pre>
<pre>
add_filter( 'affiliates_ranking_conditions', 'example_affiliates_ranking_conditions' );
function example_affiliates_ranking_conditions( $conditions ) {
	$conditions = array( 5, 10, 15, 20 );
	return $conditions;
}</pre>
