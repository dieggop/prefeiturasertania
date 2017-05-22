<?php

namespace App\Http\Controllers;

use App\Categorias;
use App\Noticias;
use Illuminate\Support\Facades\Session;
use \Validator;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{

    public function __construct() {
        $this->middleware('admin.user');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $noticias = Noticias::orderBy('id','desc')->paginate(20);

        return view("admin.noticias-listar", compact("noticias"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categorias = Categorias::all()->pluck('name','id');


        return view('admin.noticias-cad', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

//        dd($request->all());

        $fotoprincipal = $request->file("photo");

        $regras = [
            'title' => 'required|max:144|min:15',
            'texto' => 'required',
            'sobre' => 'required|max:255|min:50',

        ];
        $messages = [
            'required' => 'O campo :attribute é obrigatório.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
        ];

        $validator = Validator::make($request->all(),$regras,$messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        };


        if (!empty($fotoprincipal)) {
            if ($fotoprincipal->isValid()) {

//            $file->move($destinationPath,$file->getClientOriginalName());
                $foto = $fotoprincipal->store('photo');

            } else {
                return redirect()->back()->withErrors(["fotoprincipal"=>"Não é uma foto válida"]);
            }
        }



        $noticia = new Noticias;
        $noticia->fill($request->except(['photo']));
        if (!$request->has("ativo")) $noticia->ativo = false;

        $noticia->categoria_id = $request->input('categoria');
        $noticia->photo = $foto;
        $noticia->save();

        Session::flash('sucesso', 'Notícia cadastrada com sucesso');

        return redirect()->to(route("noticia-lista"));





    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $noticia = Noticias::find($id);
        $categorias = Categorias::all()->pluck('name','id');


        if ($noticia == null) {
            return redirect(route("noticia-lista"))->withErrors("Notícia não existente");
        }

        return view("admin.noticias-ed",compact("noticia"),compact("categorias"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {



        $noticia = Noticias::find($id);

        if ($noticia == null) {
            return redirect(route("noticia-lista"))->withErrors("Notícia não existente");


        }

        $fotoprincipal = $request->file("photo");

        $regras = [
            'title' => 'required|max:144|min:15',
            'texto' => 'required',
            'sobre' => 'required|max:255|min:50',

        ];
        $messages = [
            'required' => 'O campo :attribute é obrigatório.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
        ];

        $validator = Validator::make($request->all(),$regras,$messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        };

        $noticia->fill($request->except(['photo']));

        if (!empty($fotoprincipal)) {
            if ($fotoprincipal->isValid()) {

//            $file->move($destinationPath,$file->getClientOriginalName());
                $foto = $fotoprincipal->store('photo');
                $noticia->photo = $foto;

            } else {
                return redirect()->back()->withErrors(["fotoprincipal"=>"Não é uma foto válida"]);
            }
        }

        if (!$request->has("ativo")) $noticia->ativo = false;

        $noticia->categoria_id = $request->input('categoria');
        $noticia->save();

        Session::flash('sucesso', 'Notícia atualizada com sucesso');

        return redirect()->to(route("noticia-lista"));



    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $noticia = Noticias::find($id);

        if ($noticia == null) {
            return redirect(route("noticia-lista"))->withErrors("Notícia não existente");

        }

        $r = $noticia->delete();

        if ($r) {
            Session::flash('sucesso', 'Notícia excluída com sucesso');

            return redirect()->to(route("noticia-lista"));

        } else {
            return redirect(route("noticia-lista"))->withErrors("Notícia não excluída por algum motivo");


        }


    }


    /**
     * Display for seach.
     *
     * @param  formParamg
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        $noticias = Noticias::where('title','like','%'. $request->busca .'%' )->orderBy('id','desc')->paginate(20);

        if (count($noticias) == 0) {
            return redirect()->to(route("noticia-lista"))->withErrors("Nenhum registro encontrado");

        }

        return view("admin.noticias-listar", compact("noticias"));

    }


    public function uploadImagemCorpo(Request $request) {

        $file = $request->file('file');
        dd($file);
        /*//Display File Name
        echo 'File Name: '.$file->getClientOriginalName();
        echo '<br>';

        //Display File Extension
        echo 'File Extension: '.$file->getClientOriginalExtension();
        echo '<br>';

        //Display File Real Path
        echo 'File Real Path: '.$file->getRealPath();
        echo '<br>';

        //Display File Size
        echo 'File Size: '.$file->getSize();
        echo '<br>';

        //Display File Mime Type
        echo 'File Mime Type: '.$file->getMimeType();*/


//        if ($file->isValid()) {

//            $file->move($destinationPath,$file->getClientOriginalName());
            $foto = $file->store('images');
            return response()
                ->json(['photo' => $foto]);
//        }

        //Move Uploaded File
//        $destinationPath = 'uploads';

    }

}
