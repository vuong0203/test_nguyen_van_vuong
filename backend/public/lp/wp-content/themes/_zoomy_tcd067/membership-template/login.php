<?php
global $dp_options, $tcd_membership_vars;

get_header();
?>
<main class="l-main has-bg--pc">
	<div class="l-inner">
		<div class="p-member-page p-login">
<?php
tcd_membership_login_form();
?>
		</div>
	</div>
</main>
<?php
get_footer();
