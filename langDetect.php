<?php

class LangDetect {
    //don't change unless you use your own fingerprints
    var    $ng_max_chars = 4;        //maximum of an n-gram (is a 1to4-grams here)
    var    $ng_number_lm = 400;      //default nb of ngrams in LM-fingerprints
    //Path LM-files
    //var $dir =  $_SERVER['DOCUMENT_ROOT'].'/synchNow/langdetect/finger_prints/';
    
    var $dir =  './langdetect/finger_prints/'; //RELATIV TO CALLING SCRIPT
    //reasonable defaults
    var    $ng_number_sub = 350;     //default nb of ngrams created from analyzed text
    var    $max_delta = 140000;    //stop evaluation deviate strongly
    var    $limit_lines = 100;     //limit # line of text-file used (-1 = all lines)

//Constructor: input= string or txt-file,
function LangDetect($input, $sec = false, $dir_prints= false){
    //echo '<br>'.$input.'<br>';
    $this->input = $input;
    if ($sec == false) {
         $this->result_type = 1;
         $this->dir = '/home/www/finger_prints/';
    }
    if ($sec != false) {
        $this->result_type = $sec;
        if ($sec == 'g') {
            $this->ng_number_sub = $this->ng_number_lm;
            $this->dir_generate = $input;
        } elseif ($sec != 1 && $sec != -1) {
            echo "<br>***Invalid 2nd Argument (1 or -1 to analyze, 'g' for Generation)<br>";
        }
        if ($dir_prints !=false){
            $this->dir = $dir_prints;
         } else {
         	$this->dir = '/var/www/finger_prints/';
        }
    }
}
// MAIN- analyze string or text-file
function analyze() {
    if (substr($this->input, -4, 4) == '.txt') {
        //echo "<br>*** analyzing a text-file ******<br>";
        $this->string_readfile = $this->input;
        $this->extractText();
     } else {
        $this->string_used = $this->input;
        //echo "<br>*** analyzing a string ******<br>";

    }
    if(!empty($this->string_used)) {
         $this->getFingerprint();
         $this->createNGrams();
         if ($this->result_type == 1){//single result
            return $this->compareNGramsOne();
        } elseif ($this->result_type == -1){ //result-array
            return $this->compareNGrams();
         } else {
            return "<br>*** Error: 2nd Argument must be either 1 or -1<br>";
          }
    } else {
        return "*** Empty Text String /or wrong path/name of text file*****<br>";
    }
}
// MAIN- create Fingerprint(s) of text-file(s) in $dir_generate
function Generate() {
    echo "<br>***Generating Fingerprints in: ". $this->dir_generate ."<br>";
    if (is_dir($this->dir_generate)) {
        $pattern = "*.txt";
        chdir($this->dir_generate);
        $files = glob($pattern);
        $count = 1;
        foreach ($files as $this->string_readfile) {
            $this->extractText();
            $filename = basename($this->string_readfile, ".txt"). ".lm";
            $new_lm_array = $this->createNGrams();
            $new_lm_file = $this->dir_generate . $filename;
            $handle = fopen($new_lm_file, 'w');
            foreach ($new_lm_array as $key => $ngram) {
                $line = $ngram ."\t ". ($key+1) ."\n";
                //echo "ja<br>";
                fwrite($handle,  $line);
            }
            fclose($handle);
            echo "<br>***[$count] generated: ". $filename;
            $count++;
        }
    } else {
        if(empty($this->dir_generate)) {
            echo "<br>*** Use <b>'g'</b> as 2nd Argument when Generating finger-pritns<br>";
          } else {
            echo "<br>*** ERROR: Directory does not exist!<br>";
        }
     }
}
//-------------------------------//----------------------------------------//
//get multiple ngram-array of all LM-files in LM-DIR
function getFingerprint() {
    $pattern = "*.lm";
    chdir($this->dir);
    $files = glob($pattern);
    foreach ($files as $readfile) {
        if (is_file($readfile)) {
            $bsnm = basename($readfile, ".lm");
            $handle = fopen($readfile, 'r');
            for ($i=0; $i < $this->ng_number_lm; $i++) {
                $line = fgets($handle);
                $part = explode(" ", $line);
                $lm[$bsnm][]= trim($part[0]);
            }
        } else {
              echo " *** Pls check this LM -file: ". basename($readfile);
              echo "<br> *** Path". $readfile;
        }
    }
$this->lm_ng = $lm;
/*
    echo "HAllo";
    echo "<pre>\n";
    print_r($this->lm_ng);
    echo "</pre>\n";
*/
return $lm;
}
//-------------------------------//----------------------------------------//
/*  create ngram-array of given string  */
function createNGrams($string=false) {
    if ($string) {
        $this->string_used = $string;
    }
    $array_words = explode(" ", $this->string_used);
    foreach($array_words as $word) {
        $word = "_". $word . "_";
        $word_size = strlen($word);
        for ($i=0; $i < $word_size; $i++){ //start position within word
            for ($s=1; $s<($this->ng_max_chars + 1); $s++) {  //length of ngram
                if (($i + $s) < $word_size + 1) { //length depends on postion
                     $array_ngram[] = substr($word, $i, $s);
                 }
             }
         }
    }
    //count-> value(frequency, int)... key(ngram, string)
    $blub = array_count_values($array_ngram);
    //sort array by value(frequency) desc
    arsort($blub);
    //use only top frequent ngrams (def by $ng_number)
    $top = array_slice($blub, 0, $this->ng_number_sub);
    foreach ($top as $keyvar => $valvar){
        $blubber_sub_ng[] = $keyvar;
    }
    $this->sub_ng = $blubber_sub_ng;
    return $blubber_sub_ng;
}
//-------------------------------//----------------------------------------//
/*  compare ngrams: Textinput vs lm-files.
    Returns array of lm basenames (languages) with lowest deviation */
function compareNGrams() {
$limit = $this->max_delta;
    foreach ($this->lm_ng as $lm_basename => $language) {
        $delta = 0;
        //compare each ngram of input text to current lm-array
        foreach ($this->sub_ng as $key => $existing_ngram){
            //match
            if(in_array($existing_ngram, $language)) {
                $delta += abs($key - array_search($existing_ngram, $language));
            //no match
            } else {
                $delta += 400;
            }
            //abort: this language already differs too much
            if ($delta > $this->max_delta) {
                break;
             }
        } // End comparison with current language

        //include only non-aborted languages in result array
        if ($delta < ($this->max_delta)-400) {
            $result[$lm_basename] = $delta;
        }
    } //End comparioson all languages
    if(!isset($result)) {
      $result = "sorry nothing no lang found";
    } else {
        asort($result);
     }
    return $result;
}
/* VARIATION- COMPARE ng's - Return 1 LANGUAGE only */
function compareNGramsOne() {
$limit = 160000;
    foreach ($this->lm_ng as $lm_basename => $language) {
        $delta = 0;
        foreach ($this->sub_ng as $key => $existing_ngram){
            if(in_array($existing_ngram, $language)) {
                $delta += abs($key - array_search($existing_ngram, $language));
            } else {
                $delta += 400;
            }
            if ($delta > $limit) {
                break;
             }
        }
        if ($delta < $limit) {
            $result[$lm_basename] = $delta;
            $limit = $delta; //lower limit
        }
    }
    if(!isset($result)) {
      $result_first = "sorry nothing no lang found";
    } else {
        asort($result);
            //basename of best matching lm file
            list($result_first, $ignore) = each($result);
     }
    return $result_first;
}
//-------------------------------//----------------------------------------//
/* read out text from regular text file  */
function extractText() {
    $blu_string = '';
    if (is_file($this->string_readfile)) {
        $handle = fopen($this->string_readfile, 'r');
         $line_num = 1;
        while (!feof($handle)) {
            //default -1 (read all lines)
            if ($this->limit_lines == $line_num){
                break;
              }
              //line with max length of 2^19
            $line = trim(fgets($handle, 528288));
            if ($line != "") {
                $blu_string .= " ". $line;
                $line_num++;
            }
        }
        fclose($handle);
    } else {echo "*** Text file NOT FOUND<br>";}
//echo "<p>$blu_string</p>";
$this->string_used = $blu_string;

return $blu_string;
}
//-------------------------------//----------------------------------------//
}
?>
