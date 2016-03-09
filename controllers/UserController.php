<?php namespace App\Http\Controllers;
use App\models\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Response;
use Illuminate\Http\Request;

class UserController extends Controller {

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
        
        $this->objUser  = new User();
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{		
        $getUser     = $this->objUser->getUser();
        if ($getUser)
            return Response::json(array('user_list' => $getUser, 'message' => 'Result Found'), 200);
        else
            return Response::json(array('user_list' => array(), 'message' => 'No Records Found'), 200);            
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
	public function store(Request $request)
	{
		$input = $request->all();
        //print_r($input);
        /*$role['role']     = $request->role;
        $role['add']    = $request->addQue;
        $role['view']   = $request->viewQue;
        $role['edit']   = $request->editQue;
        $role['delete'] = $request->deleteQue;*/
        
        $user       = $this->objUser->addUser($input);
        return $user;     
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
	public function update(Request $request)
	{
		$input 	= $request->input('user');
		$pass 	= $this->objUser->getPassword($input['id_user']);

    	if(\Hash::check($input['oldPassword'], $pass))
    	{
    		$changePass = $this->objUser->changePassword($input['newPassword'],$input['id_user']);
    		if($changePass){
    			return Response::json(array('status' => true, 'message' => "Password Changed successfully"), 200);
    		}
    		else{
    			return Response::json(array('status' => false, 'message' => "Unable to Reset password. Try later "), 200);
    		}
    	}else{
    		return Response::json(array('status' => 'failed', 'message' => "Your old password does not match"), 200);
    	}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		$input 		= $request->input('user');
		$deavtivate = $this->objUser->deactivateUser($input['id_user']);
		if($deavtivate)
			return Response::json(array('status'=>true, 'message'=>"User deactivated"), 200);
		else
			return Response::json(array('status'=>false, 'message'=>"Unable to deactivate user. Try again later"), 200);
	}
	public function editUserDetails(Request $request)
	{
		$input 		= $request->input('user');
		$editUser 	= $this->objUser->editUser($input);
		if($editUser)
			return Response::json(array('status' => $editUser, 'message' => "User edited successfully"), 200);
		else
			return Response::json(array('status' =>$editUserer, 'message' => "no changes done"), 200);
	}
	public function getVersionDetails()
	{
		$versionDetails=$this->objUser->getVersionDetails();
		if($versionDetails)
		{
			return Response::json($versionDetails,200); 
		}
	}
	public function getckupload()
	{
		echo "heloooo";
	}

}
