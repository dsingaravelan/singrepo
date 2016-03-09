<?php 
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\models\DownloadQuery;

use Illuminate\Http\Request;

class DownloadQueryController extends Controller {

	public function __construct()
	{
		$this->downLoadQueryObj = new DownloadQuery();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function getSqlFileName(Request $request)
	{
		$input = $request->input('filters');
		$sql_file_name = $this->downLoadQueryObj->createSqlFilterName($input);
		return $sql_file_name; 
	}

	/**
	 * fetches query based on the filter from view question.
	 *
	 * @param  questionType, tags, from, to, modified by, status
	 * @return Response
	 */
	public function downloadQuery(Request $request)
	{
		$input = $request->input('filters');
		$file  = $this->downLoadQueryObj->downloadQuery($input);
		if($file){
			return Response::json(array('file' =>$file, 'status'=>true, 'message'=>'file download success'),200);
		}
		else{
	        return Response::json(array('status' => false, 'message' => 'No Result Found'), 401);
		}
	}

	public function downloadIndividualQuery(Request $request)
	{
		$input 		 = $request->input('individualquery');
		$file 		 = $this->downLoadQueryObj->getInputTable($input);
		// $file 		 = $this->downLoadQueryObj->downloadQueryByType($input);

		if($file){
			return $file;
		}
		else{
			return '';
	        // return \Response::json(array('status' => false, 'message' => 'No Result Found'), 401);
		}
	}

	public function getSqlFileNameQueryMenu(Request $request)
	{
		$question_filters = array();
		$input = $request->input('search');
		return $this->downLoadQueryObj->getFileNameForQueryMenu($input);
	}

}
