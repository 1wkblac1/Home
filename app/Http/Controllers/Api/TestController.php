<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Selection\SelectionTable;
use App\Services\Api\Selection\SelectionImport;
use Maatwebsite\Excel\Facades\Excel;
class TestController extends Controller
{
    /*
     * 选岗导入三期期
     */
    public function test() {
        set_time_limit(0);
        $type = 1; // 1 技能 2 文职
        $nameNum = request()->get('number');
        $name = $nameNum.".xlsx"; // 文件名称

        $path = "F:\\桌面\\import\\".$name; // 文件路径

        Excel::import(new SelectionImport($type),$path);
        dd('success'.$nameNum);
    }
    /*
     * 选岗导入二期
     */
//    public function test() {
//        set_time_limit(0);
//        $type = 2; // 1 技能 2 文职
//
//        $name = "文职.xlsx"; // 文件名称
//
//        $path = __DIR__.'\\'.$name; // 文件路径
//
//        $selectionTable = new SelectionTable();
//
//        $selectionTable->name = $name;
//        $selectionTable->save();
//
//        Excel::import(new SelectionImport($type, $selectionTable->id),$path);
//        dd('success');
//    }


    /***
     * 导入选岗数据
    public function test() {

    $list = $this->getDataFromFile(dirname(__FILE__).'/hunan');
    DB::beginTransaction();
    try {
    foreach ($list as $k => $v){
    $department_province_code = null;
    $department_city_code = null;
    if(is_array($v['department']['districts']) && count($v['department']['districts']) == 1){
    $department_province_code = $v['department']['districts'][0]['code'];
    $department_city_code = null;
    }
    if(is_array($v['department']['districts']) && count($v['department']['districts']) == 2){
    $department_province_code = $v['department']['districts'][0]['code'];
    $department_city_code = $v['department']['districts'][1]['code'];
    }
    $v['category'] = isset($v['category']) ? json_encode($v['category']):json_encode([]);
    $v['subject'] = isset($v['subject']) ? json_encode($v['subject']):json_encode([]);
    $majors_remark = '';
    if(isset($v['majors'])) {
    foreach ($v['majors'] as $mv) {
    $majors_remark .= $mv['name'];
    }
    }

    $id = (new Selection())->insertGetId([
    'year'  => $v['post_year'],
    'code'  => $v['code'],
    'name'  => $v['name'],
    'department_code'  => $v['department']['code'],
    'department_name'  => $v['department']['name'],
    'department_tel'  => json_encode($v['department']['tel']),
    'department_province_code'  => $department_province_code,
    'department_city_code'  => $department_city_code,
    'addition'  => $v['addition'],
    'work_content'  => $v['work_content'],
    'demand_quantity'  => $v['demand_quantity'],
    'qualified_quantity'  => $v['qualified_quantity'],
    'score_line'  => $v['score_line'],
    'category'  => $v['category'],
    'subject'  => $v['subject'],
    'majors'  => isset($v['majors'])?json_encode($v['majors']):[],
    'major_remark'  => $majors_remark,
    'tags'  => json_encode($v['tags']),
    'new_tags'  => json_encode($v['new_tags']),
    'register_count'  => $v['register_count'],
    ]);
    $condition[] = [
    'selection_id' => $id,
    'gender' => $v['condition']['gender'],
    'source' => $v['condition']['source'],
    'college_level' => $v['condition']['college_level'],
    'degree' => $v['condition']['degree'],
    'degree_limit' => $v['condition']['degree_limit'],
    'degree_level' => $v['condition']['degree_level'],
    'degree_level_limit' => $v['condition']['degree_level_limit'],
    'work_year' => $v['condition']['work_year'],
    'party_member' => $v['condition']['party_member'],
    'relation' => $v['condition']['relation'],
    'profession' => $v['condition']['profession'],
    'local' => $v['condition']['local'],
    ];
    }
    (new Condition())->insert($condition);
    DB::commit();
    } catch (\Exception $e) {
    DB::rollBack();
    dd($e->getMessage());
    }
    echo 'success';

    }

    // 获得文件内容
    public function getDataFromFile($filePath, $length = 2000) {
    $list = [];
    $temp_list = scandir($filePath);
    if(count($temp_list) <= 2){
    echo 'end';
    die;
    }
    foreach ($temp_list as $k => $v) {
    if(strstr($v,'txt')) {
    $arr = json_decode(file_get_contents($filePath.'/'.$v), true);
    $list[] = $arr['post']; // 选岗数据
    }
    if(count($list) >= $length) {
    break;
    }
    }
    return $list;
    }
     ***/
}
