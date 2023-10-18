<form method="post" name="<?= $formName ?>" action="clothes_details.php" style="display:inline">
    <input type="hidden" name="picture_id" value="<?= $pictureId ?>">
    <a href="javascript:document.<?= $formName ?>.submit()" style='text-decoration: none;'>
        <img id="result" src="<?= $imageSrc ?>" width="200" height="200">
    </a>
</form>
