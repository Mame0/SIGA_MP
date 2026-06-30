<?
	require_once 'include/cabecera.php';
	require_once 'classes/Html.class.php';
	require_once 'classes/Db.class.php';
	$Db = new Db();
	
	if(!empty($_POST['saveinfo']))	//guardar
	{
		$fdig=date("YmdHis");
		$result=$Db->update('mp_admi_oper',['appa_oper'=>$_POST['appa_oper'],'apma_oper'=>$_POST['apma_oper'],'nomb_oper'=>$_POST['nomb_oper'],'carg_oper'=>$_POST['carg_oper'],'depe_oper'=>$_POST['depe_oper'],'celu_oper'=>$_POST['celu_oper'],'mail_oper'=>$_POST['mail_oper'],'digi_oper'=>$_SESSION['iden_oper'],'fdig_oper'=>$fdig],['iden_oper'=>$_SESSION['iden_oper']]);
		if($result)
			echo"<script>alert('".constant("CONST_MENS_REG_OK")."')</script>";
		else
			echo"<script>alert('".constant("CONST_MENS_REG_ERROR")."')</script>";
		
		if($_POST['pass_oper'] AND $_POST['pass_ope1']==$_POST['pass_ope2'])
		{
			$result=$Db->update('mp_admi_oper',['pass_oper'=>md5($_POST['pass_ope1'])],['iden_oper'=>$_SESSION['iden_oper'],'pass_oper'=>md5($_POST['pass_oper'])]);
			if($result)
				echo"<script>alert('".constant("CONST_MENS_PASS_REG_OK")."')</script>";
			else
				echo"<script>alert('".constant("CONST_MENS_PASS_REG_ERROR")."')</script>";
		}
		unset($_POST);
	}
	if($_SESSION['iden_oper'])
	{
		$result=$Db->select(['mp_admi_oper',['appa_oper','apma_oper','nomb_oper','carg_oper','depe_oper','celu_oper','mail_oper']],['iden_oper'=>$_SESSION['iden_oper']]);
		$_POST['appa_oper']=$result[0]['appa_oper'];
		$_POST['apma_oper']=$result[0]['apma_oper'];
		$_POST['nomb_oper']=$result[0]['nomb_oper'];
		$_POST['carg_oper']=$result[0]['carg_oper'];
		$_POST['depe_oper']=$result[0]['depe_oper'];
		$_POST['celu_oper']=$result[0]['celu_oper'];
		$_POST['mail_oper']=$result[0]['mail_oper'];
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIOJAlimentos - Mi Cuenta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background-color: #ffffff; font-size: 1.1rem; }
        .card-header { background-color: #073A6B; color: white; font-weight: bold; }
        .btn-primary { background-color: #073A6B; border-color: #073A6B; font-size: 1.1rem; }
        .btn-primary:hover { background-color: #052a4e; border-color: #052a4e; }
        .form-label { font-weight: 500; color: #073A6B; font-size: 1.1rem; }
        .form-control { font-size: 1.1rem; padding: 0.6rem 0.75rem; }
        h3 { font-size: 1.75rem; }
        h5 { font-size: 1.35rem; }
    </style>
    <script>
        function check() {
            if(document.form.pass_ope1.value == document.form.pass_ope2.value) {
                if(confirm('¿Seguro de desea guardar?')) {
                    document.form.saveinfo.value=1;
                    document.form.submit();
                }
            } else {
                alert('<?=CONST_MENS_PASS_ERROR?>');
            }
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <h3 class="mb-4 pb-2 border-bottom" style="color: #073A6B;">
                    <i class="fas fa-user-cog me-2"></i><?=CONST_TITLE_MY_ACCOUNT?>
                </h3>

                <form name="form" method="post">
                    <input type="hidden" name="saveinfo">
                    
                    <h5 class="mb-3 mt-4" style="color: #073A6B;">
                        <i class="fas fa-info-circle me-2"></i><?=CONST_SUBTITLE_BASIC_INFORMATION?>
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_APPA?></label>
                            <input type="text" class="form-control" name="appa_oper" value="<?=$_POST['appa_oper']?>" placeholder="<?=CONST_PLACEHOLDER_APPA?>" required title="Solo letras">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_APMA?></label>
                            <input type="text" class="form-control" name="apma_oper" value="<?=$_POST['apma_oper']?>" placeholder="<?=CONST_PLACEHOLDER_APMA?>" required title="Solo letras">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_NAME?></label>
                            <input type="text" class="form-control" name="nomb_oper" value="<?=$_POST['nomb_oper']?>" placeholder="<?=CONST_PLACEHOLDER_NAME?>" required title="Solo letras">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_CARG?></label>
                            <input type="text" class="form-control" name="carg_oper" value="<?=$_POST['carg_oper']?>" placeholder="<?=CONST_PLACEHOLDER_CARG?>" title="Solo letras">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-secondary">Dependencia</label>
                            <input type="text" class="form-control" name="depe_oper" value="<?=$_POST['depe_oper']?>" placeholder="Ingrese Dependencia" title="Solo letras">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_CELU?></label>
                            <input type="text" class="form-control" name="celu_oper" value="<?=$_POST['celu_oper']?>" placeholder="<?=CONST_PLACEHOLDER_CELU?>" pattern="[0-9]+" title="Solo Números">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_EMAIL?></label>
                            <input type="email" class="form-control" name="mail_oper" value="<?=$_POST['mail_oper']?>" placeholder="<?=CONST_PLACEHOLDER_EMAIL?>" pattern="[^@\s]+@[^@\s]+\.[^@\s]+" title="Invalid Email Address">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-5" style="color: #073A6B;">
                        <i class="fas fa-lock me-2"></i><?=CONST_SUBTITLE_SECURITY_INFORMATION?>
                    </h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_PASS_CURRENT?></label>
                            <input type="password" class="form-control" name="pass_oper" placeholder="<?=CONST_PLACEHOLDER_PASS_CURRENT?>" pattern="[A-Za-z0-9]+" title="Solo letras y números">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_PASS_NEW?></label>
                            <input type="password" class="form-control" name="pass_ope1" placeholder="<?=CONST_PLACEHOLDER_PASS_NEW?>" minlength="6" pattern="[A-Za-z0-9]+" title="Solo letras y números">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-secondary"><?=CONST_SUBTITLE_PAS2_NEW?></label>
                            <input type="password" class="form-control" name="pass_ope2" placeholder="<?=CONST_PLACEHOLDER_PAS2_NEW?>" minlength="6" pattern="[A-Za-z0-9]+" title="Solo letras y números">
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="text-center mb-5">
                        <button type="button" class="btn btn-primary btn-lg px-5" onclick="check()">
                            <i class="fas fa-save me-2"></i><?=CONST_BUTTON_SAVE?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
