<?

function txno($tx, $no = "-"){
	return $tx . "." . $no;
}

function format_value($value){
	return sprintf("%.8f", $value);
}
