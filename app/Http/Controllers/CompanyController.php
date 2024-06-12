<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CompanyController extends Controller
{
    public function Companies(){
        $companies=Company::paginate(20);

        return view('companies.index',compact('companies'));
    }


    public function AddCompany(Request $request){
        $company=new Company();
        $company->name=$request->name;
        $company->shopify_id=$request->shopify_id;
        $company->save();
        return Redirect::tokenRedirect('companies', ['notice' => 'Company Added Successfully']);

    }

    public function EditCompany(Request $request,$id){


        $company=Company::find($id);
        if($company) {
            $company->name=$request->name;
            $company->shopify_id=$request->shopify_id;
            $company->save();
        }
        return Redirect::tokenRedirect('companies', ['notice' => 'Company Updated Successfully']);
    }


    public function DeleteCompany($id){
        $company=Company::find($id);
        if($company){
            $company->delete();
            return Redirect::tokenRedirect('companies', ['notice' => 'Company Deleted Successfully']);
        }
    }
}
