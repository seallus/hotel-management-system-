<?php
require_once 'includes/functions.php';

session_unset();
session_destroy();

session_start();
set_flash('success', 'You have been logged out successfully.');
redirect('login.php');
