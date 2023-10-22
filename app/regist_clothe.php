<?php 
global $db;
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once("utils.php");
require_once('lib/functions.php');


if(!empty($_POST)){
    //エラーチェック
    //ファイルが空
    if($_FILES['picture']['name']===''){
        $error['file'] = ERROR_TEMPERATURE_BLANK;
    }
    //分類を選んでいない
    if($_POST['type']=== CLOTH_TYPE_TOPS || $_POST['type']=== CLOTH_TYPE_BOTTOMS){
        $error['type'] = ERROR_TEMPERATURE_BLANK;
    }
    //アップロード
    if(isset($_FILES['picture']) && is_uploaded_file($_FILES['picture']['tmp_name'])){
        $old_name = $_FILES ['picture'] ['tmp_name'];
        if (! file_exists (UPLOAD_DIR)) {
            mkdir (UPLOAD_DIR);
        }
        $new_name = date (IMAGE_NAME_FORMAT);
        $new_name .= mt_rand ();

        switch (exif_imagetype ( $_FILES ['picture'] ['tmp_name'] )) {
            case IMAGETYPE_JPEG :
                $new_name .= '.jpg';
                break;
            case IMAGETYPE_PNG :
                $new_name .= '.png';
                break;
            default :
                $error['imagetype'] = ERROR_TEMPERATURE_IMPOSSIBLE; //jpg, pngのみ対応（暫定）
                break;
        }

        if (empty($error) && move_uploaded_file ( $old_name, UPLOAD_DIR . $new_name )) {
            $msg = UPLOAD_SUCCESS_MESSAGE;
        } else {
            $msg = UPLOAD_FAILURE_MESSAGE;
        }
    }
    //エラーがなければデータベースに登録する
    if(empty($error)){
	    $statement = $db->prepare(INSERT_NEW_CLOTH);
	    $statement->execute(array(
            $_SESSION['name'],
            $_POST['type'],
            $new_name ?? '',
        ));
    }
}
?>
<?php if($msg === "追加しました"):?>
<br><div id="complete"><?php echo $msg?></div><br>
<?php else:?>
<?=$msg?>
<h1>服の登録</h1>
<p>画像と種類を選んで「追加」ボタンを押してください。<br>
<?php endif; ?>
<a href="closet.php"><img src="pictures/navigationj_back.png" width="100" height="50"></a>
<div id="content">
<form action="" method="post" class="regist" enctype="multipart/form-data">
	<dl>
		<dt>【画像】※jpg、jpeg、pngのみ対応</dt>
        <?php if(($error['file'] ?? '')==='blank'):?>
                <div class="alert">※ファイルが選択されていません</div>
              <?php endif; ?>
        <?php if($error['imagetype'] === 'incompatible'):?>
            <div class="alert">※画像の形式が間違っています</div>
        <?php endif; ?>
		<dd>
            <input type="file" name="picture" accept="image/*" size="35" onchange="previewImage(this);"/>
            <br><img id="preview" src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" style="max-width:200px;">
        </dd>
        <br>
        <dt>【分類】</dt>
        <?php if(($error['type'] ?? '')==='blank'):?>
                <div class="alert">※服のタイプを選んでください</div>
             <?php endif; ?>
        <dd>
            <select name="type">
                <option value="tops">----トップス----</option>
                <?php
                foreach(fetchClothesTypes(['tops']) as $code => $name):
                    echo '<option value="'.$code.'">'.$name.'</option>';
                endforeach;
                ?>
                <option value="bottoms">----ボトムス----</option>
                <?php
                foreach(fetchClothesTypes(['bottoms']) as $code => $name):
                    echo '<option value="'.$code.'">'.$name.'</option>';
                endforeach;                
                ?>
            </select>
        </dd>
	</dl>
	<div><input class="regist" type="submit" value="追加" /></div>
</form>
</div>
<br><hr>
<div>
    ・分類について<br>
    ちょうどいい選択肢がない場合は「その他」を選んでください。   
</div>
<script>
function previewImage(obj)
{
	let fileReader = new FileReader();
	fileReader.onload = (function() {
		document.getElementById('preview').src = fileReader.result;
	});
	fileReader.readAsDataURL(obj.files[0]);
}
</script>

<?php require_once('footer.php')?>