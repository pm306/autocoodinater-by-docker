<?php
/**
 * 服を選ぶアルゴリズム
 * 独断と偏見によります
 */

require_once('utils.php');
require_once('functions.php');

$max_temperature = $_POST[POST_TEMPERATURE_MAX_KEY];
$min_temperature = $_POST[POST_TEMPERATURE_MIN_KEY];


//暑い時
if($min_temperature >= BORDER_MIN_TEMPERATURE_HOT){
    selectRandomClothe($db, $selected_tops,"t_short", "poro","other1");
    selectRandomClothe($db, $selected_bottoms, "chino_thin", "other_b");
}
//まあまあ温かい時
else if($min_temperature >= BORDER_MIN_TEMPERATURE_WARM){
    $random_number = rand(1, 2);//１なら2枚、2なら１枚着る
    if($random_number === 1){
        if($max_temperature >= BORDER_MAX_TEMPERATURE_WARM){
        selectRandomClothe($db, $selected_tops, "t_short","other1");           
        }else{
        selectRandomClothe($db, $selected_tops, "t_short", "inner","other1");
        }
        selectRandomClothe($db, $selected_tops, "t_long", "check","other2");
    }
    if($random_number === 2 || count($selected_tops) < 2){
        unset($selected_tops);
        selectRandomClothe($db, $selected_tops, "t_short", "poro", "t_long","other2");       
    }
    selectRandomClothe($db, $selected_tops, "chino_thin","other_b");

}
//ぼちぼち
else if($min_temperature >= BORDER_MIN_TEMPERATURE_COMFORTABLE){
    if($max_temperature >= BORDER_MAX_TEMPERATURE_WARM){
        selectRandomClothe($db, $selected_tops, "t_short","other1");           
    }else{
        selectRandomClothe($db, $selected_tops, "t_short", "inner","other1");
    }
    selectRandomClothe($db, $selected_tops, "t_long", "check","other2");
    selectRandomClothe($db, $selected_bottoms, "chino_thin", "other_b");
}
//ちょっと寒い
else if($min_temperature >= BORDER_MIN_TEMPERATURE_COOL){
    if($max_temperature >= BORDER_MAX_TEMPERATURE_WARM){
        selectRandomClothe($db, $selected_tops, "t_short","other1");           
    }else{
        selectRandomClothe($db, $selected_tops, "t_short", "inner","other1");
        }
    selectRandomClothe($db, $selected_tops, "t_long", "check","other2");
    if($max_temperature >= 19){
    selectRandomClothe($db, $selected_tops, "parker","check_thick","cardigan_check","other3");        
    }else{
    selectRandomClothe($db, $selected_tops, "parker", "trainer", "check_thick", "seta", "cardigan", "cardigan_check","other3");
    }
    selectRandomClothe($db, $selected_bottoms, "chino_thin","other3"); 
}
//けっこう寒い
else if($min_temperature >= BORDER_MIN_TEMPERATURE_COLD){
    selectRandomClothe($db, $selected_tops, "t_short", "inner","other1");
    selectRandomClothe($db, $selected_tops, "t_long", "check","other2");
    if($max_temperature >= BORDER_MAX_TEMPERATURE_COMFORTABLE){
    selectRandomClothe($db, $selected_tops, "parker","check_thick","cardigan_check","other3");        
    }else{
    selectRandomClothe($db, $selected_tops, "parker", "trainer", "check_thick", "seta", "cardigan", "cardigan_check","other3");
    }
    selectRandomClothe($db, $selected_tops, "outer_thin", "other4");
    selectRandomClothe($db, $selected_bottoms, "chino_thin", "chino_thick", "other_b");

//冬
}else if($min_temperature > MIN_TEMPERATURE_LIMIT){
    selectRandomClothe($db, $selected_tops, "t_short", "inner","other1");
    selectRandomClothe($db, $selected_tops, "t_long", "check", "other2");
    if($max_temperature >= BORDER_MAX_TEMPERATURE_COMFORTABLE){
    selectRandomClothe($db, $selected_tops, "parker","check_thick","cardigan_check","other3");        
    }else{
    selectRandomClothe($db, $selected_tops, "parker", "trainer", "check_thick", "seta", "cardigan", "cardigan_check","other3");
    }
    selectRandomClothe($db, $selected_tops, "outer_thin", "outer_thick","other4");
    selectRandomClothe($db, $selected_bottoms, "chino_thin", "chino_thick","other_b");
}else{
    //条件に合う服がない場合
    selectRandomClothe($db, $selected_tops, "null");
    selectRandomClothe($db, $selected_bottoms, "null");
}

?>