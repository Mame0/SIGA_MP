function trim(cad)
{
    var posi = 0;
    var posf = 0;
    for(var i=0; i<cad.length; i++)
    {
        if (cad.charAt(i) == " ")
            posi++;
        else
            break;
    }

    if (posi == cad.length)
        texto = "";
    else
    {
        for (var j=cad.length; j>0; j--)
        {
            if (cad.charAt(j-1) == " ")
                posf++;
            else
                break;
        }
    }
    texto = cad.substring(posi,cad.length);
    texto = texto.substring(0,texto.length-posf);
    return texto;
}
//--------------------------------------------------------------------------

function requeridos()
{
    for (var i=0; i<requeridos.arguments.length; i=i+2)
    {
        obj = requeridos.arguments[i];
        obj.value = trim(obj.value);
        if (obj.value == "")
        {
            alert('Debe completar los datos requeridos.\n'+'Dato incompleto: '+requeridos.arguments[i+1]);
            obj.focus();
            obj.select();
            return false;
        }
    }
    return true;
}
//--------------------------------------------------------------------------

function restringir_ndoc(frm)
{
  if (frm.sel_tipodoc.value == "01")
     frm.txt_usua_ndoc.maxLength = 8;
  else 
     frm.txt_usua_ndoc.maxLength = 15;

}
//--------------------------------------------------------------------------

var nav4 = window.Event ? true : false;
if(navigator.appName.charAt(0)=='M' && parseInt(navigator.appVersion)>=4)
	nav4= false;
function solonumeros(evt)
{
    var key = nav4 ? evt.which : evt.keyCode;
    return (key <= 13 || (key >= 48 && key <= 57));
}
function solonumerosfloat(evt)
{
    var key = nav4 ? evt.which : evt.keyCode;
    return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
}

function solonombres(evt)
{
    var key = nav4 ? evt.which : evt.keyCode;
    return (key <= 13 || (key >= 65 && key <= 90) || (key >= 97 && key <= 122) || key == 225 || key == 233 || key == 237 || key == 243 || key == 250 || key == 252 || key == 241 || key == 193 || key == 201 || key == 205 || key == 211 || key == 218 || key == 220 || key == 209 || key == 39 || key == 32 || key == 45);
}

function soloalfanumericos(evt)
{
    var key = nav4 ? evt.which : evt.keyCode;
    return ( key <= 13 || (key >= 48 && key <= 57) || (key >= 65 && key <= 90) || (key >= 97 && key <= 122) );
}

function solooficio(evt)
{
    var key = nav4 ? evt.which : evt.keyCode;
    return (key <= 13 || (key >= 48 && key <= 57) || key == 45);
}
