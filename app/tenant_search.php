<?php require_once('../lib/init.php'); ?>
<?php if(!$session->isLoggedIn()) { redirect_to('../index.php'); } ?>
<?php
	if(isset($_GET['submit'])){
		$name = $_GET['tenant'];
		if(empty($name) || $name == "Enter tenant name...."){
			$err = "Enter name of tenant to search for";	
		} else {
			$tenants = Tenant::searchTenant($name);
		}
	}
?>
<?php include_layout_template('admin_header.php'); ?>

	<div id="container">
    <h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenants" => "Tenants", "tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
	<div id="main-content">
    
    <h2>Search Results</h2>
    
    <div id="tenant-search">
    <fieldset>
    <legend>Find Tenant</legend>
    <form action="tenant_search.php" method="get">
    <table cellpadding="5">
    <tr>
    	<td><label for="tenant">Enter Tenant Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="tenant" id="tenant" /></td>
        <td><input type="submit" name="submit" value="Search" /></td>
    </tr>
    </table>
    </form>  
    </fieldset>       
    </div>
    
    <?php if(!empty($err)) { echo display_error($err); } ?>
    
    <?php if(!empty($tenants)){ ?>
    <?php foreach($tenants as $tnt): ?>
    <p><a href="tenant.php?tid=<?php echo $tnt->id; ?>"><?php echo $tnt->getFullName(); ?></a>&nbsp;&nbsp;<?php echo "Room No: ".Room::findByTenantId($tnt->id)->getRoomLabel(); ?></p>
    <?php endforeach; ?>
    <?php } elseif(empty($tenants) && !empty($name)) { echo "<p>Search returned no results</p>"; }  ?>
    
    </div>

<?php include_layout_template('admin_footer.php'); ?>