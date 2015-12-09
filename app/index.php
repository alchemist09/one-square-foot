<?php include_once("../lib/init.php"); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php
	//echo var_dump($_SESSION);
?>
<?php include_layout_template("admin_header.php"); ?>

    <div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("add_prop" => "New Property", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    
    <?php 
        /*echo "Session Object: ";
        echo var_dump($session).'<br />';*/
        $mesg = $session->message();
        //var_dump($mesg);
        if(!empty($mesg)) { echo output_message($mesg); }
        /*echo '<br />';
        echo "\$_SESSION";
        echo var_dump($_SESSION);*/
    ?>
    
    </div> <!-- main-content -->
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>      