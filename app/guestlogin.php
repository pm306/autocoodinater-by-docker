<?php
/*
ゲストアカウントの初期化
*/

// ユーザー名が「ゲスト」の全データおよび画像ファイルを削除
$result = $db->query('SELECT id, picture FROM clothes WHERE owner="ゲスト"');
while($res = $result->fetch()){
    $pass = 'upload/';
    $pass .= $res['picture'];
    unlink($pass);
}
$db->query('DELETE FROM clothes WHERE owner="ゲスト"');

// サンプルデータを新しく登録する
$sample_clothes = [
    ["inner", "sample1.png"],
    ["t_short", "sample2.png"],
    ["t_short", "sample3.png"],
    ["poro", "sample4.png"],
    ["poro", "sample5.png"],
    ["t_long", "sample6.png"],
    ["check", "sample7.png"],
    ["parker", "sample8.png"],
    ["parker", "sample9.png"],
    ["trainer", "sample10.png"],
    ["seta", "sample11.png"],
    ["seta", "sample12.png"],
    ["cardigan_chack", "sample13.png"],
    ["outer_thin", "sample14.png"],
    ["outer_thin", "sample15.png"],
    ["outer_thick", "sample16.png"],
    ["outer_thick", "sample17.png"],
    ["chino_thin", "sample18.png"],
    ["other_b", "sample19.png"]
];

foreach ($sample_clothes as $clothes) {
    $type = $clothes[0];
    $picture = $clothes[1];
    $db->query("INSERT INTO clothes(owner, type, picture, used_date) VALUES('ゲスト', '$type', '$picture', '2000-01-01')");
}

// 画像をuploadファイルにコピー
$num = 19;
for($i=1; $i<=$num; $i++){
    $before_pass = 'guest/sample'.$i.'.png';
    $after_pass = 'upload/sample'.$i.'.png';
    copy($before_pass, $after_pass);
}
?>
