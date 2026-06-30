<?php
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>-</title>
		<link rel="stylesheet" href="css/forms_demanda.css" />
		<link rel="stylesheet" href="css/forms_foot.css" />
		<link rel="stylesheet" href="css/forms_column.css" />
		<SCRIPT language="JavaScript" src="js/cadenas.js"></SCRIPT>
		<SCRIPT language="JavaScript" src="js/denuncia.js"></SCRIPT>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script>
			function f_importar2() {
				event.preventDefault();

document.form.accion.value='2';
		setTimeout(function(){
		var fd = new FormData(document.getElementById("form"));
  $(".progress").show();
  $('#bookdetails').modal("show").on('shown.bs.modal', function() {

			$.ajax({
			  url: "importacsv.php",
			  async: false,
			  type: "POST",
			  data: fd,
			  processData: false,  // tell jQuery not to process the data
			  contentType: false   // tell jQuery not to set contentType
			}).done(function( data ) {
				data=data.replace("\n","");
				$('#bookdetails').modal("hide");

				document.form.accion.value='0';
				document.form.submit();

			}).fail(function (jqXHR, textStatus) {
				//alert (textStatus);
			});

  });


		}, 200);


			}




			function ajustar_altura() {
				parent.document.getElementById('body_iframe').height=parent.window.innerHeight-80;
			}
			ajustar_altura();
		</script>
	</head>
	<body style="margin-bottom: 30px;">
	<center><h2 style="color:#073A6B">IMPORTACION DE DATOS SGF</h2></center>
		<form id="form" name="form" method="post" action="casos_importa_datossgf.php" enctype="multipart/form-data">

<?php
	$html=new htmlclass;

	echo"<div style=\"width:90%;max-width:800px;margin:auto;\">";
	echo '
	<input type="button" id="pickfiles" value="Seleccione Archivo con datos a Importar"/>
	<div id="filelist"></div>
	<input type="hidden" id="nomfile" name="nomfile" value="">';
	echo "</div>";

	echo"<main style='column-count:2;'>";
	echo $html->put_button_colum("&nbsp;","Importar Datos de Archivo &raquo;","return f_importar2()");
	echo"</main>";

echo "<input name='accion' type='hidden' id='accion' value=''>";

?>
<div id='cargadorvacio'></div>

	</form>
	</body>
</html>




<script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.3/plupload.full.min.js"></script>
<script>
// (C) INITIALIZE UPLOADER
window.addEventListener("load", () => {
  // (C1) GET HTML FILE LIST
  var filelist = document.getElementById("filelist");

  // (C2) INIT PLUPLOAD
  var uploader = new plupload.Uploader({
    runtimes: "html5",
    browse_button: "pickfiles",
    url: "chunk.php",
    chunk_size: "10mb",
    filters: {
      max_file_size: "150mb",
      mime_types: [{title: "files", extensions: "txt,csv"}]
    },
    init: {
      PostInit: () => { filelist.innerHTML = "<div>Seleccione Archivo</div>"; },
      FilesAdded: (up, files) => {
        plupload.each(files, (file) => {
          let row = document.createElement("div");
          row.id = file.id;
          row.innerHTML = `${file.name} (${plupload.formatSize(file.size)}) <strong></strong>`;

          document.getElementById("nomfile").value=`${file.name}`;

          filelist.appendChild(row);
        });
        uploader.start();
      },
      UploadProgress: (up, file) => {
        document.querySelector(`#${file.id} strong`).innerHTML = `${file.percent}%`;
      },
      Error: (up, err) => { console.error(err); }
    }
  });
  uploader.init();
});
</script>





    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" ></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" ></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />

<!--<button type="button" class="btn btn-info push">Open Modal</button>-->
<div id="bookdetails" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Procesando...</h4>
      </div>
      <div class="modal-body" style="text-align: center;">
	      <div class="spinner" style="text-align: center;"></div>
<!--          <img src="http://conferoapp.com/icons/preloader.gif" class="progress"> -->
      </div>
    </div>
  </div>
</div>


<style>
.spinner {
	margin: auto;
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border-left-color: #09f;

  animation: spin 1s ease infinite;
}
/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.progress {
  width: 150px;
  height: 150px;
}
</style>
