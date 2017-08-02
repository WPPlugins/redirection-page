<?php
/*
Plugin Name: Redirection Page
Version: 1.1
Plugin URI: http://www.yusuf.asia/go/p4-homepage/
Description: Redirect your specified pages, it is usefull when you have 404/not-found pages. Go to <a href="options-general.php?page=redirection-page">Settings Page</a> to start redirection.
Author: Yusuf
Author URI: http://www.yusuf.asia/
*/

function redirection_page() {
	$pages = get_option('redirection_pages');
	$active_page = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if (!empty($pages)) {
		foreach ($pages as $source=>$redir) {
			if ($active_page == $source){
				header('location:http://'.$redir);
				die();
			}
		}
	}
}
add_action('init','redirection_page');

function redirection_page_admin(){
	if (!empty($_GET['rp_action'])) {
		if($_GET['rp_action'] == 'add'){
			if (!empty ($_POST['source']) && !empty ($_POST['redir'])) {
				if (!in_array($_POST['source'], get_option('redirection_pages_source'))) {
					$source = get_option('redirection_pages_source');
					array_push($source, $_POST['source']);
					update_option('redirection_pages_source', $source);
					
					$redir = get_option('redirection_pages_redir');
					array_push($redir, $_POST['redir']);
					update_option('redirection_pages_redir', $redir);
					
					$redirection = array_combine(get_option('redirection_pages_source'), get_option('redirection_pages_redir'));
					update_option('redirection_pages',$redirection);
					echo '<div class="updated"><p>Saved.</p></div>';
				} else {
					echo '<div class="updated"><p><strong>'.$_POST['redir'].'</strong> is already found on database.</p></div>';
				}
			} else {
				echo '<div class="updated"><p>Redirection should not be empty.</p></div>';
			}
		}
		if ($_GET['rp_action'] == 'delete'){
			$source = get_option('redirection_pages_source');
			$redir = get_option('redirection_pages_redir');
			array_splice ($source, $_POST['source'], 1);
			array_splice ($redir, $_POST['redir'], 1);
			update_option('redirection_pages_source', $source);
			update_option('redirection_pages_redir', $redir);
			$source = get_option('redirection_pages_source');
			if (!empty($source)) {
				$redirection = array_combine(get_option('redirection_pages_source'), get_option('redirection_pages_redir'));
				update_option('redirection_pages',$redirection);
			} else {
				$arr = array();
				update_option('redirection_pages',$arr);
			}
			echo '<div class="updated"><p><strong>'.$_GET['source'].'</strong> deleted.</p></div>';
		}
	}
	echo '
	<div class="wrap" id="wpmd_div"><h2>Redirection Page</h2>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
					<div id="wpmd_about" class="postbox">
						<h3 class="hndle"><span>About this Plugin:</span></h3>
							<div class="inside">
								<p><a href="http://www.yusuf.asia/go/p4-home/">Plugin Homepage</a></p>
								<p><a href="http://www.yusuf.asia/go/p4-support/">Support Forum</a></p>
								<p><a href="http://www.yusuf.asia/">Author</a></p>
							</div>
					</div>
				</div>
			</div>
			<div class="has-sidebar sm-padded" >
				<div id="post-body-content" class="has-sidebar-content">
					<div class="meta-box-sortabless">
						<div id="wpmd_satu" class="postbox">
							<h3 class="hndle"><span>Redirection</span></h3>
								<div class="inside">
									<ul><li>
									<br />
									<form method="post" action="options-general.php?page=redirection-page&rp_action=add">
									http://<input type="text" class="regular-text" name="source" value="">
									<span class="description">(your source page)</span>
									<br />
									<br />
									http://<input type="text" class="regular-text" name="redir" value="">
									<span class="description">(destination page)</span>
									<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="submit" name="submit" class="button" value="Add Redirection">
									</form>
									
									</li></ul>
								</div>
						</div>
						<div id="wpmd_dua" class="postbox">
							<h3 class="hndle"><span>Redirection list</span></h3>
								<div class="inside">
									<ul><li>';
									$pages = get_option('redirection_pages');
									if (!empty ($pages)) {
										foreach ($pages as $source=>$redir) {
											echo '<p>http://'.$source.'</p><p><strong>Redirect to :</strong></p><p>http://'.$redir.'</p>';
											echo '<a href="options-general.php?page=redirection-page&rp_action=delete&source='.$source.'&redir='.$redir.'">delete</a>';
											echo '<hr />';
										}
									} else 
										echo 'There is no Redirection.';
									echo '</li></ul>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	';

} 
function redirection_page_admin_menu() {
	add_options_page('Redirection Setting Page','Redirection Page','manage_options','redirection-page','redirection_page_admin');
}
add_action('admin_menu', 'redirection_page_admin_menu');

function redirection_page_active(){
	$pages = array();
	update_option('redirection_pages',$pages);
	update_option('redirection_pages_source',$pages);
	update_option('redirection_pages_redir',$pages);
}

function redirection_page_deactive(){
	delete_option('redirection_pages');
	delete_option('redirection_pages_source');
	delete_option('redirection_pages_redir');
}

register_activation_hook( __FILE__, 'redirection_page_active' );
register_deactivation_hook(__FILE__, 'redirection_page_deactive'); 

?>