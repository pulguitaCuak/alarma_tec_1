<?php
                    //no me toquen lo que anda manga de gatos

session_start();
session_unset();
session_destroy();
header("Location: ../front-end/login.html");
exit;
?>
