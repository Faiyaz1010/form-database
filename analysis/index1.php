<?php
$PATH_TO_SCAN = "testspace";
header('Content-Type: application/json');
// scan all dir
$folder_tree = array();
$scan_results = array();

foreach (scandir($PATH_TO_SCAN) as $folder_name) {
    if($folder_name == "." || $folder_name == ".."){
        continue;
    }else{
        $a = $PATH_TO_SCAN."/".$folder_name;
        foreach (scandir($a) as $sub_folders) {
            if($sub_folders == "." || $sub_folders == ".."){
                continue;
            }else{
                $scan_results = [];
                $b = $a."/".$sub_folders;
                // scan all four old functions
                $scan_results["target_folder"] = $folder_name;
                $scan_results["number_of_files"] = get_number_of_files_in_thisdir($b);
                if($scan_results["number_of_files"] != 0){
                    $scan_results["last_modified"] = get_lastmtime_of_dir($b);
                    $scan_results["first_modified"] = get_firstmtime_of_dir($b);
                    $scan_results["dir_size"] = get_dir_size($b);
                }



                // if($scan_results["number_of_files"] != 0) {
                //     $scan_results["last_modified"] = get_lastmtime_of_dir($b);
                //     $scan_results["first_modified"] = get_firstmtime_of_dir($b);
                //     $scan_results["dir_size"] = get_dir_size($b);
                // }

                
                $folder_tree[$folder_name][$sub_folders] = $scan_results;
            }
        }
    }
}

echo json_encode($folder_tree);


// import dependent functions to work space
function get_lastmtime_of_dir($dir_path): string{
    $res = array();
    $ignored = array('.', '..', 'old');
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
    if(!in_array($ignored, scandir($dir_path))){
        $res = json_decode($sorted_dir_info_only_files);
        $n = (sizeof($res)>2)?($res[0]->filemtime):("no csv files");
    }
    return $n;
}
function get_firstmtime_of_dir($dir_path): string{
    $res = array();
    $ignored = array('.', '..', 'old');
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));

    if(!in_array($ignored, scandir($dir_path))){
        $res = json_decode($sorted_dir_info_only_files);
        $n = (sizeof($res)>2)?($res[sizeof($res)-1]->filemtime):("no csv files");
    }
    return $n;
}
function get_dir_size($dir_path): string{
    $res = array();
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
    $res_arr = json_decode($sorted_dir_info_only_files);
    $dir_size = 0;
    if(sizeof($res_arr)<1){
        return "0";
    }else{
        foreach($res_arr as $params){
            $dir_size += $params->filesize;
        }
        $res = $dir_size;
        return ($res);
    }
}
function get_number_of_files_in_thisdir($dir_path): string{
    $res = array();
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
    $res = sizeof(json_decode($sorted_dir_info_only_files));
    return ($res);
}
// PERFORM MAINTAINANCE
function delete_all_files_in_dir($dir_path, $idoldthan_=false): array{
    $res = array(); $i=0; $j=0;
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
    $res_arr = json_decode($sorted_dir_info_only_files);
    foreach($res_arr as $params){ // iterate files
        if(is_file($dir_path."/".$params->filename)) {
            if(unlink($dir_path."/".$params->filename)){
                $i++;
            }else{
                $j++;
            }
        }else{
            continue;
        }
    }
    $res["deleted"] = $i;
    $res["failedToDelete"] = $j;

    return ($res);
}
function getDirInfo($PATH): string{
    header('Content-type: application/json');
    return (dirToArray($PATH));
}
function dirToArray($dir) {
    $result = array();
    $cdir = scandir($dir, SCANDIR_SORT_ASCENDING);
    $i = 0;
    foreach ($cdir as $key => $value)
    {
       if (!in_array($value,array(".","..")))
 
       {
          if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
          {
             $result[$i][$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
             $i++;
          }
          else
          {
             $result[$i]["filename"] = $value;
             $result[$i]["filesize"] = filesize($dir."/".$value);
             $result[$i]["filemtime"] = date("Y-m-d H:i:s.", filemtime($dir."/".$value));
            $i++;
          } 
       }
    }
    return ($result);
 }

 function scan_dir_sort_by_timemodified($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess', 'old'); // -- ignore these file names
    $files = array(); //----------------------------------- create an empty files array to play with
    foreach (scandir($dir) as $file) {
        if ($file[0] === '.') continue; //----------------- ignores all files starting with '.'
        if (in_array($file, $ignored)) continue; //-------- ignores all files given in $ignored
        $files[$file] = filemtime($dir . '/' . $file); //-- add to files list
    }
    arsort($files); //------------------------------------- sort file values (creation timestamps)
    $files = array_keys($files); //------------------------ get all files after sorting
    return ($files) ? $files : false;
}
function make_sorted_file_info($dir, $sorted_arr): string{
    $res = array();
    $i=0;
    if($sorted_arr != false){
        foreach($sorted_arr as $file_name){
            if(!is_dir($dir."/".$file_name)){
                $res[$i]["filename"] = $file_name;
                $res[$i]["filesize"] = filesize($dir."/".$file_name);
                date_default_timezone_set('Asia/Tokyo');
                $res[$i]["filemtime"] = date("Y-m-d H:i:s.", filemtime($dir."/".$file_name));
                $i++;
            }
        }
        return json_encode($res);
    }else{
        return json_encode(array());
    }
}
?>


