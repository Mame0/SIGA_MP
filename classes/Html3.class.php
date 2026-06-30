<?php
echo '
<link rel="stylesheet prefetch" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet prefetch" href="css/bootstrap-select.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.1/chosen.jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.1/chosen.min.css" />

<style>
    /* Diseño base y reseteo */
    * {
        box-sizing: border-box;
    }
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-color: #f2f2f2;
    }
    /* Contenedor principal del formulario */
    form {
        background-color: #ffffff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        max-width: 800px;
        margin: 20px auto;
    }
    /* Estilo para los títulos de sección */
    font[style="color:silver"] {
        font-size: 1.2em;
        font-weight: bold;
        color: #073A6B !important;
    }
    hr {
        border-top: 1px solid #ccc;
    }
    /* Estilos para inputs, textareas y selects */
    input[type=text], input[type=password], input[type=email], input[type=number], textarea, select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        resize: vertical;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    input:focus, textarea:focus, select:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        outline: none;
    }
    /* Clases de columna responsivas */
    .col-25, .col-75 {
        float: left;
        width: 100%;
        margin-top: 6px;
    }
    .col-25 {
        padding-right: 20px;
    }
    label {
        padding: 12px 12px 12px 0;
        display: inline-block;
        font-weight: bold;
        color: #333;
    }
    /* Limpiar floats después de las columnas */
    .row:after {
        content: "";
        display: table;
        clear: both;
    }
    /* Botón de envío */
    input[type=submit] {
        background-color: #4CAF50;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1em;
        float: right;
        transition: background-color 0.3s;
    }
    input[type=submit]:hover {
        background-color: #45a049;
    }
    /* Media Query para responsividad */
    @media screen and (min-width: 768px) {
        .col-25 {
            width: 25%;
        }
        .col-75 {
            width: 75%;
        }
    }
</style>
';

