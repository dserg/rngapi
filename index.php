<?php

namespace RngAPI;

include "src/RngAPI/DBAL.php";
include "src/RngAPI/Helper.php";
include "src/RngAPI/Handler.php";
include "src/RngAPI/AuthHeadersProcessor.php";
include "src/RngAPI/ExceptionsHandler.php";

Handler::route();