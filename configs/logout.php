<?php
session_start();
session_destroy();
header("Location: ../authorization.php");
exit();
?> 