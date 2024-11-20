<?php
$dir_to_scan_PATH = "testspace";
// header('Content-type: application/json');

//SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
// $sorted_dir_info_only_files = (make_sorted_file_info($dir_to_scan_PATH, scan_dir_sort_by_timemodified($dir_to_scan_PATH)));
// echo json_encode($sorted_dir_info_only_files, 0);
echo "This dir was last modified on: ".get_lastmtime_of_dir($dir_to_scan_PATH)."<br>";
echo "This dir was first modified on: ".get_firstmtime_of_dir($dir_to_scan_PATH)."<br>";
echo "Size of this dir is: ".get_dir_size($dir_to_scan_PATH)." bytes"."<br>";
echo "Number of files in this dir is: ".get_number_of_files_in_thisdir($dir_to_scan_PATH)."<br>";
//READ INFO
// function get_lastmtime_of_dir($dir_path): string{
//     $res = array();
//     // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
//     $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
//     $res = json_decode($sorted_dir_info_only_files);
//     return json_encode($res[0]->filemtime);
// }

// function get_firstmtime_of_dir($dir_path): string{
//     $res = array();
//     // SORTED TIMEMODIF/IED INFO OF THIS PATH ONLY FILES
//     $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
//     $res = json_decode($sorted_dir_info_only_files);
//     return json_encode($res[sizeof($res)-1]->filemtime);
// }
//..................
function get_firstmtime_of_dir($dir_path): string {
    $res = array();
    
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path));
    
    // Decode the JSON result safely
    $res = json_decode($sorted_dir_info_only_files);

    // Check if the result is an array and not empty
    if (is_array($res) && !empty($res)) {
        // Access the last element directly using the count of the array
        $last_index = count($res) - 1;
        $last_file = $res[$last_index];  // Directly access the last element
        
        // Check if the last element has the filemtime property
        if (isset($last_file->filemtime)) {
            return json_encode($last_file->filemtime);
        }

        else {
            return json_encode("filemtime property not found.");
        }
    }
    
     else {
        // Return a message if the array is empty or invalid
        return json_encode("No files found or invalid data.");
    }
}

//..................

function get_dir_size($dir_path): string{
    $res = array();
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
    $res_arr = json_decode($sorted_dir_info_only_files);
    $dir_size = 0;
    foreach($res_arr as $params){
        $dir_size += $params->filesize;
    }
    $res = $dir_size;
    return json_encode($res);
}
function get_number_of_files_in_thisdir($dir_path): string{
    $res = array();
    // SORTED TIMEMODIFIED INFO OF THIS PATH ONLY FILES
    $sorted_dir_info_only_files = (make_sorted_file_info($dir_path, scan_dir_sort_by_timemodified($dir_path)));
    $res = sizeof(json_decode($sorted_dir_info_only_files));
    return json_encode($res);
}

function getDirInfo($PATH): string{
    header('Content-type: application/json');
    return json_encode(dirToArray($PATH));
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
    $ignored = array('.', '..', '.svn', '.htaccess'); // -- ignore these file names
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
}

$folderPath = 'path/to/your/folder'; $files = array_diff(scandir($dir_to_scan_PATH ), array('.', '..')); 
foreach ($files as $file) { if (is_file($dir_to_scan_PATH . '/' . $file)) { echo $file . "<br>"; } }



//......
function get_lastmtime_of_dir($dir) {
    $last_mtime = filemtime($dir);  // Start with the directory's modification time
    $dir_handle = opendir($dir);

    // Loop through the files and subdirectories
    while (($file = readdir($dir_handle)) !== false) {
        // Skip . and .. entries
        if ($file == '.' || $file == '..') {
            continue;
        }
        
        $file_path = $dir . DIRECTORY_SEPARATOR . $file;
        
        // Get the modification time for files and directories
        if (is_dir($file_path)) {
            $mtime = get_lastmtime_of_dir($file_path);  // Recursively check subdirectories
        } else {
            $mtime = filemtime($file_path);
        }
        
        // Update last_mtime if the current file/directory has a newer timestamp
        if ($mtime > $last_mtime) {
            $last_mtime = $mtime;
        }
    }

    closedir($dir_handle);

    return $last_mtime;

    
}
?>