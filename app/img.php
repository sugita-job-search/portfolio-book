<?php
require_once('./config.php');

header('Content-type:'. $_SESSION[Session::UNCONFIRMED][Common::URL_BOOK_REGISTRATION][Session::FILE_TYPE]);
echo $_SESSION[Session::UNCONFIRMED][Common::URL_BOOK_REGISTRATION][Session::FILE];