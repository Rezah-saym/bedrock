<?php

namespace Rezah\Modules\ApiWordpress; 


class Api_Wordpress{

     function __construct(){
		new Api_Actions;
		new Api_Filters;

     }

}