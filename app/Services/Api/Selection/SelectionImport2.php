<?php


namespace App\Services\Api\Selection;

use App\Models\Area;
use App\Models\Selection\SelectionCivilService;
use App\Models\Selection\SelectionDirectType;
use App\Models\Selection\SelectionEducation;
use App\Models\Selection\SelectionEmployer;
use App\Models\Selection\SelectionPoliticalOutlook;
use App\Models\Selection\SelectionSkill;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class SelectionImport2 implements OnEachRow,WithChunkReading
{
    private $type;
    private $name;
    private $selectionCivilService;
    private $selectionDirectType;
    private $selectionEducation;
    private $selectionEmployer;
    private $selectionPoliticalOutlook;
    private $selectionSkill;
    private $area;

    public function __construct($type, $name)
    {
        $this->type = $type;
        $this->name = $name;
        $this->area = new Area();
    }

    public function onRow(Row $row)
    {
        set_time_limit(0);
        $this->selectionCivilService = new SelectionCivilService();
        $this->selectionDirectType = new SelectionDirectType();
        $this->selectionEducation = new SelectionEducation();
        $this->selectionEmployer = new SelectionEmployer();
        $this->selectionPoliticalOutlook = new SelectionPoliticalOutlook();
        $this->selectionSkill = new SelectionSkill();
        //确定导入列表表头
        $info = $row->toArray();
        if ($info[1]) {
            foreach ($info as &$v) {
                $v = filtrationStr($v, "\n");
            }
            // 技能岗数据
            if ($this->type === 1) {
                // 地点转换-户籍地址
                $register_code = $this->area->where('name',$info[13])->value('id')?$this->area->where('name',$info[13])->value('id'):0;
                // 地点转换-工作地址-省
                $work_province_code = 0;
                if ($info[17]) {
                    $work_province_code = $this->area->where('city_id', 0)->where('province_alias_name', 'like', '%'.$info[17].'%')->value('id')?$this->area->where('city_id', 0)->where('province_alias_name', 'like', '%'.$info[17].'%')->value('id'):0;
                }
                // 地点转换-工作地址-市
                $work_city_code = 0;
                if ($info[18]) {
                    $work_city_code = $this->area->where('area_id', 0)->where('city', 'like', '%'.$info[18].'%')->value('id')?$this->area->where('area_id', 0)->where('city', 'like', '%'.$info[18].'%')->value('id'):0;
                }
                // 表名称
                $this->selectionSkill->table_id = $this->name;
                // 用人单位
                if (!$this->selectionEmployer->where(['name' => $info[1], 'province_code' => $work_province_code, 'city_code' => $work_city_code])->exists()) {
                    $this->selectionEmployer->name = $info[1];
                    $this->selectionEmployer->province_code = $work_province_code;
                    $this->selectionEmployer->city_code = $work_city_code;
                    $this->selectionEmployer->save();
                }
                $this->selectionSkill->employer_id = $this->selectionEmployer->where(['name' => $info[1], 'province_code' => $work_province_code, 'city_code' => $work_city_code])->value('id');
                // 招录岗位
                $this->selectionSkill->post = $info[2];
                // 招录岗位代码
                $this->selectionSkill->post_code = $info[3];
                // 招录数量
                $this->selectionSkill->post_count = $info[4];
                // 面试比例
                $this->selectionSkill->interview_proportion = $info[5];
                // 岗位类型
                $this->selectionSkill->post_type = $info[6];
                // 岗位等级
                $this->selectionSkill->post_level = $info[7];
                // 工作内容
                $this->selectionSkill->work = $info[8];
                // 学历要求
                if (!$this->selectionEducation->where(['name' => $info[9]])->exists()) {
                    $this->selectionEducation->name = $info[9];
                    $this->selectionEducation->save();
                }
                $this->selectionSkill->education_id = $this->selectionEducation->where(['name' => $info[9]])->value('id');
                // 专业
                $this->selectionSkill->major = $info[10];
                // 专业要求
                $this->selectionSkill->major_level = $info[11];
                // 户籍地址
                $this->selectionSkill->register_code = $register_code;
                // 性别
                $sex = 0;
                if ($info[14]) {
                    $sex = $info[14] == '男' ? 1 : 2;
                }
                $this->selectionSkill->sex = $sex;
                // 政治面貌
                $this->selectionSkill->political_outlook = $info[15];
                // 其他条件
                $this->selectionSkill->other = $info[16];
                // 工作地点
                $this->selectionSkill->work_province_code = $work_province_code;
                $this->selectionSkill->work_city_code = $work_city_code;
                // 咨询电话
                $this->selectionSkill->tell_phone = $info[19];
                // 准驾车型
                $this->selectionSkill->driver_type = $info[20];
                // 定向招考类型
                $this->selectionSkill->direct_type = $info[21];
                // 重要说明
                $this->selectionSkill->notice = $info[22];
                // 婚姻情况
                $marriage = 0;
                if ($info[23]) {
                    $marriage = $info[23] == '已婚' ? 1 : 2;
                }
                $this->selectionSkill->marriage = $marriage;
                // 考试科目
                $this->selectionSkill->examination = $info[24];
                // 招聘来源
                $this->selectionSkill->source_text = $info[25];
                $this->selectionSkill->save();
            } else {
                // 文职数据
                // 地点转换-工作地址-省
                $work_province_code = 0;
                if ($info[19]) {
                    $work_province_code = $this->area->where('city_id', 0)->where('province_alias_name', 'like', '%'.$info[19].'%')->value('id')?$this->area->where('city_id', 0)->where('province_alias_name', 'like', '%'.$info[19].'%')->value('id'):0;
                }
                // 地点转换-工作地址-市
                $work_city_code = 0;
                if ($info[20]) {
                    $work_city_code = $this->area->where('area_id', 0)->where('city', 'like', '%'.$info[20].'%')->value('id')?$this->area->where('area_id', 0)->where('city', 'like', '%'.$info[20].'%')->value('id'):0;
                }

                // 表名称
                $this->selectionCivilService->table_id = $this->name;
                // 用人单位
                if (!$this->selectionEmployer->where(['name' => $info[2], 'province_code' => $work_province_code, 'city_code' => $work_city_code])->exists()) {
                    $this->selectionEmployer->name = $info[2];
                    $this->selectionEmployer->code = $info[1];
                    $this->selectionEmployer->province_code = $work_province_code;
                    $this->selectionEmployer->city_code = $work_city_code;
                    $this->selectionEmployer->save();
                }
                $this->selectionCivilService->employer_id = $this->selectionEmployer->where(['name' => $info[2], 'province_code' => $work_province_code, 'city_code' => $work_city_code])->value('id');

                // 招录岗位
                $this->selectionCivilService->post = $info[4];
                // 招录岗位代码
                $this->selectionCivilService->post_code = $info[0];
                // 招录数量
                $this->selectionCivilService->post_count = $info[6];
                // 面试比例
                $this->selectionCivilService->interview_proportion = $info[7];
                // 岗位类型
                $this->selectionCivilService->post_type = $info[3];
                // 工作内容
                $this->selectionCivilService->work = $info[5];
                // 来源类别
                $source = 0;
                if ($info[8]) {
                    switch ($info[8]) {
                        case '高校毕业生':
                            $source = 1;
                            break;
                        case '社会人才':
                            $source = 2;
                            break;
                        case '高校毕业生或社会人才':
                            $source = 3;
                            break;
                        default:
                            $source = 0;
                            break;
                    }
                }
                $this->selectionCivilService->source = $source;
                // 学历要求
                if (!$this->selectionEducation->where(['name' => $info[9]])->exists()) {
                    $this->selectionEducation->name = $info[9];
                    $this->selectionEducation->save();
                }
                $this->selectionCivilService->education_id = $this->selectionEducation->where(['name' => $info[9]])->value('id');
                // 学位要求
                $this->selectionCivilService->degree = $info[10];
                // 专业
                $this->selectionCivilService->majors = $info[11].$info[12];
                // 高校职称
                $this->selectionCivilService->title_high_school = $info[14];
                // 社会人才职称
                $this->selectionCivilService->title_world = $info[15];
                // 高校职业资格
                $this->selectionCivilService->qualification_high_school = $info[16];
                // 社会人才职业资格
                $this->selectionCivilService->qualification_world = $info[17];
                // 其他条件
                $this->selectionCivilService->other = $info[18];
                // 工作地点
                $this->selectionCivilService->work_province_code = $work_province_code;
                $this->selectionCivilService->work_city_code = $work_city_code;
                // 咨询电话
                $this->selectionCivilService->tell_phone = $info[21];
                $this->selectionCivilService->save();
            }
        }
    }

    //以50条数据基准切割数据
    public function chunkSize(): int
    {
        return 500;
    }
}
