<?php

namespace App\Http\Controllers;

use App\models\Section;
use App\models\Subsection;
use \Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SectionController extends Controller
{
    public function __construct(Request $request) {
        //Getting the user details
        //$userDet = Session::get('userDet');
        $userDet = $request->userDet;
        if (!empty($userDet)) {
            $user_id = $userDet['user_id'];
            $this->user_id = $user_id;
            //$this->college_id                     =   $userDet[0]['id_college']; 
        }

        $this->curr_date = date('y-m-d H:i:s');
        \DB::enableQueryLog();
        
        $this->objSection  = new Section();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sections = $this->objSection->getSections();
        
        $sections_subsection = $this->objSection->getSubsection();

        $sections_subsubsection = $this->objSection->getSubsubsection();
        
        if ($sections && $sections_subsection)
            return Response::json(array('sections' => $sections, 'subsections' => $sections_subsection, 'subsubsections' => $sections_subsubsection, 'message' => 'Result Found'), 200);
        else
            return Response::json(array('sections' => array(), 'subsections' => array(), 'message' => 'No Records Found'), 200);    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('section.section');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input('section');
        $sectionObj = new Section;
        // $duplicateSection = $sectionObj->checkDuplicate('section', 'name', $input['section_name'], 'id_section');
        $duplicateSection = $sectionObj->checkDuplicate($input);
        if(!empty($duplicateSection)){
            return Response::json(array('status' => false, 'message' => 'Section already exists'), 200);
        }else{
            $section    = $sectionObj->createSection($input);
            if($section){
                return Response::json(array('status' => true, 'message' => 'Section created Successfully'), 200);
               
            }
        }

        // return $section;
        /*if(Section::create($input)){
            return 'Section created successfully'; 
        }else{
            return 'Check your input and try again!!!';
        }*/
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editSectionObj     = new Section();
        $section            = $editSectionObj->editSection($id);
        // $sectionInfo    = array();
        /*foreach($editSectionInfo as $editSection){
            $section = $editSection;
        }*/
        return view('section.edit')->with('section',$section);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input            = $request->input('section');
        $updateSectionObj = new Section();
        $checkDuplicate     = $updateSectionObj->checkDuplicate($input);
        if(!empty($checkDuplicate)){
            return Response::json(array('status'=>false,"message"=>"Section already Exist"), 200);
        }else{
            $updateSection    = $updateSectionObj->updateSection($input);

            if($updateSection)
                return Response::json(array('status'=>true,"message"=>"Updated Successfully"), 200);
            else
                return Response::json(array('status'=>true,"message"=>"No changes done"), 200);
        }
        /*if($updateSection){
            return \Redirect::to('section'); //$this->index(); //view('section.index');
        }else{
            $editSectionInfo    = $updateSectionObj->editSection($input->id_section);
            foreach($editSectionInfo as $editSection){
                $section = $editSection;
            }
            return view('section.edit')->with('section',$section);
        }*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $input            = $request->input('section');
        $deleteSectionObj = new Section();
        $deleteSection    = $deleteSectionObj->deleteSection($input);
        if($deleteSection){
            return Response::json(array('status' => $deleteSection, 'message' => "Section deleted successfully"), 200);
            // return \Redirect::to('section');
        }else{
            return Response::json(array('status' => $deleteSection, 'message' => "Unable to delete section"), 200);
        }
    }

    public function getSubsubsection(Request $request){
        $id=$request->input('subsection');
        $subsubsections = \DB::table('subsubsection')->where('subsection_id_subsection',$id)->get();
        return $subsubsections;
    }

    public function getSectionDetails(Request $request){
        $sectionDetails = $this->objSection->getSectionDetails();
        if($sectionDetails)
            return Response::json($sectionDetails, 200);
        else
            return Response::json(array('message' => 'No Records Found'), 404);
        //return $sectionDetails;
    }
    public function getSubSection(Request $request){
        $subSectionDetails = $this->objSection->getSubSectionDetails();
        if($subSectionDetails)
            return Response::json($subSectionDetails, 200);
        else
            return Response::json(array('message' => 'No Records Found'), 404);        
    }
    public function getSubSubSectionDetails(Request $request){
        $subSubSectionDetails = $this->objSection->getSubSubSectionDetails();
        if($subSubSectionDetails)
            return Response::json($subSubSectionDetails, 200);
        else
            return Response::json(array('message' => 'No Records Found'), 404);        
    }
    public function addSubSection(Request $request){
        $input = $request->input('subsection');
        $duplicateSubsection = $this->objSection->checkDuplicateSubsection($input);
        if(!empty($duplicateSubsection)){
            foreach ($duplicateSubsection as $duplicateSec) {
                $sectionId = $duplicateSec->section_id_section;
            }
            $message = "Subsection already exists for the section ".$this->objSection->getSecName($sectionId);
            return Response::json(array('status' => false, 'message' => $message), 200);
        }
        else{
            $subSectionId = $this->objSection->addSubSection($input['section_id_section'],$input['subsection']);
            if($subSectionId)
                return Response::json(array('status' => true, 'message' => "Subsection created successfully"), 200);
            else
                return Response::json(array('status' => false, 'message' => "Subsection creation failed"), 200);
        }
    }
    public function addSubSubSection(Request $request){
        $input           = $request->input('subsubsection');
        $duplicateSubsubsection = $this->objSection->checkDuplicateSubsubsection($input);
        if(!empty($duplicateSubsubsection)){
            foreach($duplicateSubsubsection as $duplicateName)
            {
                $id_section    = $duplicateName->section_id_section;
                $id_subsection = $duplicateName->subsection_id_subsection;
            }
            $sectionName    = $this->objSection->getSecName($id_section);
            $subSectionName = $this->objSection->getSubSecName($id_subsection);
            $message        = "Subsubsection already exist for the section ".$sectionName." & subsection ".$subSectionName;
            
            return Response::json(array('status'=>false,"message"=>$message), 200);
        }else{
            $subSubSection = $this->objSection->addSubSubSection($input);
            
            if($subSubSection)
               return Response::json(array('status' => $subSubSection, 'message' => "Subsubsection Created successfully"), 200);
            else
               return Response::json(array('status' => $subSubSection, 'message' => "unable to create Subsubsection"), 200);
        }
    }
    
    public function deleteSubsection(Request $request)
    {
        $input               = $request->input('subsection');
        $deleteSubSectionObj = new Section();
        $deleteSubSection    = $deleteSubSectionObj->deleteSubSection($input);
        if($deleteSubSection){
            return Response::json(array('status' => $deleteSubSection, 'message' => "Subsection deleted successfully"), 200);
            // return \Redirect::to('section');
        }else{
            return Response::json(array('status' => $deleteSubSection, 'message' => "Unable to delete subsection"), 200);
        }
    }
    public function editSubSection(Request $request)
    {
        $input               = $request->input('subsection');
        $updateSubSectionObj = new Section();
        $duplicateSubsection = $this->objSection->checkDuplicateSubsection($input);
        if(!empty($duplicateSubsection)){
            foreach ($duplicateSubsection as $duplicateSec) {
                $sectionId = $duplicateSec->section_id_section;
            }
            $message = "Subsection already exists for the section ".$this->objSection->getSecName($sectionId);
            return Response::json(array('status' => false, 'message' => $message), 200);
        }
        else{
            $updateSubSection    = $updateSubSectionObj->updateSubSection($input);
            if($updateSubSection)
                return Response::json(array('status'=>$updateSubSection,"message"=>"Updated Successfully"), 200);
            else
                return Response::json(array('status'=>$updateSubSection,"message"=>"No changes done"), 200);
        }
    }
    public function deleteSubSubSection(Request $request)
    {
        $input                  = $request->input('subsubsection');
        $deleteSubSubSectionObj = new Section();
        $deleteSubSubSection    = $deleteSubSubSectionObj->deleteSubSubSection($input);
        if($deleteSubSubSection){
            return Response::json(array('status' => $deleteSubSubSection, 'message' => "Subsubsection deleted successfully"), 200);
            // return \Redirect::to('section');
        }else{
            return Response::json(array('status' => $deleteSubSubSection, 'message' => "Unable to delete subsubsection"), 200);
        }
    }
    public function editSubSubSection(Request $request)
    {
        $input                  = $request->input('subsubsection');
        $duplicateSubsubsection = $this->objSection->checkDuplicateSubsubsection($input);
        if(!empty($duplicateSubsubsection)){
            foreach($duplicateSubsubsection as $duplicateName){
                $id_section    = $duplicateName->section_id_section;
                $id_subsection = $duplicateName->subsection_id_subsection;
            }
            $sectionName    = $this->objSection->getSecName($id_section);
            $subSectionName = $this->objSection->getSubSecName($id_subsection);
            $message        = "Subsubsection already exist for the section ".$sectionName." & subsection ".$subSectionName;
            return Response::json(array('status'=>false,"message"=>$message), 200);
        }else{
            $updateSubSubSectionObj = new Section();
            $updateSubSubSection    = $updateSubSubSectionObj->updateSubSubSection($input);
            if($updateSubSubSection)
                return Response::json(array('status'=>$updateSubSubSection,"message"=>"subsubsection Updated Successfully"), 200);
            else
                return Response::json(array('status'=>$updateSubSubSection,"message"=>"No changes done"), 200);
        }
    }
}