class htmlclass
{
	// El resto de la clase permanece sin cambios...
//var $arra_options array;
	//@param array $arra_options;
	//var $tag;
	function put_select_estado($label,$name,$value,$enable,$disable)
	{
		$arra_sele[$value]='selected';
		return"
			<div class=\"row\">
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
				<div class=\"col-75\">
					<select id=\"$name\" name=\"$name\">
						<option value=\"1\" {$arra_sele[1]}>$enable
						<option value=\"0\" {$arra_sele[0]}>$disable
					</select>
				</div>
			</div>
		";
	}
/**
* @param $table
*
* @param array $arra_options
* @return bool
*/
	public function put_select($label,$name,$arra_options,$value,$others,$id_div='',$only_tag='')
	{
		$arra_sele[$value]='selected';
		foreach($arra_options as $cod => $nam)
		{
			if(substr($cod,0,5)=='9999_')
				$options.="<optgroup label=\"$nam\">";
			else
				$options.="<option value=\"$cod\" {$arra_sele[$cod]}>$nam</option>";
		}
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		if($id_div)
			$id_div="id=\"$id_div\"";
		if($only_tag)
		{
			return"
					<select id=\"$name\" name=\"$name\" $others>
						$options
					</select>
			";
		}
		else
		{
			return"
			<div class=\"row\">
				$la
				<div class=\"col-75\">
<p style=\"height:40;margin:0px;\" $id_div>
					<select class=\"chosen\" id=\"$name\" name=\"$name\" $others>
						$options
					</select>
</p>
				</div>
			</div>
			";
		}
	}
	public function put_select_buscador($label,$name,$arra_options,$value,$others,$id_div='',$only_tag='')
	{
		$arra_sele[$value]='selected';
		foreach($arra_options as $cod => $nam)
		{
			if(substr($cod,0,5)=='9999_')
				$options.="<optgroup label=\"$nam\">";
			else
				$options.="<option value=\"$cod\" {$arra_sele[$cod]}>$nam</option>";
		}
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		if($id_div)
			$id_div="id=\"$id_div\"";
		if($only_tag)
		{
			return"
					<select id=\"$name\" name=\"$name\" $others class=\"selectpicker\" data-show-subtext=\"false\" data-live-search=\"true\">
						$options
					</select>
			";
		}
		else
		{
			return"
			<div class=\"row\">
				$la
				<div class=\"col-75\">
<p style=\"height:40;margin:0px;\" $id_div>
					<select id=\"$name\" name=\"$name\" $others class=\"selectpicker\" data-show-subtext=\"false\" data-live-search=\"true\>
						$options
					</select>
</p>
				</div>
			</div>
			";
		}
		echo"<script>var intro = document.getElementById('$name');intro.</script>";
	}
	public function put_others($label,$others)
	{
		return"
			<div class=\"row\">
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
				<div class=\"col-75\">
					$others
				</div>
			</div>
		";
	}
	function put_title($label)
	{
		return "
		<BR><font style=\"color:silver\">$label</font><HR>
		";
	}
	function put_title_demand($label,$content_right='')
	{
		if($content_right)
			$content_right="<td width=1%>$content_right</td>";
		return "
			<div style=\"display:block;column-span:all;\">
				<BR>
				<table border=0 cellpading=2 cellspacing=2 width=100% style=\"border-bottom-width: 1px; border-bottom-color: silver; border-bottom-style: solid;margin: 4px 0px\"><tr>
					<td width=100% style=\"text-align:left\"><font style=\"color:#073A6B\"><b>$label</font></td>
					$content_right
				</tr></table>
			</div>
		";
					//<td width=1%><font style=\"color:silver\"><img src=\"img/plus-square2.svg\" height=\"16px\"></font></td>
					//<td width=1%><font style=\"color:silver\"><img src=\"img/delete2.svg\" height=\"16px\"></font></td>
	}
	function put_title_demand_switch4($label,$name,$chec,$nomb2,$value,$tipo,$place,$min_size,$max_size,$others,$nomb3='')
	{
		if($chec)
			$chec='checked';
		if($name)
			$nom="
					<td>
                                        	<label class=\"switch\">
                                                	<input type=\"checkbox\" name=\"$name\" $chec onclick=\"list_down2(this,'$nomb2','$nomb3')\">
	                                                <span class=\"slider round\"></span>
	                                        </label>
					</td>
			";
		if($label)
			$lab="<td>$label</td>";
		return"
                        <div class=\"row\">
				<table border=0 width=100%>
					<tr height=30px valign=bottom>
					<td>
					</td>
                                        $lab
                                </tr>
				<tr>
					$nom
					<td>
<input type=\"$tipo\" id=\"$nomb2\" name=\"$nomb2\" disabled value=\"$value\" placeholder=\"$place\" minlength=\"$min_size\" maxlength=\"$max_size\" $others>
					</td>
				</tr>
				</table>
                        </div>
                ";
	}
	function put_title_demand_switch2($label,$name,$chec,$nomb_div)
	{
		if($chec)
			$chec='checked';
		return"
                        <div class=\"row\">
				<table border=0><tr>
					<td>
                                        	<label class=\"switch\">
                                                	<input type=\"checkbox\" name=\"$name\" $chec onclick=\"list_down2(this,'$nomb_div')\">
	                                                <span class=\"slider round\"></span>
	                                        </label>
					</td>
                                        <td>$label</td>
                                </tr></table>
                        </div>
                ";
	}
	function put_title_demand_switch3($label,$nomb_div,$nomb_div2)
	{
		return "
			<div style=\"display:block;column-span:all;\">
				<BR><div style=\"display:-webkit-inline-box;\"><font style=\"color:silver;\">$label</font></div>
					<a href=\"javascript:list_down('$nomb_div','$nomb_div2')\">
						<div id=\"$nomb_div\" style=\"color:silver;display:-webkit-inline-box;\">
							<img src=\"img/icons/list_down.svg\" height=10px>
						</div>
					</a>
				<HR>
			</div>
		";
	}
	function put_title_demand_switch($label,$nomb_div,$nomb_div2)
	{
		return "
			<div style=\"display:block;column-span:all;\">
				<BR><div style=\"display:-webkit-inline-box;\"><font style=\"color:silver;\">$label</font></div>
					<a href=\"javascript:list_down('$nomb_div','$nomb_div2')\">
						<div id=\"$nomb_div\" style=\"color:silver;display:-webkit-inline-box;\">
							<img src=\"img/icons/list_down.svg\" height=10px>
						</div>
					</a>
				<HR>
			</div>
		";
	}
	function put_iframe_documento($docu)
	{
		return "
			<div style=\"display:block;column-span:all;\">
				<iframe width=\"100%\" height=\"300\" src=\"documentos/docu_001_000001.pdf\" id=\"doc_iframe\" name=\"doc_iframe\" style=\"border:0px solid red;\" allowfullscreen></iframe>
			</div>
		";
	}
	function put_separator_demand($height)
	{
		return "
			<div style=\"display:block;column-span:all;height:$height"."px\">
			</div>
		";
	}
	function put_hidden($name,$value)
	{
		return "
			<input type=hidden name=\"$name\" value=\"$value\">
		";
	}
	function put_info2($label,$value)
	{
		if(!$value)
			$value='-------------------------';
		return "
			<div class=\"row\">
				<div class=\"col-75\"><font color=silver size=2px>$label</font><BR>
					$value
				</div>
			</div>
		";
	}
	function put_info($label,$value)
	{
		if($label)
			$la="
				<table border=0 cellpading=2 cellspacing=2 width=100% style=\"text-align:-webkit-left;border-bottom-width: 1px; border-bottom-color: silver; border-bottom-style: solid;margin: 4px 0px\"><tr>
					<td width=100% style=\"text-align:-webkit-left\"><font style=\"color:silver\">$label</font></td>
				</tr></table>
			";
		return "
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					$value
				</div>
			</div>
		";
/*
		return "
				<table border=0 cellpading=2 cellspacing=2 width=100% style=\"border-bottom-width: 1px; border-bottom-color: silver; border-bottom-style: solid;margin: 4px 0px\"><tr>
					<td width=100%><font style=\"color:silver\">$label</font></td>
				</tr></table>
		";
*/
	}
	function put_upload_file_mprobatorios($nume,$label,$codi,$file,$others,$content_right='')
	{
		if($_POST["codi_mpro_$codi"])
			$mpro=$_POST["codi_mpro_$codi"];
		else
			$mpro=$_POST["iden_mpro_agre"];

		if($content_right)
			$content_right="<td width=1%>
				<table border=0 width=1%><tr>
                                        <td width=1%><a href=\"javascript:eliminar_mprobatorio('".$_POST["iden_mpro_$codi"]."','$codi','$nume','$_POST[cant_alim]')\"><img src=\"img/delete2.svg\" height=\"16px\"></a></td>
                                </tr></table>
			</td>";

		if($label)
			$la="
				<div class=\"col-25\" style=\"width:100%\">
				<table border=0 cellpading=4 cellspacing=4 width=100% style=\"border-bottom-width: 1px; border-bottom-color: silver; border-bottom-style: solid;margin: 4px 0px; width:100%\"><tr>
					<td width=100%>$nume. $label[$mpro]</td>
					$content_right
				</tr></table>
				</div>
			";
		if(file_exists($file))
			$file="<BR><img src=\"$file\" width=\"100px\">";
		else
			unset($file);
		return "
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					<input type=\"file\" id=\"nomb_file_$codi\" name=\"nomb_file_$codi\" $others>
					$file<BR><BR>
					<input type=\"text\" id=\"obse_mpro_$codi\" name=\"obse_mpro_$codi\" value=\"".$_POST["obse_mpro_$codi"]."\" $others>
					<input type=hidden name=\"codi_mpro_$codi\" value=\"$mpro\">
					<input type=hidden name=\"iden_mpro_$codi\" value=\"".$_POST["iden_mpro_$codi"]."\">
				</div>
			</div>
		";
	}
	function put_upload_file($label,$name,$file,$others)
	{
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		if(file_exists($file))
			$file="<BR><img src=\"$file\" width=\"100px\">";
		else
			unset($file);
		return "
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					<input type=\"file\" id=\"$name\" name=\"$name\" $others>
					$file
				</div>
			</div>
		";
	}
	function put_column_blanco()
	{
		return "
			<div class=\"row\">
			    <div class=\"col-25\">&nbsp;</div>
				<div class=\"col-75\">&nbsp;</div>
			</div>
		";
	}
	function put_image($label,$src,$others)
	{
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		return "
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					<img src=\"$src\" $others style=\"max-width: 400px;width: 100%; border: black 1px solid; border-radius: 6px;\">
				</div>
			</div>
		";
	}
	function put_text($type,$label,$place,$name,$value,$min_size,$max_size,$others)
	{
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		return "
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					<input class=\"selectpicker\" type=\"$type\" id=\"$name\" name=\"$name\" value=\"$value\" placeholder=\"$place\" minlength=\"$min_size\" maxlength=\"$max_size\" $others>
				</div>
			</div>
		";
	}
	function put_textarea($label,$name,$value,$others)
	{
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		return "
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					<textarea id=\"$name\" name=\"$name\" $others rows=\"4\" cols=\"50\">$value</textarea>
				</div>
			</div>
		";
	}
	function put_table($label,$arra_cont)
	{
		$arra_tabl=json_decode($arra_cont);
		$tabl="<table>";
		for($f=0;$f<10;$f++)
		{
			$tabl.="<tr>
				<td>
					<input type=\"text\" id=\"f".$f."A\" name=\"f".$f."A\" value=\"".$arra_tabl[$f]->A."\">
				</td><td>
					<input type=\"text\" id=\"f".$f."B\" name=\"f".$f."B\" value=\"".$arra_tabl[$f]->B."\">
				</td>
			</tr>";
		}
		$tabl.="</table>";
		return "
			<div class=\"row\">
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
				<div class=\"col-75\">
					$tabl
				</div>
			</div>
		";
	}
	function put_checkbox($label,$name,$value,$others,$label2='')
	{
		if($value)
			$chec='checked';
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		return "
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					<input type=\"checkbox\" id=\"$name\" name=\"$name\" $chec $others>$label2
				</div>
			</div>
		";
	}
	function put_switch_old($label,$name,$chec)
	{
		if($chec)
			$chec='checked';
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
	function put_switch($label,$name,$chec)
	{
		if($chec)
			$chec='checked';
		return"
                        <div class=\"row\">
				<table border=0><tr>
					<td>
                                        	<label class=\"switch\">
                                                	<input type=\"checkbox\" name=\"$name\" $chec>
	                                                <span class=\"slider round\"></span>
	                                        </label>
					</td>
                                        <td>$label</td>
                                </tr></table>
                        </div>
                ";
	}
	function put_number($label,$place,$name,$value,$max_size,$others)
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
	function put_button_colum($label,$name,$function)
	{
		if($label)
			$la="
				<div class=\"col-25\">
					<label for=\"$name\">$label</label>
				</div>
			";
		return"
			<div class=\"row\">
				$la
				<div class=\"col-75\">
					<button class=\"button_foot\" onclick=\"$function\">$name</button>
				</div>
			</div>
		";
	}
	function put_submit($title,$function,$others="")
	{
		if($others=='vacio')
			return"
				<div class=\"row\">
					<div class=\"col-25\">
						<label for=\"$name\">&nbsp;</label>
					</div>
					<div class=\"col-75\">
						<input type=\"submit\" value=\"$title\" onclick=\"$function\" $others>
					</div>
				</div>
			";
		else
			return"
				<div class=\"row\">
					<input type=\"submit\" value=\"$title\" onclick=\"$function\" $others>
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
	function put_address($sufi,$ubig,$dire,$refe,$lati,$long,$Db)
	{
		$arra_options_dpto=$Db->get_options_dpto('ubig_reni');
		$arra_options_prov=$Db->get_options_prov('ubig_reni',substr($ubig,0,2));
		$arra_options_dist=$Db->get_options_dist('ubig_reni',substr($ubig,0,4));
		echo $this->put_text('text',CONST_SUBTITLE_ADDRESS,CONST_PLACEHOLDER_ADDRESS,"dire_$sufi",$dire,'','100','');
		echo $this->put_text('text',CONST_SUBTITLE_REFERENCE,CONST_PLACEHOLDER_REFERENCE,"refe_$sufi",$refe,'','100','');
		if($sufi=='proc')
			echo $this->put_text('text',"Nro.&nbsp;Casilla","Numero de Casilla","casi_$sufi",$refe,'','100','');
		echo"</main><main>";
		echo $this->put_select(CONST_SUBTITLE_DPTO,"dpto_$sufi",$arra_options_dpto,substr($ubig,0,2),"","p_dpto_$sufi");
		echo $this->put_select(CONST_SUBTITLE_PROV,"prov_$sufi",$arra_options_prov,substr($ubig,0,4),"","p_prov_$sufi");
		echo $this->put_select(CONST_SUBTITLE_DIST,"dist_$sufi",$arra_options_dist,substr($ubig,0,6),"","p_dist_$sufi");
	}
	function put_table_responsive_open()
	{
		return"
			<link href=\"css/table_responsive.css\" rel=\"stylesheet\" type=\"text/css\">
			<table align=center class=\"table_responsive\">
		";
	}
	function put_table_responsive_close()
	{
		return"
			</tbody>
			</table>
		";
	}
	function put_table_responsive_title($title)
	{
		return"
			<caption>
				$title
			</caption>
		";
	}
	function put_table_responsive_header($header)
	{
		$cols="";
		foreach($header as $codi => $nomb)
		{
			$cols.="<th scope=\"col\" style=\"font-size: 0.8em;\">$nomb</th>";
		}
		return"
			<thead>
				<tr>
					$cols
				</tr>
			</thead>
			<tbody>
		";
	}
	function put_table_responsive_data($header,$data)
	{
		$cols="";
		foreach($header as $codi => $nomb)
		{
			//$cols.="<td data-label=\"$nomb\">$data[$codi]</td>";
			$cols.="<td>$data[$codi]</td>";
		}
		echo"
			<tr>
				$cols
			</tr>
		";
	}
	function put_page($id,$pagi,$tota,$boto='')
	{
		$js_prev=$js_next='#';
		if($pagi>1)
			$js_prev="javascript:go_prev_$id()";
		if($pagi<$tota)
			$js_next="javascript:go_next_$id()";
		if($boto)
			$boto_izqu="
				<div class=\"pagination\" style=\"float:left\">
					<a href=\"javascript:f_accion_tabla()\">$boto</a>
				</div>
			";
		return"
			<style>
				.center_pagination {
				  text-align: right;
				  margin:10px;
				}

				.pagination {
				  display: inline-block;
				  margin:0;
				}
				.pagination_left {
				  display: inline-block;
				  float:left;
				}

				.pagination a {
				  color: black;
				  float: left;
				  padding: 8px 16px;
				  text-decoration: none;
				  transition: background-color .3s;
				  border: 1px solid #ddd;
				  margin: 0 4px;
				}

				.pagination a.active {
				  background-color: #4CAF50;
				  color: white;
				  border: 1px solid #4CAF50;
				}

				.pagination a:hover:not(.active) {background-color: #ddd;}
			</style>
			<script>
				function go_prev_$id()
				{
					--document.form.busc_pagi_actu.value;
					document.form.submit();
				}
				function go_next_$id()
				{
					++document.form.busc_pagi_actu.value;
					document.form.submit();
				}
			</script>
			<div class=\"center_pagination\">
				$boto_izqu
				<div class=\"pagination\">
					<a href=\"$js_prev\">&laquo;</a>
					<a href=\"#\">P&aacute;g. $pagi de $tota</a>
					<a href=\"$js_next\">&raquo;</a>
				</div>
			</div>
		";
	}
	function put_window2($titu="SIOJ Alimentos",$cont,$anch,$alto)
	{
		return"hola";
	}
	function put_window($id,$titu="SIOJ Alimentos",$cont,$anch=200,$alto=200)
	{
		return"
			<style>
				.modal
				{
					display: none; /* Hidden by default */
					position: fixed; /* Stay in place */
					z-index: 1; /* Sit on top */
					left: 0;
					top: 0;
					width: 100%; /* Full width */
					height: 100%; /* Full height */
					overflow: auto; /* Enable scroll if needed */
					background-color: rgb(0,0,0); /* Fallback color */
					background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
				}
				/* Modal Content/Box */
				.modal-content
				{
					border-radius:5px;
					background-color: #fefefe;
					margin: 15% auto; /* 15% from the top and centered */
					padding: 10px 20px;
					border: 1px solid #888;
					width: 100%; /* Could be more or less, depending on screen size */
					max-width: $anch; /* Could be more or less, depending on screen size */
					height:$alto;
				}
				/* The Close Button */
				.close
				{
					color: #aaa;
					float: right;
					font-size: 28px;
					font-weight: bold;
				}
				.close:hover,
				.close:focus
				{
					color: black;
					text-decoration: none;
					cursor: pointer;
				}
			</style>
			<script>
				function close_modal_$id()
				{
					document.getElementById('$id').style.display = 'none';
					return false;

				}
			</script>
			<div id=\"$id\" class=\"modal\">
				<div class=\"modal-content\">
					<div class=\"head\" style=\"color:silver; font-family:helvetica\">
						<a href=\"javascript:close_modal_$id()\"><span class=\"close\">&times;</span></a>
						<div style=\"text-align: left;padding-top: 8; border-bottom: solid; border-bottom-width: thin; height: 30;\">
							$titu
						</div>

					</div>
					<p style=\"font-family:helvetica\">$cont</p>
					<div><center>
						<div style=\"width: 47%; display: inline-block; padding: 10;\">
<button class=\"button_foot\" onclick=\"return close_modal_$id()\">&laquo; Cancelar</button>
						</div>
						<div style=\"width: 47%; display: inline-block; padding: 10;\">
<button class=\"button_foot\" onclick=\"return f_ejecutar_$id()\">Aceptar &raquo;</button>
						</div>
					</div>
				</div>
			</div>
			<script>
				var modal = document.getElementById(\"$id\");
				// Get the button that opens the modal
				var btn = document.getElementById(\"myBtn\");
				// Get the <span> element that closes the modal
				var span = document.getElementsByClassName(\"close\")[0];
				// When the user clicks on the button, open the modal
				btn.onclick = function()
				{
					modal.style.display = \"block\";
				}
				// When the user clicks on <span> (x), close the modal
				span.onclick = function()
				{
					modal.style.display = \"none\";
				}
				// When the user clicks anywhere outside of the modal, close it
				window.onclick = function(event)
				{
					if (event.target == modal)
					{
						modal.style.display = \"none\";
					}
				}
			</script>
		";
	}
	function put_window_pdf($titu="SIOJ Alimentos",$file)
	{
		return"
			<style>
				.modal
				{
					display: none; /* Hidden by default */
					position: fixed; /* Stay in place */
					z-index: 1; /* Sit on top */
					left: 0;
					top: 0;
					width: 100%; /* Full width */
					height: 100%; /* Full height */
					overflow: auto; /* Enable scroll if needed */
					background-color: rgb(0,0,0); /* Fallback color */
					background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
				}
				/* Modal Content/Box */
				.modal-content
				{
					border-radius:5px;
					background-color: #fefefe;
					margin: 5% auto; /* 15% from the top and centered */
					padding: 10px 20px;
					border: 1px solid #888;
					width: 90%; /* Could be more or less, depending on screen size */
					height:80%;
				}
				/* The Close Button */
				.close
				{
					color: #aaa;
					float: right;
					font-size: 28px;
					font-weight: bold;
				}
				.close:hover,
				.close:focus
				{
					color: black;
					text-decoration: none;
					cursor: pointer;
				}
			</style>
			<script>
				function close_modal()
				{
					document.getElementById('myModal').style.display = 'none';
					return false;

				}
			</script>
			<div id=\"myModal\" class=\"modal\">
				<div class=\"modal-content\">
					<div class=\"head\" style=\"color:silver; font-family:helvetica\">
						<a href=\"javascript:close_modal()\"><span class=\"close\">&times;</span></a>
						<div style=\"text-align: left;padding-top: 8px; border-bottom: solid; border-bottom-width: thin; height: 30px;\">
							$titu
						</div>

					</div>
					<div style=\"height:90%\"><center style=\"height:100%\">
					<embed id=\"frame_anexo\" src=\"$file\" type=\"application/pdf\" width=\"100%\" height=\"100%\" />
					</div>
				</div>
			</div>
			<script>
				var modal = document.getElementById(\"myModal\");
				// Get the button that opens the modal
				var btn = document.getElementById(\"myBtn\");
				// Get the <span> element that closes the modal
				var span = document.getElementsByClassName(\"close\")[0];
				// When the user clicks on the button, open the modal
				btn.onclick = function()
				{
					modal.style.display = \"block\";
				}
				// When the user clicks on <span> (x), close the modal
				span.onclick = function()
				{
					modal.style.display = \"none\";
				}
				// When the user clicks anywhere outside of the modal, close it
				window.onclick = function(event)
				{
					if (event.target == modal)
					{
						modal.style.display = \"none\";
					}
				}
			</script>
		";
	}
}
?>