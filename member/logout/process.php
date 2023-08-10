<?php
require_once('../../app/config.php');

unset($_SESSION[Session::USER]);
unset($_SESSION[Session::TOKEN]);
Session::deleteFormData();

Common::redirect(Common::URL_LOGIN);