<?php

namespace App\Http\Controllers;

use App\models\Tags;
use App\Model\User;
use App\Http\Controllers\UserControllers;
use Illuminate\Http\Request;
use \Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TagsController extends Controller
{
    public function __construct(Request $request) {
        //Getting the user details
        //$userDet = Session::get('userDet');
        $userDet = $request->userDet;
        if (!empty($userDet)) {
            $user_id = $userDet['user_id'];
            $this->user_id = $user_id;
            $role_id_role = $userDet['role_id_role'];
            $this->role_id_role = $role_id_role;
            $role = $userDet['role'];
            $this->role = $role;
            //$this->college_id                     =   $userDet[0]['id_college']; 
        }

        $this->curr_date = date('y-m-d H:i:s');
        \DB::enableQueryLog();
        
        $this->objTags  = new Tags();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {        
        $tags    = $this->objTags->getTags($this->role);        
        if($tags)
            return Response::json(array('tags' => $tags, 'message' => 'Result Found'), 200);
        else
            return Response::json(array('tags' => array(), 'message' => 'No Records Found'), 200);        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input      = $request->input('tag');
        //$duplicate  = $this->objTags->checkDuplicateTag($input['question_type'], $id_tag='', $input['name']);
        $duplicate  = $this->objTags->checkDuplicateTag($id_tag='', $input['name'], $this->role);
        //print_r($duplicate);exit;
        // if($duplicate){
        if($duplicate){
            // foreach ($duplicate as $duplicateTag) {
            //     $qType = $duplicateTag->question_type_id_question_type;
            // }
            // $message = "Question group already exists for the question type ". $this->objTags->getTypeName($qType);
            $message = "Question group already exists";
            return Response::json(array('status' => false, 'message' => $message), 200);
        }else{
            $createTag  = $this->objTags->createTag($input);
            if($createTag)
                return Response::json(array('status' => $createTag, 'message' => 'Question group created successfully'), 200);
            else
                return Response::json(array('status' => $createTag, 'message' => 'Question group creation unsuccessfull'), 200);
        }
        /*$input      = $request->all();
        $tagObj     = new tags();
        $createTag  = $tagObj->createTag($input);
        if($createTag)
        {
            return \Redirect::to('tags');
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
        $tagObj      = new Tags();
        $editTag     = $tagObj->editTag($id);
        $userObj     = new UserController();
        /*$userDetails = $userObj->login();
        print_r($userDetails);*/
        return view('tags.edit')->with('tags', $editTag);
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
        $input       = $request->input('tag');
        $duplicate   = $this->objTags->checkDuplicateTag($input['questionType'], $input['id_tag'], $input['tagName']);
        // if(!empty($duplicate)){
        //     foreach ($duplicate as $duplicateTag) {
        //         $qType = $duplicateTag->question_type_id_question_type;
        //     }
        if($duplicate){
            //$message = "Question group already exists for the question type ". $this->objTags->getTypeName($qType);
            $message = "Question group already exists";
            return Response::json(array('status' => false, 'message' => $message), 200);
        }
        else{
            $editTag     = $this->objTags->updateTag($input);
            if($editTag)
                return Response::json(array('status' => $editTag, 'message' => 'Question group edited successfully'), 200);
            else
                return Response::json(array('status' => $editTag, 'message' => 'No Changes done'), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $input       = $request->input('tag');
        $deleteTag     = $this->objTags->deleteTag($input);
        if($deleteTag){
            return Response::json(array('status' => $deleteTag, 'message' => 'Question group deleted successfully'), 200);
        }
    }
    /**
     * get the resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getTagDetails($idQuestionType)
    {
        $tags = $this->objTags->getQuestionTags($idQuestionType,$this->role);
        if ($tags)
            return Response::json(array('tag_list' => $tags), 200);
        else
            return Response::json(array('tag_list' => array(), 'message' => 'No Records Found'), 404);
    }
    public function getTypeFromTag($idTag)
    {
        $types = $this->objTags->getQuestionTypesFromTags($idTag);
        if ($types)
            return Response::json(array('types_list' => $types), 200);
        else
            return Response::json(array('types_list' => array(), 'message' => 'No Records Found'), 404);
    }
    public function getQuestionType()
    {
        $questionType = $this->objTags->getQuestionType();
        if($questionType)
            return Response::json(array('question_type'=>$questionType, 'message' => "Result Found"),200);
        else
            return Response::json(array('question_type'=>$questionType, 'message' => "No result found"),200);
    }
    public function getTagsForViewQuestions($idQuestionType)
    {
        //
        $tags_viewQuestions = $this->objTags->getTagsForViewQuestions($idQuestionType,$this->role);
        if ($tags_viewQuestions)
            return Response::json(array('tag_list_viewQuestions' => $tags_viewQuestions), 200);
        // else
        //     return Response::json(array('tag_list_viewQuestions' => array(), 'message' => 'No Records Found'), 404);
    }
    public function getTagsForCopyQuestion(Request $request)
    {
        //**$idQuestionType=$request->input('idQuestionType');
        $idQuestion=$request->input('idQuestion');
        //**$tags_copyQuestions = $this->objTags->getTagsForCopyQuestion($idQuestionType,$idQuestion);
        $tags_copyQuestions = $this->objTags->getTagsForCopyQuestion($idQuestion,$this->role);
        if ($tags_copyQuestions)
            return Response::json(array('tag_list_copyQuestions' => $tags_copyQuestions), 200);
        else
            return Response::json(array('tag_list_copyQuestions' => array(), 'message' => 'No Records Found'), 404);
    }
    public function copyQuestionToAnotherTag(Request $request)
    {
        // print_r($request);
        // exit;
        $copyTagId=$request->input('copyTagId');
        $copyQuestionTypeId=$request->input('copyQuestionTypeId');
        $idQuestion=$request->input('idQuestion');
        $tags_copyQuestions = $this->objTags->copyQuestionToAnotherTag($copyTagId,$copyQuestionTypeId,$idQuestion,$this->user_id);
        if ($tags_copyQuestions)
            return Response::json(array('tag_list_copyQuestions' => $tags_copyQuestions), 200);
        else
            return Response::json(array('tag_list_copyQuestions' => array(), 'message' => 'No Records Found'), 404);
    }
    public function getTagNameFromTagId(Request $request)
    {
        $tagId=$request->input('tagId');
        $tagName = $this->objTags->getTagNameFromTagId($tagId);
        if ($tagName)
            return Response::json(array('tagName' => $tagName), 200);
        // else
        //     return Response::json(array('tagName' => array(), 'message' => 'No Records Found'), 404);
    }
    public function getTagsCountForQuestion($idQuestion)
    {
        //
        $tags_countQuestions = $this->objTags->getTagsCountForQuestion($idQuestion,$this->role);
        if ($tags_countQuestions)
            return Response::json(array('tags_countQuestions' => $tags_countQuestions), 200);
        // else
        //     return Response::json(array('tag_list_viewQuestions' => array(), 'message' => 'No Records Found'), 404);
    }
}
