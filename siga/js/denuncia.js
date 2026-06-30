
function calcularEdad(fecha) {
                var d=fecha.substring(0,2);
                var m=fecha.substring(3,5);
                var a=fecha.substring(6,10);
                fecha=m+'/'+d+'/'+a;
                var hoy = new Date();
                var cumpleanos = new Date(fecha);
                var edad = hoy.getFullYear() - cumpleanos.getFullYear();
                var m = hoy.getMonth() - cumpleanos.getMonth();

                if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
                        edad--;
                }
                return edad;
        }
	function ver_map(titu,nomb,lati,long,cvio,part,etiq)
        {
		GB_showCenter('UBIQUE DIRECCION DE: '+titu, '../../denu_usua_mapa.php?codi_tipo='+nomb+'&n_latitud='+lati+'&n_longitud='+long+'&n_codi_cviolencia='+cvio+'&n_codi_parte='+part+'&nomb_parte='+etiq,'515','730');
        }
	function cambiar(pagi)
	{
		document.form.saveinfo.value='1';
		document.form.redireccionar.value=pagi;
		document.form.submit();
	}
	function f_WebService_user()
	{
		var dni = document.getElementById("ndoc_oper").value;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function()
		{
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var json = eval('(' + xmlhttp.responseText + ')');
				if(json[0].NOMBRE=='no encontrado')
				{
				}
				else
				{
					if(document.getElementById('nomb_oper').value=='')
						document.getElementById('nomb_oper').value = json[0].NOMBRE;
					if(document.getElementById('appa_oper').value=='')
						document.getElementById('appa_oper').value = json[0].APP;
					if(document.getElementById('apma_oper').value=='')
						document.getElementById('apma_oper').value = json[0].APM;
				}
			}
		}  
		xmlhttp.open('GET', 'WebServiceKn.php?Id=' + dni, true);
		xmlhttp.send();      
	}
	
	function f_WebService_login()
	{
		var dni = document.getElementById("n_ndocumento").value;
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function()
		{
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var json = eval('(' + xmlhttp.responseText + ')');
				if(json[0].NOMBRE=='no encontrado')
				{
				}
				else
				{
					document.getElementById('f_caducidad_verif').value = json[0].fech_cadu;
					document.getElementById('n_cverificacion_verif').value = json[0].digi_veri;
				}
			}
		}  
		xmlhttp.open('GET', 'WebServiceKn.php?Id=' + dni, true);
		xmlhttp.send();      
	}
	function f_WebService_multi(sufi)
	{
		var dni = document.getElementById("ndoc_"+sufi).value;
		if(dni.length==8)
		{
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var json = eval('(' + xmlhttp.responseText + ')');
				if(json[0].NOMBRE=='no encontrado')
				{
					document.getElementById('nomb_'+sufi).value = '';
					document.getElementById('appa_'+sufi).value = '';
					document.getElementById('apma_'+sufi).value = '';
					document.getElementById('dire_'+sufi).value = '';
					document.getElementById('fnac_'+sufi).value = '';
				}
				else
				{
					document.getElementById('nomb_'+sufi).value = json[0].NOMBRE;
					document.getElementById('appa_'+sufi).value = json[0].APP;
					document.getElementById('apma_'+sufi).value = json[0].APM;
					document.getElementById('dire_'+sufi).value = json[0].nomb_dire;
					document.getElementById('fnac_'+sufi).value = json[0].fechanac.substring(6,10)+'-'+ json[0].fechanac.substring(3,5)+'-'+ json[0].fechanac.substring(0,2);

					if(json[0].sexo.substring(0,1)=='M')
						sex='1';
					if(json[0].sexo.substring(0,1)=='F')
						sex='2';
					document.getElementById('sexo_'+sufi).value = sex;

					///////UBIGEO DIRECCION
					document.getElementById('dpto_'+sufi).value=json[0].udep_domi;
					var evt = document.createEvent("HTMLEvents");
					evt.initEvent("change", false, true);
					document.getElementById('dpto_'+sufi).dispatchEvent(evt);
					setTimeout (function() { cambiar_provincia('prov_'+sufi,json[0].udep_domi+json[0].upro_domi) }, 800);
					setTimeout (function() { cambiar_distrito('dist_'+sufi,json[0].udep_domi+json[0].upro_domi+json[0].udis_domi) }, 1600);

				}
			}
			else
			{
//				var d = document.getElementById("espera2");
//				d.innerHTML = '<img src="CSS_1/Cargando.gif" height="15" width="25">';
			}
		}  
		xmlhttp.open('GET', 'WebServiceKn.php?Id=' + dni, true);
		xmlhttp.send();      
		}
	}
	function f_WebService(sufi)
	{
		var dni = document.getElementById("ndoc_part").value;
		if(dni.length==8)
		{
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var json = eval('(' + xmlhttp.responseText + ')');
				if(json[0].NOMBRE=='no encontrado')
				{
					document.getElementById('nomb_part').value = '';
					document.getElementById('appa_part').value = '';
					document.getElementById('apma_part').value = '';
					document.getElementById('dire_real').value = '';
				}
				else
				{
					document.getElementById('nomb_part').value = json[0].NOMBRE;
					document.getElementById('appa_part').value = json[0].APP;
					document.getElementById('apma_part').value = json[0].APM;
					document.getElementById('dire_real').value = json[0].nomb_dire;

					if(json[0].sexo.substring(0,1)=='M')
						sex='1';
					if(json[0].sexo.substring(0,1)=='F')
						sex='2';
					document.getElementById('sexo_part').value = sex;

					///////UBIGEO DIRECCION
					document.getElementById('dpto_real').value=json[0].udep_domi;
					var evt = document.createEvent("HTMLEvents");
					evt.initEvent("change", false, true);
					document.getElementById('dpto_real').dispatchEvent(evt);
					setTimeout (function() { cambiar_provincia('prov_real',json[0].udep_domi+json[0].upro_domi) }, 800);
					setTimeout (function() { cambiar_distrito('dist_real',json[0].udep_domi+json[0].upro_domi+json[0].udis_domi) }, 1600);

				}
			}
			else
			{
//				var d = document.getElementById("espera2");
//				d.innerHTML = '<img src="CSS_1/Cargando.gif" height="15" width="25">';
			}
		}  
		xmlhttp.open('GET', 'WebServiceKn.php?Id=' + dni, true);
		xmlhttp.send();      
		}
	}
	function cambiar_provincia(sufi,valo)
	{
		//alert(sufi+' -- '+valo);
		document.getElementById(sufi).value=valo;
		var evt = document.createEvent("HTMLEvents");
		evt.initEvent("change", false, true);
		document.getElementById(sufi).dispatchEvent(evt);
	}
	function cambiar_distrito(sufi,valo)
	{
		document.getElementById(sufi).value=valo;
	}
