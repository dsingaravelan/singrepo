<?php

namespace App\Http\Controllers;

use App\models\Role;
use ProfileController;
use Illuminate\Http\Request;
use \Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RoleController extends Controller
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
        
        $this->objRole  = new Role();
    }
    public function index(Request $request)
    {         
        //$getRoles      = $this->objRole->getRole();
        //$getModule     = $this->objRole->getModule();
        $roleId='';
        $getUserRole     = $this->objRole->getUserRole($roleId);
        // print_r($getUserRole );
        // exit();
        // return view('role.role',compact('getRoles')); // working
        if ($getUserRole)
            return Response::json(array('roles' => $getUserRole, 'message' => 'Result Found'), 200);
        else
            return Response::json(array('roles' => array(), 'message' => 'No Records Found'), 200);            
        
    }
    public function create(Request $request)
    {
        $cookie = $request->cookie('id_user');
        if($cookie=='')
        {
            return \Redirect::to('/');
        }
        else
        {
            $input = $request->all();
            /*$role['role']     = $request->role;
            $role['add']    = $request->addQue;
            $role['view']   = $request->viewQue;
            $role['edit']   = $request->editQue;
            $role['delete'] = $request->deleteQue;*/
            
            $roleModelObj   = new Role();
            $op             = $roleModelObj->createRole($input);
            return $op;
        }
    } 
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        
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
    /**
     * Get the roles list.
     *
     * @param  
     * @return Response
     */
    public function getRoles()
    {       
        $getRoles     = $this->objRole->getRole();        
        if ($getRoles)
            return Response::json(array('roles' => $getRoles, 'message' => 'Result Found'), 200);
        else
            return Response::json(array('roles' => array(), 'message' => 'No Records Found'), 402);            
    } 

    public function getModules()
    {
        $getRoleModules     = $this->objRole->getModule();
        if ($getRoleModules)
            return Response::json(array('modules' => $getRoleModules, 'message' => 'Result Found'), 200);
        else
            return Response::json(array('modules' => array(), 'message' => 'No Records Found'), 402);            
    }
    public function addRole(Request $request)
    {
        $input           = $request->input('role');
        $check_duplicate = $this->objRole->checkDuplicateRole($input['name']);
        if(isset($check_duplicate) && !empty($check_duplicate))
        {
            return Response::json(array('status'=>false, 'message' => 'Role already exists'), 200);            
        }
        else
        {
            $insertRoles = $this->objRole->addRoleModule($input);
            if($insertRoles)
                return Response::json(array('status'=>true, 'message' => 'Role added successfully'), 200);
            else
                return Response::json(array('status'=>false, 'message' => 'Unable to create role.'), 402);
        }
    }
    public function getAllModuleForCheckRole()
    {
        return $this->objRole->getAllModuleForCheckRole();
    }

}
