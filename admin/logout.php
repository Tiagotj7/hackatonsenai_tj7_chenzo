<?php
require_once __DIR__ . '/../config/helpers.php';
session_destroy();
session_start();
flash('info', 'Você saiu do sistema.');
redirect('interface.php');