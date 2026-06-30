<?
class htmlclass 
{
	//var $label;
	//var $tag;
	function put_select($label,$name,$options)
	{
		return"
			<div class=\"row\">
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
				<div class=\"col-75\">
					<select id=\"$name\" name=\"$name\">
						$options
					</select>
				</div>
			</div>
		";
	}
	function put_text($label,$place,$name,$max_size,$others)
	{
		return "
			<div class=\"row\">
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
				<div class=\"col-75\">
					<input type=\"text\" id=\"$name\" name=\"$name\" placeholder=\"$place\" maxlenght=\"$max_size\" $others>
				</div>
			</div>
		";
	}
	function put_switch($label,$name,$chec)
	{
		return"
			<div class=\"row\">
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
				<div class=\"col-75\">
					<label class=\"switch\">
						<input type=\"checkbox\" name=\"$name\" $chec>
						<span class=\"slider round\"></span>
					</label>
				</div>
			</div>
		";
	}
	function put_number($label,$place,$name,$max_size,$others)
	{
		return "
			<div class=\"row\">
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
				<div class=\"col-75\">
					<input type=\"number\" id=\"$name\" name=\"$name\" placeholder=\"$place\" maxlenght=\"$max_size\" $others>
				</div>
			</div>
		";
	}
	function put_submit($title)
	{
		return"
			<div class=\"row\">
				<input type=\"submit\" value=\"$title\">
			</div>
		";
	}
	function put_button($title)
	{
		return"
			<div class=\"row\">
				<input type=\"submit\" value=\"$title\">
			</div>
		";
	}
}
?>
