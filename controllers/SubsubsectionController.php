<?php

namespace App\Http\Controllers;

use App\Model\Subsubsection;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubsubsectionController extends Controller
{
	public function create($id)
	{
		return view('subsubsection.index')->with('id',$id); //view('subsubsection.index');
	}

	public function store(Request $request)
	{
		$input = $request->all();
		$subsubsectionObj = new Subsubsection();
		$createSubsubsection = $subsubsectionObj->createSubsection($input);
		if($createSubsubsection)
		{
			return \Redirect::to('section');
		}
	}
	public function edit($id)
	{
		$subsubsectionObj 	 = new Subsubsection();
		$subsubsection 		 = $subsubsectionObj->editSubsubsection($id);
		return view('subsubsection.edit')->with('subsubsections', $subsubsection);
	}
	public function update(Request $request)
	{
		$input = $request->all();
		$subsubsectionObj 	 = new Subsubsection();
		$updateSubsubsection = $subsubsectionObj->updateSubsubsection($input);
		if($updateSubsubsection)
		{
			return \Redirect::to('section');
		}
	}
	public function destroy($id)
	{
		$subsubsectionObj 	 = new Subsubsection();
		$deleteSubsubsection = $subsubsectionObj->deleteSubsubsection($id);
		if($deleteSubsubsection)
		{
			return \Redirect::to('section');
		}
	}
}
