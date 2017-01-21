<?php

class Route {

	public static function redirect($route_path) {
		header("Location: http://community.dur.ac.uk/cs.seg04/password/conCEPt/conCEPt/public/?" . $route_path);
	}


}
