<?php

declare(strict_types=1);

namespace App\Controller;
use App\Model\Categoria;
use App\Model\Curso;
use App\Repository\CategoriaRepository;
use App\Repository\CursoRepository;
use Dompdf\Dompdf;
use Exception;

class CursoController extends AbstractController
{
    private CursoRepository $repository;

    public function __construct()
    {
        $this->repository = new CursoRepository();
    }
    
    public function listar(): void
    {
        $cursos = $this->repository->buscarTodos();

        $this->render('cursos/listar', [
            'cursos' => $cursos,
        ]);
    }

    public function cadastrar() : void
    {
        if (true === empty($_POST)) {
            $this->categoriaRepository = new CategoriaRepository;
            $this->render('cursos/cadastrar', [
                'categorias' => $this->categoriaRepository->buscarTodos()
        ]);
            return;
        }

        $curso = new Curso;
        $curso->nome = $_POST['nome'];
        $curso->cargaHoraria = $_POST['cargaHoraria'];
        $curso->descricao = $_POST['descricao'];
        $curso->categoria_id = intval($_POST['categoria']);

        // $rep = new CursoRepository();

        try{
            $this->repository->inserir($curso);
        } catch(Exception $exception){
            if(str_contains($exception->getMessage(), 'nome')){
                die('O curso já existe');
            }

            var_dump($curso);
         
            die('Vish, aconteceu um erro');
        }
        $this->redirect('/cursos/listar');
    }
    public function editar(): void
    {
        $id = $_GET['id'];
        $rep = new CursoRepository();
        $curso = $rep->buscarUm($id);
        $this->categoriaRepository = new CategoriaRepository;
        $this->render('cursos/editar', [
            $curso,
            'categorias' => $this->categoriaRepository->buscarTodos()
        ]); 
        if (false === empty($_POST)) {
            $curso->nome = $_POST['nome'];
            $curso->cargaHoraria = $_POST['cargaHoraria'];
            $curso->categoria_id = intval($_POST['categoria']);
    
            try {
                $rep->atualizar($curso, $id);
            } catch (Exception $exception) {
                if (true === str_contains($exception->getMessage(), 'nome')) {
                    die('Nome ja existe');
                }
    
    
                die('Vish, aconteceu um erro');
            }
            $this->redirect('/cursos/listar');
        }
    }

    public function excluir(): void
    {
        // $this->render('aluno/excluir');
        $id = $_GET['id'];
        $rep = new CursoRepository();
        $rep->excluir($id);
        $this->redirect('/cursos/listar');

    }

    private function redirecionar(iterable $cursos){
        $resultado = '';
        foreach ($cursos as $curso) {
        $resultado .= "
            <tr>
            <td>{$curso->id}</td>
            <td>{$curso->nome}</td>
            <td>{$curso->categoria}</td>
            <td>{$curso->cargaHoraria}</td>

            </tr>";
            }
            return $resultado;
    }

    public function relatorio(): void
    {
        $hoje = date('d/m/Y');
        $curso = $this->repository->buscarTodos();
        $design = "
        <h1>Relatorio de Cursos</h1>
        <hr>
        <em>Gerando em {$hoje}</em>
        <hr>
        <table border='1' width='100%' style='margin-top: 30px;'>
            <thead>
                <tr>
                <th>#ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Carga Horaria</th>
                </tr>
            </thead>
            <tbody>
            ".$this->redirecionar($curso)."
            </tbody>
        </table>
        ";

        $dompdf = new Dompdf();
        $dompdf->loadHtml(($design)); 
        $dompdf->setPaper('A4', 'portrait'); 
        $dompdf->render();
        $dompdf->stream('Relatorio-Cursos.pdf', ['Attachment' => 0]); 
    }
}