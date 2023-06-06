<?php


namespace App\Services\Api\Selection;

use App\Models\Area;
use App\Models\Selection\SelectionAll;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Row;

class SelectionImport implements OnEachRow,WithChunkReading
{
    private $area;

    private $line = 1;

    private $type;
    private $year;
    private $yearKey;
    private $employerKey;
    private $employerCodeKey;
    private $postKey;
    private $postCodeKey;
    private $postCountKey;
    private $interviewProportionKey;
    private $sourceKey;
    private $sourceTextKey;
    private $postTypeKey;
    private $postLevelKey;
    private $workKey;
    private $educationKey;
    private $degreeKey;
    private $majorKey;
    private $majorLevelKey;
    private $registerKey;
    private $titleHighSchoolKey;
    private $titleWorldKey;
    private $qualificationHighSchoolKey;
    private $qualificationWorldKey;
    private $sexKey;
    private $politicalOutlookKey;
    private $otherKey;
    private $workProvinceKey;
    private $workCityKey;
    private $tellPhoneKey;
    private $driverTypeKey;
    private $directTypeKey;
    private $noticeKey;
    private $marriageKey;
    private $examinationKey;

    public function __construct($type)
    {
        $this->type = $type;
        $this->area = new Area();
    }

    public function onRow(Row $row)
    {
        set_time_limit(0);
        $this->selectionAll = new SelectionAll();
        //确定导入列表表头
        $info = $row->toArray();
        foreach ($info as &$v) {
            $v = filtrationStr($v, "\n");
        }
        if($this->line === 1){
            // 分配表头
            foreach ($info as $kk => $vv) {
                if($vv == '年份') {
                    $this->yearKey = $kk;
                }
                if($vv == '用人单位') {
                    $this->employerKey = $kk;
                }
                if($vv == '用人单位代码') {
                    $this->employerCodeKey = $kk;
                }
                if($vv == '招录岗位') {
                    $this->postKey = $kk;
                }
                if($vv == '岗位代码') {
                    $this->postCodeKey = $kk;
                }
                if($vv == '招录数量') {
                    $this->postCountKey = $kk;
                }
                if($vv == '面试比例') {
                    $this->interviewProportionKey = $kk;
                }
                if($vv == '来源类别') {
                    $this->sourceKey = $kk;
                }
                if($vv == '招聘来源') {
                    $this->sourceTextKey = $kk;
                }
                if($vv == '岗位类型') {
                    $this->postTypeKey = $kk;
                }
                if($vv == '岗位等级') {
                    $this->postLevelKey = $kk;
                }
                if($vv == '从事工作') {
                    $this->workKey = $kk;
                }
                if($vv == '学历要求') {
                    $this->educationKey = $kk;
                }
                if($vv == '学位要求') {
                    $this->degreeKey = $kk;
                }
                if($vv == '专业') {
                    $this->majorKey = $kk;
                }
                if($vv == '职业技能资格要求') {
                    $this->majorLevelKey = $kk;
                }
                if($vv == '户籍所在地要求') {
                    $this->registerKey = $kk;
                }
                if($vv == '高校职称') {
                    $this->titleHighSchoolKey = $kk;
                }
                if($vv == '社会人才职称') {
                    $this->titleWorldKey = $kk;
                }
                if($vv == '高校职业资格') {
                    $this->qualificationHighSchoolKey = $kk;
                }
                if($vv == '社会人才职业资格') {
                    $this->qualificationWorldKey = $kk;
                }
                if($vv == '性别') {
                    $this->sexKey = $kk;
                }
                if($vv == '政治面貌') {
                    $this->politicalOutlookKey = $kk;
                }
                if($vv == '工作地点1') {
                    $this->workProvinceKey = $kk;
                }
                if($vv == '工作地点2') {
                    $this->workCityKey = $kk;
                }
                if($vv == '咨询电话') {
                    $this->tellPhoneKey = $kk;
                }
                if($vv == '准驾车型') {
                    $this->driverTypeKey = $kk;
                }
                if($vv == '定向招考对象') {
                    $this->directTypeKey = $kk;
                }
                if($vv == '说明') {
                    $this->noticeKey = $kk;
                }
                if($vv == '重要说明') {
                    $this->noticeKey = $kk;
                }
                if($vv == '其他条件') {
                    $this->otherKey = $kk;
                }
                if($vv == '婚姻状况') {
                    $this->marriageKey = $kk;
                }
                if($vv == '考试科目') {
                    $this->examinationKey = $kk;
                }
            }
            $this->line++;
        } else {
            if($this->line == 2){
                $this->year = $info[$this->yearKey];
            }
            $this->selectionAll->type = $this->type;
            $this->selectionAll->year = $this->year;
            if($this->employerKey !== null) {
                $this->selectionAll->employer = $info[$this->employerKey];
            }
            if($this->employerCodeKey !== null) {
                $this->selectionAll->employer_code = $info[$this->employerCodeKey];
            }
            if($this->postKey !== null) {
                $this->selectionAll->post = $info[$this->postKey];
            }
            if($this->postCodeKey !== null) {
                $this->selectionAll->post_code = $info[$this->postCodeKey];
            }
            if($this->postCountKey) {
                $this->selectionAll->post_count = $info[$this->postCountKey];
            }
            if($this->interviewProportionKey) {
                if(!strstr($info[$this->interviewProportionKey], ':')
                && !strstr($info[$this->interviewProportionKey], '：')){
                    $this->selectionAll->interview_proportion = '1:'.$info[$this->interviewProportionKey];
                } else {
                    $this->selectionAll->interview_proportion = $info[$this->interviewProportionKey];
                }
            }
            if($this->sourceKey) {
                $this->selectionAll->source = $info[$this->sourceKey];
            }
            if($this->sourceTextKey) {
                $this->selectionAll->source_text = $info[$this->sourceTextKey];
            }
            if($this->postTypeKey) {
                $this->selectionAll->post_type = $info[$this->postTypeKey];
            }
            if($this->postLevelKey) {
                $this->selectionAll->post_level = $info[$this->postLevelKey];
            }
            if($this->workKey) {
                $this->selectionAll->work = $info[$this->workKey];
            }
            if($this->educationKey) {
                $this->selectionAll->education = $info[$this->educationKey];
            }
            if($this->degreeKey) {
                $this->selectionAll->degree = $info[$this->degreeKey];
            }
            if($this->majorKey) {
                $this->selectionAll->major = $info[$this->majorKey];
            }
            if($this->majorLevelKey) {
                $this->selectionAll->major_level = $info[$this->majorLevelKey];
            }
            if($this->registerKey) {
                $this->selectionAll->register = $info[$this->registerKey];
            }
            if($this->titleHighSchoolKey) {
                $this->selectionAll->title_high_school = $info[$this->titleHighSchoolKey];
            }
            if($this->titleWorldKey) {
                $this->selectionAll->title_world = $info[$this->titleWorldKey];
            }
            if($this->qualificationHighSchoolKey) {
                $this->selectionAll->qualification_high_school = $info[$this->qualificationHighSchoolKey];
            }
            if($this->qualificationWorldKey) {
                $this->selectionAll->qualification_world = $info[$this->qualificationWorldKey];
            }
            if($this->sexKey) {
                switch ($info[$this->sexKey]){
                    case '男':
                        $this->selectionAll->sex = 1;
                        break;
                    case '男性':
                        $this->selectionAll->sex = 1;
                        break;
                    case '女':
                        $this->selectionAll->sex = 2;
                        break;
                    case '女性':
                        $this->selectionAll->sex = 2;
                        break;
                    default:
                        $this->selectionAll->sex = 0;
                        break;
                }
            }
            if($this->politicalOutlookKey) {
                $this->selectionAll->political_outlook = $info[$this->politicalOutlookKey];
            }
            if($this->otherKey) {
                $this->selectionAll->other = $info[$this->otherKey];
            }
            if($this->workProvinceKey) {
                $this->selectionAll->work_province = $info[$this->workProvinceKey];
            }
            if($this->workCityKey) {
                $this->selectionAll->work_city = $info[$this->workCityKey];
            }
            if($this->tellPhoneKey) {
                $this->selectionAll->tell_phone = $info[$this->tellPhoneKey];
            }
            if($this->driverTypeKey) {
                $this->selectionAll->driver_type = $info[$this->driverTypeKey];
            }
            if($this->directTypeKey) {
                $this->selectionAll->direct_type = $info[$this->directTypeKey];
            }
            if($this->noticeKey) {
                $this->selectionAll->notice = $info[$this->noticeKey];
            }
            if($this->marriageKey) {
                switch ($info[$this->marriageKey]){
                    case '已婚':
                        $this->selectionAll->marriage = 1;
                        break;
                    case '未婚':
                        $this->selectionAll->marriage = 2;
                        break;
                    default:
                        $this->selectionAll->marriage = 0;
                        break;
                }
            }
            if($this->examinationKey) {
                $this->selectionAll->examination = $info[$this->examinationKey];
            }
            $this->selectionAll->save();
            $this->line++;
        }
    }

    //以50条数据基准切割数据
    public function chunkSize(): int
    {
        return 1000;
    }
}
