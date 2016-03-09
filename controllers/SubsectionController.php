<?php

namespace App\Http\Controllers;

use App\Model\Subsection;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubsectionController extends Controller
{
    public function create($id)
    {
    	return view('subsection.subsection')->with('id', $id);
    }
    public function store(Request $request)
    {
	    $input 			= $request->all();
    	$subsection_obj = new Subsection();
    	$subsections 	= $subsection_obj->createSubsection($input);
    	if($subsections){
            return \Redirect::to('section');
        }
    }

    public function edit($id)
    {
        $subsectionObj  = new Subsection();
        $subsection     = $subsectionObj->getSubsection($id);
        /*$subsection     = array();
        foreach($subsectionInfo as $subsections){
            $subsection = $subsections;
        }*/
        return view('subsection.edit')->with('subsection', $subsection);
    }

    public function update(Request $request)
    {
        $input          = $request->all();
        $subsectionObj  = new Subsection();
        $update         =  $subsectionObj->updateSubsection($input);
        if($update){
            return \Redirect::to('section');
        }else{
            $subsectionInfo = $subsectionObj->getSubsection($id);
            $subsection     = array();
            foreach($subsectionInfo as $subsections){
                $subsection = $subsections;
            }
            return view('subsection.edit')->with('subsection', $subsection);
        }
    }
    public function destroy($id)
    {
        $deleteSubsectionObj = new Subsection();
        $deleteSection    = $deleteSubsectionObj->deleteSubsection($id);
        if($deleteSection){
            return \Redirect::to('section');
        }
    }
}
