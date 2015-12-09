      <div id="footer">
    
      </div> <!-- footer -->
</div> <!-- wrapper -->
<?php
if($session->isLoggedIn()) { echo $session->message(); }
echo "<br />";
echo $session->sessionVar("user");
?>
</body>
</html>