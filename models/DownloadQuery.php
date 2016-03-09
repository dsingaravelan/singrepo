<?php 
namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class DownloadQuery extends Model {

	public function downloadQuery($input)
	{
		DB::enableQueryLog();
		$date     = date('Y-m-d_H:i:s');
		$filename = '';

		$where = array('q.status'=>1);
        if(isset($input['id_question']) && $input['id_question']!=''){
            $where['q.id_question'] = $input['id_question'];
            $filename .= $this->editSqlFilename('Question_'.$input['id_question']);
        }
		if(!empty($input['question_type'])){
		    $where['q.question_type_id_question_type'] = $input['question_type'];
		    $get_question_type_name = DB::table('question_type')->where('id_question_type','=',$input['question_type'])->pluck('name');
		    $filename .= $this->editSqlFilename($get_question_type_name); 
		}
		if(!empty($input['id_tag'])){
		    $where['thq.tag_id_tag'] = $input['id_tag'];
            $get_tag_name = DB::table('tag')->where('id_tag','=',$input['id_tag'])->pluck('name');
            $filename .= '_'.$this->editSqlFilename($get_tag_name);
		}
		if(!empty($input['levelType'])){
		    $where['q.level'] = $input['levelType'];
            $filename .= '_'.$where['q.level'];
		}
		if(!empty($input['questionStatus']) && $input['questionStatus']!=-1 ){
		    $where['q.audited_status'] = $input['questionStatus'];
		}
		if(!empty($input['modified_by']) && $input['modified_by']!=-1){
		    $where['qu.user_id_user'] = $input['modified_by'];
            $get_user_name = DB::table('user')->where('id_user', '=', $input['modified_by'])->pluck('name');
            $filename .= '_'.$this->editSqlFilename($get_user_name);
		}
		if(!empty($input['source']) && $input['source']!=-1){
		    $where['q.id_source'] = $input['source'];
            $get_source_name = DB::table('source')->where('id_source','=',$input['source'])->pluck('name');
            $filename .= '_'.$this->editSqlFilename($get_source_name);
		}
    	$getQuestions = DB::table('question AS q')
    					->select('q.id_question','q.section_id_section','q.subsection_id_subsection', 'q.subsubsection_id_subsubsection','q.owner','q.blooms_taxonomy','q.question_type_id_question_type','q.level','q.question','q.comprehension_id_comprehension', 'q.audited_status','q.status','q.created_at','q.updated_at')
    					->leftJoin('question_uploader AS qu', 'qu.question_id_question', '=','q.id_question')
		    			->leftJoin('tag_has_question AS thq', 'thq.question_id_question', '=','q.id_question')
                        ->leftJoin('user AS u', 'u.id_user','=','qu.user_id_user');
		if(!empty($input['from'])){
		    $getQuestions->where('q.created_at','>=', date('Y-m-d H:i:s', strtotime(str_replace(' - ', ' ', $input['from']))));
		}
		if(!empty($input['to'])){
		    $getQuestions->where('q.updated_at', '<=', date('Y-m-d H:i:s', strtotime(str_replace(' - ', ' ', $input['to']))));
		}
    	$getQuestions = $getQuestions->where($where);
		$data = $getQuestions->get();

		if(!empty($data)){
			$path = storage_path().'/sql';
			if(!\File::exists($path)) {
				$makeDir = \File::makeDirectory($path, 0777, true);
			}
            if(empty($filename)){
                $filename = 'All_type_questions';
            }
            if(!empty($filename) && substr($filename, 0, 1) === '_'){
                $filename = substr($filename, 1);
            }
            $filename = $this->editSqlFilename($filename);
			$sqlFile  = $path.'/'.$filename.'_'.$date.'.sql';
			$handle   = fopen($sqlFile,'w+');  
			$finalSql = "SET NAMES utf8;\r\nSET AUTOCOMMIT = 0;\n";
			fwrite($handle, $finalSql);
		
            $comp_ids 		    = array();
            $id_questions 	    = array();
            $section_group      = array();
            $sections           = array();
            $tag_ids            = array();
            $question_type_ids  = array();
            $get_user_id        = array();
            $section_ids        = array();
            $subsubsection_ids  = array();

            /* get comprehension id */
            foreach ($data as $questions) {
                $que = (array) $questions;
                if($que['id_question']!=''){
                    $id_questions[] = $que['id_question'];
                }                
                if(isset($que['section_id_section']) && $que['section_id_section']!=''){
                    $section_ids[] = $que['section_id_section'];
                }
                if(isset($que['subsection_id_subsection']) && $que['subsection_id_subsection']!=''){
                    $subsection_ids[] = $que['subsection_id_subsection'];
                }
                if(isset($que['subsubsection_id_subsubsection']) && $que['subsubsection_id_subsubsection']!=''){
                    $subsubsection_ids[] = $que['subsubsection_id_subsubsection'];
                }
                if(isset($que['comprehension_id_comprehension']) && $que['comprehension_id_comprehension']!=''){
                    $comp_ids[] = $que['comprehension_id_comprehension'];
                }
                if(isset($que['question_type_id_question_type']) && $que['question_type_id_question_type']!=''){
                    $question_type_ids[] = $que['question_type_id_question_type'];
                }
            }
            /* question type records */
            $unique_q_type_ids = array_unique($question_type_ids);
            if(!empty($unique_q_type_ids)){
                foreach ($unique_q_type_ids as $q_type) {
                    $q_type = DB::table('question_type')->where('id_question_type','=',$q_type)->get();
                    $question_type = (array) $q_type[0];
                    $question_type_query = $this->createInsertStatement('question_type', $question_type);
                    fwrite($handle, $question_type_query);
                }
                fwrite($handle, "\n");
            }

            /* comprehension records */
            $unique_compIds = array_unique($comp_ids);
            if(!empty($unique_compIds)){
                foreach ($unique_compIds as $value) {
                    $get_comprehensions = DB::table('comprehension')->where('id_comprehension','=',$value)->first();
                    $comprehension = (array) $get_comprehensions;
                    // $comp_question_query = $this->createInsertUpdateStatement('comprehension', $comprehension, $comprehension);
                    $comp_question_query = $this->createInsertStatement('comprehension', $comprehension);
                    fwrite($handle, $comp_question_query);
                } // ends
                fwrite($handle, "\n");
            }
            $unique_id_questions = array_unique($id_questions);

            /* get section ids */
            foreach($unique_id_questions as $que_ids){
                $get_qs_records = DB::table('question_section')->where('question_id_question', '=', $que_ids)->first();
                $question_section = (array) $get_qs_records;
                if(!empty($question_section)){
                    if(isset($question_section['section_id_section']) && $question_section['section_id_section']!=''){
                       $sections[] = $question_section['section_id_section'];
                    }
                }
            } //ends

            /* section records */
            $unique_sections = array_unique($sections); 
            if(!empty($unique_sections)){
                foreach ($unique_sections as $section_id) {
                    $get_sections = DB::table('section')
                                      ->where('id_section','=', $section_id)
                                      ->first();
                    $sections     = (array) $get_sections;
                    // $section_query= $this->createInsertUpdateStatement('section', $sections, $sections);
                    $section_query= $this->createInsertStatement('section', $sections);
                    fwrite($handle, $section_query);
                } //ends
                fwrite($handle, "\n");
            }            


            /* get question records */
            foreach ($data as $questions) {
                $que = (array) $questions;
                // $question_query = $this->createInsertUpdateStatement('question', $que, $que);
                $question_query = $this->createInsertStatement('question', $que);
                fwrite($handle, $question_query);
            }
            fwrite($handle, "\n");

            /* question section records */
            if(!empty($unique_id_questions))
            {
                foreach($unique_id_questions as $que_ids){
                    $get_qs_records = DB::table('question_section')->where('question_id_question', '=', $que_ids)->first();
                    if(!empty($get_qs_records))
                    {
                        $question_section = (array) $get_qs_records;
                        if(!empty($question_section))
                        {
                            if(isset($question_section['section_id_section']) && $question_section['section_id_section']!='')
                            {
                               $sections[] = $question_section['section_id_section'];
                            }
                            // $question_section_query = $this->createInsertUpdateStatement('question_section', $question_section, $question_section);
                            $question_section_query = $this->createInsertStatement('question_section', $question_section);
                            fwrite($handle, $question_section_query);
                        }
                    }
                } //ends
                fwrite($handle, "\n");
            }

            /* get tag id  */
            if(!empty($unique_id_questions))
            {
                foreach($unique_id_questions as $que_ids){
                    $get_thq_records = DB::table('tag_has_question')
                                         ->where('question_id_question','=',$que_ids)
                                         ->first();
                    if(!empty($get_thq_records))
                    {
                        $thq_arr         = (array) $get_thq_records;
                        if(isset($thq_arr['tag_id_tag']) && $thq_arr['tag_id_tag']!=''){
                            $tag_ids[] = $thq_arr['tag_id_tag'];
                        }
                    }
                } //ends
            }

            /* tags records */
            $unique_tags = array_unique($tag_ids);
            if(!empty($unique_tags))
            {
                foreach ($unique_tags as $t_id) 
                {
                    $get_tag_records = DB::table('tag')->where('id_tag','=',$t_id)->first();
                    if(!empty($get_tag_records))
                    {
                        $tags            = (array) $get_tag_records;
                        unset($tags['description']);
                        // $tag_query       = $this->createInsertUpdateStatement('tag', $tags, $tags);
                        $tag_query       = $this->createInsertStatement('tag', $tags);
                        fwrite($handle, $tag_query);
                    }
                } //ends
                fwrite($handle, "\n");
            }

            /* get section group id */
            if(!empty($unique_sections))
            {
                foreach ($unique_sections as $section_id) 
                {
                    $get_sections_has_grp       = DB::table('section_group_has_section')
                                                    ->where('section_id_section','=', $section_id);
                    if(!empty($get_sections_has_grp))
                    {
                        if(isset($input['id_tag'])){
                            $get_sections_has_grp = $get_sections_has_grp->where('tag_id_tag','=',$input['id_tag']);
                        }
                        $get_sections_has_grp = $get_sections_has_grp->first();
                        $section_group_has_section  = (array) $get_sections_has_grp;
                        if(!empty($section_group_has_section['section_group_id_section_group']) && $section_group_has_section['section_group_id_section_group']!=0){
                           $section_group[] = $section_group_has_section['section_group_id_section_group'];
                        }
                    }
                } //ends
            }

            /* section group records */
            $unique_section_group = array_unique($section_group);
            if(!empty($unique_section_group))
            {
                foreach ($unique_section_group as $sec_group) 
                {
                    $get_secion_group    = DB::table('section_group')
                                             ->where('id_section_group','=',$sec_group)
                                             ->first();
                    if(!empty($get_secion_group))
                    {
                        $section_group       = (array) $get_secion_group;
                        // $section_group_query = $this->createInsertUpdateStatement('section_group', $section_group, $section_group);
                        $section_group_query = $this->createInsertStatement('section_group', $section_group);
                        fwrite($handle, $section_group_query);
                    }
                }
                fwrite($handle, "\n");
            } //ends

            /* section group has section records */
            if(!empty($unique_sections))
            {
                foreach ($unique_sections as $section_id) 
                {
                    $get_sections_has_grp       = DB::table('section_group_has_section')
                                                    ->where('section_id_section','=', $section_id);
                    if(!empty($get_sections_has_grp))
                    {
                        if(isset($input['id_tag']))
                        {
                            $get_sections_has_grp = $get_sections_has_grp->where('tag_id_tag','=',$input['id_tag']);
                        }
                        $get_sections_has_grp = $get_sections_has_grp->first();
                        $section_group_has_section  = (array) $get_sections_has_grp;
                        if(!empty($section_group_has_section['section_group_id_section_group']) && $section_group_has_section['section_group_id_section_group']!=0)
                        {
                           $section_group[] = $section_group_has_section['section_group_id_section_group'];
                        }
                        // $section_group_section_query = $this->createInsertUpdateStatement('section_group_has_section', $section_group_has_section, $section_group_has_section);
                        $section_group_section_query = $this->createInsertStatement('section_group_has_section', $section_group_has_section);
                        fwrite($handle, $section_group_section_query);
                    }
                } //ends
            fwrite($handle, "\n");
            }
            /* tag has question */
            if(!empty($unique_id_questions))
            {
                foreach($unique_id_questions as $que_ids)
                {
                    $get_thq_records = DB::table('tag_has_question')
                                         ->where('question_id_question','=',$que_ids)
                                         ->first();
                    if(!empty($get_thq_records))
                    {
                        $thq_arr         = (array) $get_thq_records;
                        unset($thq_arr['added_by']);
                        if(isset($thq_arr['tag_id_tag']) && $thq_arr['tag_id_tag']!=''){
                            $tag_ids[] = $thq_arr['tag_id_tag'];
                        }
                        // $thq_query       = $this->createInsertUpdateStatement('tag_has_question', $thq_arr, $thq_arr);
                        $thq_query       = $this->createInsertStatement('tag_has_question', $thq_arr);
                        fwrite($handle, $thq_query);
                    }
                } //ends
                fwrite($handle, "\n");
            }

            /* tag has question type record */
            if(!empty($unique_tags))
            {
                foreach ($unique_tags as $t_id) 
                {
                    $get_thqt_records = DB::table('tag_has_question_type')->where('tag_id_tag','=',$t_id)->get();
                    if(!empty($get_thqt_records))
                    {
                        $tag_has_que_type = (array) $get_thqt_records;
                        for ($j=0; $j < count($tag_has_que_type); $j++) { 
                            $get_thqt_arr = (array)$tag_has_que_type[$j];
                            if(!empty($get_thqt_arr['added_by']) && $get_thqt_arr['added_by']!=0 ){
                                $get_user_id[] = $get_thqt_arr['added_by'];
                            }
                            // $thqt_query   = $this->createInsertUpdateStatement('tag_has_question_type', $get_thqt_arr, $get_thqt_arr);
                            $thqt_query   = $this->createInsertStatement('tag_has_question_type', $get_thqt_arr);
                            fwrite($handle, $thqt_query);
                        }
                    }
                } //ends
                fwrite($handle, "\n");
            }

            $finalSql="COMMIT;\nSET AUTOCOMMIT = 1;\n";
            fwrite($handle,$finalSql);
            fclose($handle);

            if (file_exists($sqlFile)) {
                readfile($sqlFile); exit;
            }
		}
	}

    public function editSqlFilename($file_name)
    {
        $matches    = array('-', ' - ','/', ',', ' ', '.','__', '___');
        $replace    = array('_');
        $file_name  = str_replace($matches, $replace[0], $file_name);
        return $file_name;
    }
    public function getInputTable($input)
    {
        $question_filters = array();
        if($input['search_type']=='1'){
            $question_filters['id_question']=$input['search_string'];
        }
        elseif($input['search_type']=='2'){
            $tag_id = DB::table('tag')->where('name','=',trim($input['search_string']))->pluck('id_tag');   
            if(!empty($tag_id)){
                $question_filters['id_tag'] = $tag_id;
            }
        }
        elseif($input['search_type']=='3'){
            $id_question_type = DB::table('question_type')->where('name','=',trim($input['search_string']))->pluck('id_question_type');
            if(!empty($id_question_type)){
                $question_filters['question_type']=$id_question_type;
            }
        }
        elseif($input['search_type']=='4'){
            $id_source = DB::table('source')->where('name','=',trim($input['search_string']))->pluck('id_source');
            if(!empty($id_source)){
                $question_filters['source']=$id_source;
            }
        }
        return $this->downloadQuery($question_filters);
    }
    public function createSqlFilterName($input)
    {
        $filename = '';
        $date     = date('Y-m-d_H:i:s');
        if(isset($input['id_question']) && $input['id_question']!=''){
            $where['q.id_question'] = $input['id_question'];
            $filename .= $this->editSqlFilename('Question_'.$where['q.id_question']);
        }
        if(!empty($input['question_type'])){
            $get_question_type_name = DB::table('question_type')->where('id_question_type','=',$input['question_type'])->pluck('name');
            $filename .= $this->editSqlFilename($get_question_type_name);
        }
        if(!empty($input['id_tag'])){
            $get_tag_name = DB::table('tag')->where('id_tag','=',$input['id_tag'])->pluck('name');
            $filename .= '_'.$this->editSqlFilename($get_tag_name);
        }
        if(!empty($input['levelType'])){
            $filename .= '_'.$input['levelType'];
        }
        if(!empty($input['modified_by']) && $input['modified_by']!=-1){
            $get_user_name = DB::table('user')->where('id_user', '=', $input['modified_by'])->pluck('name');
            $filename .= '_'.$this->editSqlFilename($get_user_name);
        }
        if(!empty($input['source']) && $input['source']!=-1){
            $get_source_name = DB::table('source')->where('id_source','=',$input['source'])->pluck('name');
            $filename .= '_'.$this->editSqlFilename($get_source_name);
        }        
        $sql_file_name = $this->editSqlFilename($filename);

        if(!empty($sql_file_name) && substr($sql_file_name, 0, 1) === '_'){
            $sql_file_name = substr($sql_file_name, 1);
        }
        return $sql_file_name.'_'.$date.'.sql';
    }

    public function getFileNameForQueryMenu($input)
    {
        $question_filters = array();
        if($input['search_type']=='1'){
            $question_filters['id_question']=$input['search_string'];
        }
        elseif($input['search_type']=='2'){
            $tag_id = DB::table('tag')->where('name','=',trim($input['search_string']))->pluck('id_tag');   
            if(!empty($tag_id)){
                $question_filters['id_tag'] = $tag_id;
            }
        }
        elseif($input['search_type']=='3'){
            $id_question_type = DB::table('question_type')->where('name','=',trim($input['search_string']))->pluck('id_question_type');
            if(!empty($id_question_type)){
                $question_filters['question_type']=$id_question_type;
            }
        }
        elseif($input['search_type']=='4'){
            $id_source = DB::table('source')->where('name','=',trim($input['search_string']))->pluck('id_source');
            if(!empty($id_source)){
                $question_filters['source']=$id_source;
            }
        }

        $sql_file_name = $this->createSqlFilterName($question_filters);
        return $sql_file_name; 
    }
    
    public function downloadQueryByType($input)
    {
        DB::enableQueryLog();
        if($input['search_type']=='1'){
            $id_question = array();
            $id_question = explode(',',$input['search_string']);

            $get_question_by_type = DB::table('question')->whereIn('id_question', $id_question)->get();
            if(!empty($get_question_by_type)){
                foreach ($get_question_by_type as $get_question) {
                    $get_question = (array) $get_question;
                    echo $this->createInsertStatement('question', $get_question);
                }
            }
        }
        exit;
    }


}


